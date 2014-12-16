<?php

namespace app\modules\kupon\parser;

use SleepingOwl\Apist\Apist;
use app\components\Tools;
use yii\db\Query;

class MirKuponovApi extends BaseApi
{

    public function getBaseUrl()
    {
        return 'http://mirkuponov.kz';
    }

    protected function getSourceServiceCode()
    {
        return 'mirkuponovkz';
    }

    protected function getSourceServiceName()
    {
        return 'MirKuponov.kz';
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
        $result = $this->get('/', [
            'cities'  => Apist::filter('div.city > a')->each([
                'city' => Apist::current()->text(),
                'link' => Apist::current()->attr('href')->call(function ($href)
                {
                    return $this->getBaseUrl() . $href;
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
        $result = $this->get('/', [
            'categories'  => Apist::filter('#category_menu > div.menu a')->each([
                'categoryName' => Apist::current()->text(),
                'categoryId' => Apist::current()->attr('href')->call(function ($href)
                {
                    $catName = Tools::getLastWord($href, '/');
                    if(trim($catName) == '') {
                        return 'none';
                    } else {
                        return $catName;
                    }
                }),
                'parentCategoryId' => '-1',
                'categoryAdditionalInfo' => Apist::current()->attr('href')->call(function ($href)
                {
                    return str_replace('/almaty/', '/-city-/', $href);
                }),
            ]),
        ]);

        $result = Tools::trimArray($result);

        return $result;
    }

    protected function couponsByCityId($cityId)
    {
        $cityPath = \Yii::$app->db->createCommand('SELECT path FROM cityUrl WHERE cityId=\''.$cityId.'\' AND sourceServiceId=\'' . $this->getSourceServiceId() . '\'')->queryScalar();
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
            AND categoryIdentifier <> \'-1\'
            AND categoryIdentifier NOT IN (\'none\')',
                [
                    ':sourceServiceId' => $this->getSourceServiceId()
                ]
            )
            ->createCommand()
            ->queryAll();

        $links = [];

        foreach($categories as $key => $value) {
            $links[] = array (str_replace('-city-',$cityCode,$value['categoryAdditionalInfo']), $value['categoryIdentifier']);
        }

        //return $links;
        //TODO all categories links!!!

        $allResult = [];
        $allResult['coupons'] = [];

        for ($i = 0; $i < count($links); $i++) {
            $result = $this->get($links[$i][0], [
                'city' => Apist::filter('div.city > a')->text(),
                'coupons' => Apist::filter('#content_container a.catalog_offer')->each([
                    'title' => Apist::filter('span.offer_frame > span.title > span')->text(),
                    'shortDescription' => Apist::filter('span.offer_frame > span.title > span')->text(),
                    'longDescription' => 'empty',
                    'conditions' => 'empty',
                    'features' => 'empty',
                    'timeToCompletion' => 'empty',
                    'originalCouponPrice' => Apist::filter('span.offer_frame > span.price')->text()->call(function($str) {
                        return str_replace('тг.','',$str);
                    }),
                    'originalPrice' => Apist::filter('span.offer_frame > span.price_before')->text()->call(function($str) {
                        return str_replace('от','',str_replace('тг.','',$str));
                    }),
                    'discountPercent' => Apist::filter('span.offer_frame > span.discount')->text()->call(function($str) {
                        return str_replace('-','',str_replace('до','',str_replace('%','',$str)));
                    }),
                    'discountPrice' => Apist::filter('span.offer_frame > span.price_after')->text()->call(function($str) {
                        return str_replace('от','',str_replace('тг.','',$str));
                    }),
                    'pageLink' => Apist::current()->attr('href')->call(function($str) {
                        return explode(';jsessionid', $str)[0];
                    }),
                    'boughtCount' => Apist::filter('span.offer_frame > span.sales')->text()->call(function($str) {
                        return str_replace('уже купили','',$str);
                    }),
                    'sourceServiceCategories' => $links[$i][1],
                    'sourceServiceId' => $this->getSourceServiceId(),
                    'imagesLinks' => 'empty',
                    'mainImageLink' => Apist::filter('span.offer_image')->attr('style')->call(function($str) {
                        return str_replace('background-image:url(\'','',str_replace('\');','',$str));
                    }),
                ]),
            ]);

            $result = Tools::trimArray($result);

            if (isset($result['coupons'])) {
                for ($j = 0; $j < count($result['coupons']); $j++) {
                    $originalCouponPrice = $result['coupons'][$j]['originalCouponPrice'];

                    //пытаемся понять что у нас за тип - купион или сертификат
                    $discountType = 'undefined';

                    //TODO condition for discountType 'full' is not found!!!
                    if ($originalCouponPrice == '0') {
                        $discountType = 'freeCoupon';
                    } else {
                        $discountType = 'coupon';
                    }

                    if ($result['coupons'][$j]['boughtCount'] == '') {
                        $result['coupons'][$j]['boughtCount'] = 0;
                    }

                    $result['coupons'][$j]['title'] = Tools::getThreeFirstWords($result['coupons'][$j]['title']);

                    $result['coupons'][$j]['discountType'] = $discountType;
                }

                $result['cityCode'] = Tools::ru2lat($result['city']);

                foreach ($result['coupons'] as $key => $value) {
                    $allResult['coupons'][] = $value;
                }
            }
        }

        $allResult['city'] = $result['city'];
        $allResult['cityCode'] = Tools::ru2lat($result['city']);

        return $allResult;
    }

    protected function couponAdvancedById($couponId)
    {
        //return 0;
        $pageLink = \Yii::$app->db->createCommand('SELECT pageLink FROM coupon WHERE id=\''.$couponId.'\'')->queryScalar();

        $result = $this->get($pageLink, [
            'longDescription' => Apist::filter('#content_container > div.offer_page_body > div.description')->html(),
            'conditions' => Apist::filter('#content_container > div.offer_page_body > div.terms_body > div.terms')->html(),
            'features' => Apist::filter('#content_container > div.offer_page_body > div.terms_body > div.features')->html(),
            'imageLinks' => Apist::filter('#content_container > div.offer_page_header > div.offer_image')->each()->attr('style')->call(function($str) {
                return str_replace('background-image:url(\'','',str_replace('\');','',$str));
            }),
            'timeToCompletion' => Apist::current()->call(function(){
                $dates=explode("-", date("m-d-Y"));
                $then=mktime (0,0,0,$dates[0],$dates[1]+1,$dates[2]);
                $now=time();
                $how=$then-$now - 21600; //TODO gmt!!
                return $how;
            }),
            'boughtCount' => Apist::filter('#content_container > div.offer_page_header > div.sales > b')->text(),
        ]);

        if (!isset($result['boughtCount']) || $result['boughtCount'] == '') {
            $result['boughtCount'] = 0;
        }

        if (empty($result['longDescription']) && empty($result['timeToCompletion'])) {
            //TODO SpecialPage!!!
            return 0;
        }
        return $result;
    }

}