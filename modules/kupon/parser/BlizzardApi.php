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

        $urls = [];

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
            $links[] = $cityPath . $value['categoryAdditionalInfo'];
        }

        return $links;

//
//        $result = $this->get($cityPath, [
//            'city'  => Apist::filter('#js-b-header > div.b-logo__city__wrapper > div.b-city__change > a > span')->text(),
//            //'cityLat'  => Tools::ru2lat(Apist::filter('#js-b-header > div.b-logo__city__wrapper > div.b-city__change > a > span')->text()->mb_substr(0, -1)),
//            'coupons' => Apist::filter('body > div.b-deals__wrapper > ul li')->each([
//                'title' => Apist::filter('.e-deal__title')->text(),
//                'shortDescription' => Apist::filter('.e-deal__text')->text(),
//                'longDescription' => 'empty',
//                'conditions' => 'empty',
//                'features' => 'empty',
//                'timeToCompletion' => 'empty',
//                'originalCouponPrice' => Apist::filter('div.b-deal__info > span.e-deal__price.e-deal__price')->text(),
//                'originalPrice' => Apist::filter('div.b-deal__info > span.e-deal__price.e-deal__price--old')->text(),
//                'discountPercent' => Apist::filter('.e-deal__discount')->text(),
//                'discountPrice' => Apist::filter('div.b-deal__info > span:nth-child(2)')->text(),
//                'pageLink' => Apist::filter('div.b-deal__info > a')->attr('href')->call(function ($href) {
//                    return parse_url($href)['path'];
//                }),
//                'boughtCount' => Apist::filter('div.b-deal__bought > span')->text()->call(function ($str)
//                {
//                    return Tools::getLastWord($str);
//                }),
//                'sourceServiceCategories' => Apist::current()->attr('data-categories')->call(function ($str)
//                {
//                    return str_replace('[','',str_replace(']','',$str));
//                }),
//                'sourceServiceId' => $this->getSourceServiceId(),
//                'imagesLinks' => 'empty',
//                'mainImageLink' => Apist::filter('div.e-deal__imgs img')->attr('data-original'),
//            ]),
//        ]);
//
//        $result = Tools::trimArray($result);
//
//        $result['cityCode'] = Tools::ru2lat($result['city']);
//
//        return $result;
        return;
    }

    protected function couponAdvancedById($couponId)
    {
        return;
    }

}