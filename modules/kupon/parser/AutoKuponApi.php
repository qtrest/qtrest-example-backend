<?php

/*
 * Сайт говёный и сложный. Функционал реализован лишь частично.
 */


namespace app\modules\kupon\parser;

use SleepingOwl\Apist\Apist;
use app\components\Tools;
use yii\db\Query;

class AutoKuponApi extends BaseApi
{

    public function getBaseUrl()
    {
        return 'http://autokupon.kz';
    }

    protected function getSourceServiceCode()
    {
        return 'autokuponkz';
    }

    protected function getSourceServiceName()
    {
        return 'AutoKupon.kz';
    }

    protected function getCountryName()
    {
        return 'Казахстан';
    }

    protected function getCountryCode()
    {
        return 'kazakhstan';
    }

    protected function cities()
    {
        //return 0;
        $result = $this->get('/city/astana/?back=%2F', [
            'cities'  => Apist::filter('#city-menu > li > ul > li > a')->each([
                'city' => Apist::current()->text(),
                'link' => Apist::current()->attr('href')->call(function ($href)
                {
                    if(Tools::startsWith($href, 'http://') || Tools::startsWith($href, 'https://')) {
                        return $href;
                    } else {
                        return $this->getBaseUrl() . $href;
                    }
                }),
                'path' => Apist::current()->attr('href'),
            ]),
        ]);

        $result = Tools::trimArray($result);

        return $result;
    }

    protected function categories()
    {
        //return 0;
        $result = $this->get('/city/astana/?back=%2F', [
            'categories'  => Apist::filter('.companies-categories-list li a')->each([
                'categoryName' => Apist::current()->text(),
                'categoryId' => Apist::current()->attr('href')->call(function ($href)
                {
                    $catId = str_replace('/?category=','', $href);
                    return $catId;
                }),
                'parentCategoryId' => '-1',
                'categoryAdditionalInfo' => Apist::current()->attr('href'),
            ]),
        ]);

        $result = Tools::trimArray($result);

        return $result;
    }

    protected function couponsByCityId($cityId)
    {
        //return 0;
        $cityPath = \Yii::$app->db->createCommand('SELECT path FROM cityUrl WHERE cityId=\''.$cityId.'\' AND sourceServiceId=\'' . $this->getSourceServiceId() . '\'')->queryScalar();
        $cityName = \Yii::$app->db->createCommand('SELECT cityName FROM city WHERE id=\''.$cityId.'\'')->queryScalar();
        if (empty($cityPath)) {
            throw new \yii\web\HttpException(400, 'empty cityPath', 405);
            return;
        }
        $cityCode = \Yii::$app->db->createCommand('SELECT cityCode FROM city WHERE id=\''.$cityId.'\'')->queryScalar();

        $query = new Query;
        $categories = $query->select('categoryCode, categoryIdentifier, parentCategoryIdentifier, categoryAdditionalInfo')
            ->from('categories')
            ->where('sourceServiceId=:sourceServiceId
            AND categoryIdentifier <> parentCategoryIdentifier
            AND categoryIdentifier <> \'-1\'',
                [
                    ':sourceServiceId' => $this->getSourceServiceId()
                ]
            )
            ->createCommand()
            ->queryAll();

        $links = [];

        foreach($categories as $key => $value) {
            $links[] = array ($value['categoryAdditionalInfo'], $value['categoryIdentifier']);
        }

        //return $links;

        $allResult = [];
        $allResult['coupons'] = [];

        //там что-то типа кукисов чтоли, но кукисы вроде нигде не выставляются, а город как-то выбирается...
        $this->get($cityPath, []);

        for ($i = 0; $i < count($links); $i++) {
            //echo $links[$i][0] . '<br/>';

            //блин там всё ещё и по страницам разбито((
            $pages = $this->get($links[$i][0], [
                'pages' => array(Apist::filter('#pagination-list li.page a')->each()->attr('href'), $links[$i][1])
            ]);

            $pageLinks = $pages['pages'][0];

            $pagesResult = [];
            $pagesResult['coupons'] = [];
            //Tools::print_array('pages',$pageLinks);

            for ($k = 0; $k < count($pageLinks); $k++) {
                //echo $pageLinks[$k] . '<br/>';
                $result = $this->get($pageLinks[$k], [
                    'city' => Apist::filter('#city-menu > li > a')->text(),
                    'coupons' => Apist::filter('#companies-row .span9 .company-item')->each([
                        'title' => Apist::filter('div.company-item-title > a')->text(),
                        'shortDescription' => Apist::filter('div.company-item-description')->text(),
                        'longDescription' => 'empty',
                        'conditions' => Apist::filter('div.company-item-contacts > p:nth-child(2)')->text(),
                        'features' => Apist::filter('div.company-item-contacts > p:nth-child(1)')->text(),
                        'timeToCompletion' => '-1',
                        'originalCouponPrice' => '0',
                        'originalPrice' => '0',
                        'discountPercent' => Apist::filter('div.company-item-discount')->text()->call(function ($str) {
                            return str_replace('-', '', str_replace('%', '', $str));
                        }),
                        'discountPrice' => '0',
                        'discountType' => 'freeCoupon',
                        'pageLink' => '-1',
                        'boughtCount' => '-1',
                        'sourceServiceCategories' => $links[$i][1],
                        'sourceServiceId' => $this->getSourceServiceId(),
                        'imagesLinks' => 'empty',
                        'mainImageLink' => Apist::filter('div.company-item-in .span2 img')->attr('src'),
                    ]),
                ]);

                if (isset($result['coupons'])) {
                    foreach ($result['coupons'] as $key => $value) {
                        $pagesResult['coupons'][] = $value;
                    }
                }
                if (!isset($allResult['city'])) {
                    $allResult['city'] = $result['city'];
                }
            }
            //echo count($pagesResult['coupons']);

            if (isset($pagesResult['coupons'])) {
                foreach ($pagesResult['coupons'] as $key => $value) {
                    $allResult['coupons'][] = $value;
                }
            }
        }

        $allResult = Tools::trimArray($allResult);

        if (!isset($allResult['city'])) {
            $allResult['city'] = $cityName;
        }

        $allResult['cityCode'] = Tools::ru2lat($allResult['city']);

        return $allResult;
    }

    protected function couponAdvancedById($couponId)
    {
        //not supported
        return 0;
//        $pageLink = \Yii::$app->db->createCommand('SELECT pageLink FROM coupon WHERE id=\''.$couponId.'\'')->queryScalar();
//
//        $result = $this->get($pageLink, [
//            'longDescription' => Apist::filter('#content_container > div.offer_page_body > div.description')->html(),
//            'conditions' => Apist::filter('#content_container > div.offer_page_body > div.terms_body > div.terms')->html(),
//            'features' => Apist::filter('#content_container > div.offer_page_body > div.terms_body > div.features')->html(),
//            'imageLinks' => Apist::filter('#content_container > div.offer_page_header > div.offer_image')->each()->attr('style')->call(function($str) {
//                return str_replace('background-image:url(\'','',str_replace('\');','',$str));
//            }),
//            'timeToCompletion' => Apist::current()->call(function(){
//                $dates=explode("-", date("m-d-Y"));
//                $then=mktime (0,0,0,$dates[0],$dates[1]+1,$dates[2]);
//                $now=time();
//                $how=$then-$now - 21600; //TODO gmt!!
//                return $how;
//            }),
//            'boughtCount' => Apist::filter('#content_container > div.offer_page_header > div.sales > b')->text(),
//        ]);
//
//        if (!isset($result['boughtCount']) || $result['boughtCount'] == '') {
//            $result['boughtCount'] = 0;
//        }
//
//        if (empty($result['longDescription']) && empty($result['timeToCompletion'])) {
//            //TODO SpecialPage!!!
//            return 0;
//        }
//        return $result;
    }

}