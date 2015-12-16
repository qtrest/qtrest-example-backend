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
            'categories'  => Apist::filter('.navigation_menu .menu_ul1 a')->each([
                'categoryName' => Apist::current()->call(function ($current)
                {
                    //$id = $current->attr('id');
                    return trim($current->filter('a')->text());
                }),
                'categoryId' => Apist::current()->call(function ($current)
                {

                    $href = $current->attr('href');
                    $href = trim ($href);
                    $href = rtrim ($href, '/');

                    $categoryId = end(explode('/', $href));

                    if ($categoryId > '') {
                        return $categoryId;
                    } else {
                        return -1;
                    }

                    //old, before 07.12.2015
                    // $categoryId = str_replace('categ', '', $current->attr('id'));

                    // if ($categoryId > '') {
                    //     BlizzardApi::$lastParentCategoryId = $categoryId;
                    //     return $categoryId;
                    // } else {
                    //     $categoryId = end(explode('/', $current->attr('href')));

                    //     if ($categoryId > '') {
                    //         return end(explode('-', $categoryId));
                    //     } else {
                    //         return -1;
                    //     }
                    // }
                }),

                'parentCategoryId' => Apist::current()->call(function ($current)
                {

                    $href = $current->attr('href');
                    $href = trim ($href);
                    $href = rtrim ($href, '/');

                    $arr = explode('/', $href);

                    $categoryId = -1;

                    if (count ($arr) > 1) {
                        if (Tools::isDigitString($arr[count ($arr) -2])) {
                            $categoryId = $arr[count ($arr) -2];
                        }
                    }

                    return $categoryId;


                    //old, before 07122015
                    // $categoryId = str_replace('categ', '', $current->attr('id'));

                    // if ($categoryId > '') {
                    //     BlizzardApi::$lastParentCategoryId = $categoryId;
                    //     return $categoryId;
                    // } else {
                    //     $categoryId = end(explode('/', $current->attr('href')));

                    //     if ($categoryId > '') {
                    //         return current(explode('-', $categoryId));
                    //     } else {
                    //         return BlizzardApi::$lastParentCategoryId;
                    //     }
                    // }
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

        //remove digits from last of categoryName
        for($i = 0; $i < count($result['categories']); $i++) {
            if (!empty(trim($result['categories'][$i]['categoryName']))) {
                $result['categories'][$i]['categoryName'] = trim(Tools::removeLastDigits($result['categories'][$i]['categoryName']));
            }
        }

        $resultCleared = array();
        $resultCleared['categories'] = array();

        //clear empty categories and 
        for($i = 0; $i < count($result['categories']); $i++) {
            if (!empty(trim($result['categories'][$i]['categoryName']))) {
                if ( !in_array($result['categories'][$i]['categoryId'], ["feedback", "how-it-work", "how_pay_order", "delivery", "contact", "about", "feedback", "bonusclub"]) ) {
                    $resultCleared['categories'][] = $result['categories'][$i];
                }
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
            $categorylink = "";
            if(Tools::startsWith($value['categoryAdditionalInfo'], 'http://') || Tools::startsWith($value['categoryAdditionalInfo'], 'https://')) {
                $categorylink = $value['categoryAdditionalInfo'];
            } else {
                $categorylink = $cityPath . $value['categoryAdditionalInfo'];
            }

            $links[] = array ($categorylink, $value['categoryIdentifier']);
        }

        //TODO all categories links!!!

        $allResult = [];
        $allResult['coupons'] = [];

        for ($i = 0; $i < count($links); $i++) {
            $result = $this->get($links[$i][0], [
                'city' => Apist::filter('#tows > div > span')->text(),
                //'cityLat'  => Tools::ru2lat(Apist::filter('#js-b-header > div.b-logo__city__wrapper > div.b-city__change > a > span')->text()->mb_substr(0, -1)),
                'coupons' => Apist::filter('#page_content figure')->each([
                    'title' => Apist::filter('.act_info .act_title')->text(),
                    'shortDescription' => Apist::filter('.act_info .podcateg_name')->text(),
                    'longDescription' => 'empty',
                    'conditions' => 'empty',
                    'features' => 'empty',
                    'timeToCompletion' => 'empty',
                    'originalCouponPrice' => Apist::filter('.act_info .act_price_out')->text()->call(function ($str) {
                        return trim(str_replace('от', '', str_replace('до', '', str_replace('тг.', '', $str))));
                    }),
                    'originalPrice' => Apist::filter('.act_info .act_price')->text()->call(function ($str) {
                        return trim(str_replace('от', '', str_replace('до', '', str_replace('тг.', '', $str))));
                    }),
                    'discountPercent' => Apist::filter('.act_info .act_skidka')->text(),
                    'discountPrice' => Apist::filter('.act_info .act_price_out')->text()->call(function ($str) {
                        return trim(str_replace('от', '', str_replace('до', '', str_replace('тг.', '', $str))));
                    }),
                    'pageLink' => Apist::filter('.act_info > a')->attr('href')->call(function ($href) {
                        return $href;
                    }),
                    'boughtCount' => Apist::current()->attr("count_bought")->call(function ($attr) {
                        return trim(str_replace("Уже купили", "", $attr));
                    }),
                    'viewCount' => Apist::current()->attr("view_count"),
                    'sourceServiceCategories' => $links[$i][1],
                    'sourceServiceId' => $this->getSourceServiceId(),
                    'imagesLinks' => 'empty',
                    'mainImageLink' => Apist::filter('.act_image img.lazy')->attr('data-original'),
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
        $pageLink = \Yii::$app->db->createCommand('SELECT pageLink FROM coupon WHERE id=\''.$couponId.'\'')->queryScalar();

        $result = $this->get($pageLink, [
            'pageLink' => $pageLink,
            'couponId' => $couponId,
            'isOfficialCompleted' => false,
            'discountPrice' => Apist::filter('#price')->text()->call(function($text){
                return trim(str_replace("от", "", str_replace("тенге", "", $text)));
            }),
            'longDescription' => Apist::filter('#tabsbloc2')->html(),
            'conditions' => Apist::filter('#tabsbloc1 > div.usl_osb > div.tab_usl')->html(),
            'features' => Apist::filter('#tabsbloc1 > div.usl_osb > div.tab_osoben')->html(),
            'imageLinks' => Apist::filter('#carousel img')->each()->attr('src'),
            'timeToCompletion' => Apist::current()->call(function() {
                $dates=explode("-", date("m-d-Y"));
                $then=mktime (0,0,0,$dates[0],$dates[1]+1,$dates[2]);
                $now=time();
                $how=$then-$now - 21600; //TODO gmt!!
                return $how;
            }),
            'boughtCount' => Apist::filter('.already_bought_ac')->text()->call(function($text){
                return trim(str_replace("Уже купили", "", str_replace("человек", "", $text)));
            })
        ]);

        if (empty($result['longDescription']) && empty($result['timeToCompletion'])) {
            //TODO SpecialPage!!!
            return 0;
        }
        return $result;
    }
}