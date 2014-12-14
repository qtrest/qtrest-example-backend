<?php

namespace app\modules\kupon\parser;

use SleepingOwl\Apist\Apist;
use app\components\Tools;
use yii\db\Query;

class KupiKuponApi extends BaseApi
{

    public function getBaseUrl()
    {
        return 'http://www.kupikupon.kz';
    }

    protected function getSourceServiceCode()
    {
        return 'kupikuponkz';
    }

    protected function getSourceServiceName()
    {
        return 'KupiKupon.kz';
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
            'cities'  => Apist::filter('#id_selector > div > ul > li a')->each([
                'city' => Apist::current()->text(),
                'link' => Apist::current()->attr('href'),
                'path' => Apist::current()->attr('href')->call(function ($href)
                {
                    return parse_url($href)['path'];
                }),
            ]),
        ]);

        $result = Tools::trimArray($result);

        return $result;
    }

    protected function categories()
    {
        $result = $this->get('/almaty/all', [
            'categories'  => Apist::filter('div.w-deals-menu > div > ul > li > a')->each([
                'categoryName' => Apist::current()->text(),
                'categoryId' => Apist::current()->attr('href')->call(function ($href)
                {
                    return str_replace('/almaty/', '', $href);
                }),
                'parentCategoryId' => '-1',
                'categoryAdditionalInfo' => Apist::current()->attr('href')->call(function ($href)
                {
                    return str_replace('/almaty/', '', $href);
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

        $query = new Query;
        $categories = $query->select('categoryCode, categoryIdentifier, parentCategoryIdentifier, categoryAdditionalInfo')
            ->from('categories')
            ->where('sourceServiceId=:sourceServiceId
            AND categoryIdentifier <> parentCategoryIdentifier
            AND categoryIdentifier <> \'-1\'
            AND categoryIdentifier NOT IN (\'last-deals\', \'all\')',
                [
                    ':sourceServiceId' => $this->getSourceServiceId()
                ]
            )
            ->createCommand()
            ->queryAll();

        $links = [];

        foreach($categories as $key => $value) {
            $links[] = array ($cityPath . '/' . $value['categoryAdditionalInfo'], $value['categoryIdentifier']);
        }

        //return $links;
        //TODO all categories links!!!

        $allResult = [];
        $allResult['coupons'] = [];

        for ($i = 0; $i < count($links); $i++) {
            $result = $this->get($links[$i][0], [
                'city' => Apist::filter('#city-s')->text(),
                'coupons' => Apist::filter('div.b-buy > div.w-deal_box')->each([
                    'title' => Apist::filter('a.deal-link > div.deal-description')->text(),
                    'shortDescription' => Apist::filter('a.deal-link > div.deal-description')->text(),
                    'longDescription' => 'empty',
                    'conditions' => 'empty',
                    'features' => 'empty',
                    'timeToCompletion' => 'empty',
                    'originalCouponPrice' => Apist::filter('a.deal-link > div.deal-price-block > div.cost > span')->text(),
                    'originalPrice' => '0',
                    'discountPercent' => Apist::filter('a.deal-link > div.deal-img > div > div.deal-discount')->text()->call(function($str) {
                        return str_replace('до','',str_replace('%','',$str));
                    }),
                    'discountPrice' => '0',
                    'pageLink' => Apist::filter('a.deal-link')->attr('href'),
                    'boughtCount' => Apist::filter('a.deal-link .already_buy span.count')->text(),
                    'sourceServiceCategories' => $links[$i][1],
                    'sourceServiceId' => $this->getSourceServiceId(),
                    'imagesLinks' => 'empty',
                    'mainImageLink' => Apist::filter('a.deal-link img.deal-pic')->attr('src')->call(function($href){
                        return parse_url($href)['path'];
                    }),
                ]),
            ]);

            $result = Tools::trimArray($result);

            if (isset($result['coupons'])) {
                for ($j = 0; $j < count($result['coupons']); $j++) {
                    $originalCouponPrice = $result['coupons'][$j]['originalCouponPrice'];

                    //пытаемся понять что у нас за тип - купион или сертификат
                    $discountType = 'undefined';
                    $originalPrice = 0;
                    $discountPrice = 0;

                    if (intval($originalCouponPrice) < 1000) {
                        if (intval($originalCouponPrice) > 0) {
                            $discountType = 'coupon';
                        } else {
                            $discountType = 'freeCoupon';
                        }
                    } else {
                        $discountType = 'full';
                        $originalPrice = $originalCouponPrice / ((100 - intval($result['coupons'][$j]['discountPercent'])) / 100);
                        $discountPrice = $originalCouponPrice;
                    }

                    if ($result['coupons'][$j]['boughtCount'] == '') {
                        $result['coupons'][$j]['boughtCount'] = 0;
                    }

                    $result['coupons'][$j]['title'] = Tools::getThreeFirstWords($result['coupons'][$j]['title']);

                    $result['coupons'][$j]['discountType'] = $discountType;
                    $result['coupons'][$j]['originalPrice'] = $originalPrice;
                    $result['coupons'][$j]['discountPrice'] = $discountPrice;
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
            'longDescription' => Apist::filter('div.deal_cat_title p')->text(),
            'conditions' => 'empty',
            'features' => Apist::filter('#deal_body_blocks')->html(),
            'imageLinks' => Apist::filter('.deal_logo_wrapper img')->each()->attr('src'),
            'timeToCompletion' => Apist::current()->call(function(){
                $dates=explode("-", date("m-d-Y"));
                $then=mktime (0,0,0,$dates[0],$dates[1]+1,$dates[2]);
                $now=time();
                $how=$then-$now - 21600; //TODO gmt!!
                return $how;
            }),
            'boughtCount' => Apist::filter('.already_buy span.count')->text(),
        ]);

        if ($result['boughtCount'] == '') {
            $result['boughtCount'] = 0;
        }

        if (empty($result['longDescription']) && empty($result['timeToCompletion'])) {
            //TODO SpecialPage!!!
            return 0;
        }
        return $result;
    }

}