<?php

namespace app\modules\kupon\parser;

use SleepingOwl\Apist\Apist;
use app\components\Tools;
use yii\db\Query;
use yii\db\QueryBuilder;

class BeSmartApi extends BaseApi
{

    public function getBaseUrl()
    {
        return 'https://besmart.kz/';
    }

    protected function getSourceServiceCode()
    {
        return 'besmartkz';
    }

    protected function getSourceServiceName()
    {
        return 'BeSmart.kz';
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
            'cities'  => Apist::filter('div.search-cities > div.bottom-countries.kz > ul li a')->each([
                'city' => Apist::current()->text()->call(function ($href)
                {
                    return trim($href);
                }),
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
        ],  ['proxy' => \Yii::$app->params['proxy']]);

        $result = Tools::trimArray($result);

        return $result;
    }

    protected function categories()
    {
        $result = $this->get('/', [
            'categories'  => Apist::filter('#topCategoryMenu > ul > li a')->each([
                'categoryName' => Apist::current()->text()->call(function ($text)
                {
                    return Tools::removeLastDigits(Tools::trim_all($text));
                }),
                'categoryId' => Apist::current()->attr('href')->call(function ($href)
                {
                    $href = Tools::endsWithCut($href, "/");
                    $href = Tools::startsWithCut($href, "/");
                    //return $href;
                    if ( substr_count($href, "/") > 0 ) {
                        return explode("/", $href)[1];
                    } else {
                        return $href;
                    }
                }),
                'parentCategoryId' => Apist::current()->attr('href')->call(function ($href)
                {
                    $href = Tools::endsWithCut($href, "/");
                    $href = Tools::startsWithCut($href, "/");
                    //return $href;
                    if ( substr_count($href, "/") > 0 ) {
                        return explode("/", $href)[0];
                    } else {
                        return "-1";
                    }
                }),
                'categoryAdditionalInfo' => Apist::current()->attr('href'),
            ]),
        ],  ['proxy' => \Yii::$app->params['proxy']]);

        $result = Tools::trimArray($result);
        //used before 03/12/2015
        //$result = Tools::removeLastWordArray($result);
        
        $result = Tools::trimArray($result);

        $resultCleared = array();
        $resultCleared['categories'] = array();

        //clear empty categories
        for($i = 0; $i < count($result['categories']); $i++) {
            if (!empty(trim($result['categories'][$i]['categoryName']))) {
                //if ( !in_array($result['categories'][$i]['categoryId'], ["feedback", "how-it-work", "how_pay_order", "delivery", "contact", "about", "feedback", "bonusclub"]) ) {
                    $resultCleared['categories'][] = $result['categories'][$i];
                //}
            }
        }

        return $resultCleared;
    }

    protected function couponsByCityId($cityId)
    {
        $cityPath = \Yii::$app->db->createCommand('SELECT path FROM cityUrl WHERE cityId=\''.$cityId.'\' AND sourceServiceId=\'' . $this->getSourceServiceId() . '\'')->queryScalar();
        if (empty($cityPath)) {
            throw new \yii\web\HttpException(400, 'empty cityPath', 405);
            return;
        }
        
        //return $cityPath;

        $query = new Query;
        $categories = $query->select('categoryCode, categoryIdentifier, parentCategoryIdentifier, categoryAdditionalInfo, id')
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
            $categorylink = "";
            if(Tools::startsWith($value['categoryAdditionalInfo'], 'http://') || Tools::startsWith($value['categoryAdditionalInfo'], 'https://')) {
                $categorylink = $value['categoryAdditionalInfo'];
            } else {
                $categorylink = $cityPath . $value['categoryAdditionalInfo'];
            }

            $links[] = array ($categorylink, $value['categoryIdentifier'], $value['categoryCode'], $value['id']);
            
            //only for dev and tests!
            //break;
        }
        
        //return $links;

        //TODO all categories links!!!

        $allResult = [];
        $allResult['coupons'] = [];
        
        for ($i = 0; $i < count($links); $i++) {
            $result = $this->get($links[$i][0], [
                'city' => Apist::filter('div.city.CityButton')->text(),
                //'cityLat'  => Tools::ru2lat(Apist::filter('#js-b-header > div.b-logo__city__wrapper > div.b-city__change > a > span')->text()->mb_substr(0, -1)),
                'coupons' => Apist::filter('#deals .deal')->each([
                    'title' => Apist::filter('#wrapedTitle a')->text()->call(function ($text) {
                        return Tools::getThreeFirstWords($text);
                    }),
                    'shortDescription' => Apist::filter('#wrapedTitle a')->text(),
                    'longDescription' => 'empty',
                    'conditions' => 'empty',
                    'features' => 'empty',
                    'timeToCompletion' => 'empty',
                    'originalCouponPrice' => Apist::filter('.price-block')->text()->call(function ($str) {
                        return trim(str_replace('от', '', str_replace('до', '', str_replace('тг', '', $str))));
                    }),
                    'originalPrice' => Apist::filter('.act_info .act_price')->text()->call(function ($str) {
                        return trim(str_replace('от', '', str_replace('до', '', str_replace('тг.', '', $str))));
                    }),
                    'discountPercent' => Apist::filter('.grey')->text(),
                    'discountPrice' => Apist::filter('.act_info .act_price_out')->text()->call(function ($str) {
                        return trim(str_replace('от', '', str_replace('до', '', str_replace('тг.', '', $str))));
                    }),
                    'pageLink' => Apist::filter('.details a')->attr('href')->call(function ($href) {
                        return $href;
                    }),
                    'boughtCount' => Apist::filter(".sold")->text()->call(function ($attr) {
                        return trim(str_replace("Получили", "", str_replace("Купили:", "", $attr)));
                    }),
                    'viewCount' => Apist::current()->attr("view_count"),
                    'sourceServiceCategories' => $links[$i][1],
                    'sourceServiceCategoriesId' => $links[$i][3],//todo add field to coupon table
                    'sourceServiceId' => $this->getSourceServiceId(),
                    'imagesLinks' => 'empty',
                    'mainImageLink' => Apist::filter('.thumbnail img')->attr('src'),
                    'currentLink' => $links[$i][0]
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
    //     $pageLink = \Yii::$app->db->createCommand('SELECT pageLink FROM coupon WHERE id=\''.$couponId.'\'')->queryScalar();

    //     $result = $this->get($pageLink, [
    //         'pageLink' => $pageLink,
    //         'couponId' => $couponId,
    //         'isOfficialCompleted' => Apist::filter('.e-offer__expire-text2')->text()->call(function($text){
    //             if (trim($text) == "Акция завершена") {
    //                 return 1;
    //             } else {
    //                 return 0;
    //             }
    //         }),
    //         'discountPrice' => Apist::filter('.e-offer__price')->text()->call(function($text){
    //             return trim(str_replace("от", "", str_replace("тг.", "", $text)));
    //         }),
    //         'longDescription' => Apist::filter('#information > .e-offer__description')->html(),
    //         'conditions' => Apist::filter('#information > .b-conditions')->html(),
    //         'features' => Apist::filter('#information > .b-offer__features')->html(),
    //         'imageLinks' => Apist::filter('.b-offer__imgs img')->each()->attr('src'),
    //         'timeToCompletion' => Apist::filter('.e-offer__expire-date')->text()->call(function($stamp){
    //             $stamp = substr(trim($stamp),0,10);
    //             $diff = intval($stamp) - time();
    //             return  $diff;
    //         }),
    //         'boughtCount' => Apist::filter('.b-offer__how_many_bought span')->text()->call(function ($str) {
    //             return Tools::getFirstWord($str);
    //         }),
    //     ],  ['proxy' => \Yii::$app->params['proxy']]);

    //     //print_r($result);

    //     if (empty($result['longDescription']) && empty($result['conditions']) && empty($result['features']) && empty($result['timeToCompletion'])) {
    //         //TODO SpecialPage!!!
    //         return 0;
    //     }
    //     return $result;
    }
}