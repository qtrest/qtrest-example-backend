<?php

namespace app\modules\kupon\parser;

use SleepingOwl\Apist\Apist;
use app\components\Tools;
use yii\db\Query;

class BlizzardApi extends BaseApi
{

    public static $lastParentCategoryId = -1;

    public function getBaseUrl()
    {
        return 'https://blizzard.kz';
    }

    protected function getSourceServiceCode()
    {
        return 'blizzard';
    }

    protected function getSourceServiceName()
    {
        return 'Blizzard.kz';
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
        $result = $this->get('/', [
            'cities'  => Apist::filter('.city-bar li a')->each([
                'city' => Apist::current()->text(),
                'link' => Apist::current()->attr('onclick')->call(function ($href)
                {
                    $tmpStr = Tools::getTextInBrackets($href);
                    $tmpArr = explode(', ',$tmpStr);
                    $path = '/town/' . str_replace('\'','', $tmpArr[2]);

                    return $this->getBaseUrl() . $path;
                }),
                'path' => Apist::current()->attr('onclick')->call(function ($href)
                {
                    $tmpStr = Tools::getTextInBrackets($href);
                    $tmpArr = explode(', ',$tmpStr);
                    $path = '/town/' . str_replace('\'','', $tmpArr[2]);

                    return $path;
                }),
            ]),
        ]);

        $result = Tools::trimArray($result);

        return $result;
    }

    protected function categories()
    {
        $result = $this->get('/', [
            'categories'  => Apist::filter('#main > div.content > div.content_head > div.rubrics2 > ul:nth-child(1) li, #main > div.content > div.content_head > div.rubrics2 > ul:nth-child(1) li a.podc_aa')->each([
                'categoryName' => Apist::current()->call(function ($current)
                    {
                        $id = $current->attr('id');
                        return $current->filter('a')->text();
                    }),
                'categoryId' => Apist::current()->call(function ($current)
                {

                    $categoryId = str_replace('categ', '', $current->attr('id'));

                    if ($categoryId > '') {
                        BlizzardApi::$lastParentCategoryId = $categoryId;
                        return $categoryId;
                    } else {
                        $categoryId = end(explode('/', $current->attr('href')));

                        if ($categoryId > '') {
                            return end(explode('-', $categoryId));
                        } else {
                            return -1;
                        }
                    }
                }),
                'parentCategoryId' => Apist::current()->call(function ($current)
                {
                    $categoryId = str_replace('categ', '', $current->attr('id'));

                    if ($categoryId > '') {
                        BlizzardApi::$lastParentCategoryId = $categoryId;
                        return $categoryId;
                    } else {
                        $categoryId = end(explode('/', $current->attr('href')));

                        if ($categoryId > '') {
                            return current(explode('-', $categoryId));
                        } else {
                            return BlizzardApi::$lastParentCategoryId;
                        }
                    }
                }),
                'categoryAdditionalInfo' => Apist::current()->call(function ($current)
                {
                    $href = $current->attr('href');
                    if ($href > '') {
                        $startShares = strpos($href, '/shares/');
                        return substr($href, $startShares, strlen($href) - $startShares);
                    } else {
                        return '0';
                    }
                }),
            ]),
        ]);

        $result = Tools::trimArray($result);
        $result = Tools::removeLastWordArray($result);

        return $result;
    }

    protected function couponsByCityId($cityId)
    {
        $cityPath = \Yii::$app->db->createCommand('SELECT path FROM cityUrl WHERE cityId=\''.$cityId.'\' AND sourceServiceId=\'' . $this->getSourceServiceId() . '\'')->queryScalar();
        if (empty($cityPath)) {
            throw new \yii\web\HttpException(400, 'empty cityPath', 405);
            return;
        }

        //only services (Пока берём только услуги! С товарами там полная неразбериха...)
        $query = new Query;
        $categories = $query->select('categoryCode, categoryIdentifier, parentCategoryIdentifier, categoryAdditionalInfo')
            ->from('categories')
            ->where('sourceServiceId=:sourceServiceId AND categoryIdentifier <> parentCategoryIdentifier AND categoryIdentifier <> \'-1\' ',
                [
                    ':sourceServiceId' => $this->getSourceServiceId()
                ]
            )
            ->createCommand()
            ->queryAll();

        $links = [];

        foreach($categories as $key => $value) {
            $links[] = array ($cityPath . $value['categoryAdditionalInfo'], $value['categoryIdentifier']);
        }

        //TODO all categories links!!!

        $allResult = [];
        $allResult['coupons'] = [];

        for ($i = 0; $i < count($links); $i++) {
            $result = $this->get($links[$i][0], [
                'city' => Apist::filter('#main > div.header > div.top_panel > ul > li:nth-child(1) > a')->text(),
                //'cityLat'  => Tools::ru2lat(Apist::filter('#js-b-header > div.b-logo__city__wrapper > div.b-city__change > a > span')->text()->mb_substr(0, -1)),
                'coupons' => Apist::filter('.page .akc')->each([
                    'title' => Apist::filter('.akc_name > a')->text()->call(function ($str) {
                        return Tools::getFirstSentence($str);
                    }),
                    'shortDescription' => Apist::filter('.akc_name > a')->text(),
                    'longDescription' => 'empty',
                    'conditions' => 'empty',
                    'features' => 'empty',
                    'timeToCompletion' => 'empty',
                    'originalCouponPrice' => Apist::filter('div.akc_diskount > div.zakl')->text()->call(function ($str) {
                        return str_replace('тг.', '', $str);
                    }),
                    'originalPrice' => Apist::filter('div.akc_diskount > ul > li.prices > font')->text(),
                    'discountPercent' => Apist::filter('div.akc_diskount > ul > li.discount > div > font')->text(),
                    'discountPrice' => Apist::filter('div.akc_diskount > ul > li.summ > font')->text(),
                    'pageLink' => Apist::filter('.akc_name > a')->attr('href')->call(function ($href) {
                        return $href;
                    }),
                    'boughtCount' => Apist::filter('div.akc_info > div > div.kupon_count > font')->text()->call(function ($str) {
                        return str_replace('человек', '', $str);
                    }),
                    'sourceServiceCategories' => $links[$i][1],
                    'sourceServiceId' => $this->getSourceServiceId(),
                    'imagesLinks' => 'empty',
                    'mainImageLink' => Apist::filter('div.akc_t > div.akc_img > img')->attr('data-original'),
                ]),
            ]);

            $result = Tools::trimArray($result);

            if (isset($result['coupons'])) {
                for ($j = 0; $j < count($result['coupons']); $j++) {
                    $originalCouponPrice = $result['coupons'][$j]['originalCouponPrice'];
                    $originalPrice = $result['coupons'][$j]['originalPrice'];
                    $discountPrice = $result['coupons'][$j]['discountPrice'];
                    $discountPercent = $result['coupons'][$j]['discountPercent'];

                    $originalRemain = $originalPrice - ($originalPrice * (intval($discountPercent) / 100));

                    $type = 'undefined';
                    if ($originalCouponPrice == '0') {
                        $type = 'freeCoupon';
                    } else if ($originalRemain == $originalCouponPrice) {
                        $type = 'full';
                    } else {
                        $type = 'coupon';
                    }

                    $result['coupons'][$j]['discountType'] = $type;
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
        $pageLink = \Yii::$app->db->createCommand('SELECT pageLink FROM coupon WHERE id=\''.$couponId.'\'')->queryScalar();

        $result = $this->get($pageLink, [
            'longDescription' => Apist::filter('#con_tab2 > p:nth-child(1)')->text(),
            'conditions' => Apist::filter('#con_tab1 > div.usl_osb > div.tab_usl')->html(),
            'features' => Apist::filter('#con_tab1 > div.usl_osb > div.tab_osoben')->html(),
            'imageLinks' => Apist::filter('#new_slider > img')->each()->attr('src'),
            'timeToCompletion' => Apist::current()->call(function(){
                $dates=explode("-", date("m-d-Y"));
                $then=mktime (0,0,0,$dates[0],$dates[1]+1,$dates[2]);
                $now=time();
                $how=$then-$now - 21600; //TODO gmt!!
                return $how;
            }),
            'boughtCount' => Apist::filter('div.kupon_peop span')->text(),
        ]);

        if (empty($result['longDescription']) && empty($result['timeToCompletion'])) {
            //TODO SpecialPage!!!
            return 0;
        }
        return $result;
    }
}