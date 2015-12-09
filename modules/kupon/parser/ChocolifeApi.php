<?php

namespace app\modules\kupon\parser;

use SleepingOwl\Apist\Apist;
use app\components\Tools;
use yii\db\Query;
use yii\db\QueryBuilder;

class ChocolifeApi extends BaseApi
{

    public function getBaseUrl()
    {
        return 'https://www.chocolife.me';
    }

    protected function getSourceServiceCode()
    {
        return 'chocolifeme';
    }

    protected function getSourceServiceName()
    {
        return 'Chocolife.me';
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
            'cities'  => Apist::filter('.b-city_links__wrapper li a')->each([
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
        $result = $this->get('/', [
            'categories'  => Apist::filter('#b-deals__menunav__category__place li a')->each([
                'categoryName' => Apist::filter('span')->text(),
                'categoryId' => Apist::current()->attr('cat_id'),
                'parentCategoryId' => Apist::current()->attr('parent_id'),
                'categoryAdditionalInfo' => Apist::current()->call(function ($current)
                {
                        return '0';
                }),
            ]),
        ]);

        $result = Tools::trimArray($result);
        //used before 03/12/2015
        //$result = Tools::removeLastWordArray($result);

        return $result;
    }

    protected function couponsByCityId($cityId)
    {
        $cityPath = \Yii::$app->db->createCommand('SELECT path FROM cityUrl WHERE cityId=\''.$cityId.'\'')->queryScalar();
        if (empty($cityPath)) {
            throw new \yii\web\HttpException(400, 'empty cityPath', 405);
            return;
        }

        $result = $this->get($cityPath, [
            'city'  => Apist::filter('#js-b-header > div.b-logo__city__wrapper > div.b-city__change > a > span')->text(),
            //'cityLat'  => Tools::ru2lat(Apist::filter('#js-b-header > div.b-logo__city__wrapper > div.b-city__change > a > span')->text()->mb_substr(0, -1)),
			'coupons' => Apist::filter('body > div.b-deals__wrapper > ul li')->each([
                'title' => Apist::filter('.e-deal__title')->text(),
                'altTitle' => Apist::filter('.e-plate__img')->attr('alt')->call(function ($alt) { return Tools::getThreeFirstWords($alt); }),
                //exists then else NOT working. Why?
                //                 'title' => Apist::filter('.e-deal__title')->exists()->then(
                //     Apist::filter('.e-deal__title')->text() // This value will be used if .page-header element was found
                // )->else(
                //     Apist::filter('.e-plate__img')->attr('alt')->call(function ($alt)
                //     {
                //         return Tools::getFirstSentence($alt);
                //     })
                // ),
                // 'shortDescription' => Apist::filter('.e-deal__text')->exists()->then(
                //         Apist::filter('.e-deal__text')->text() // This value will be used if .page-header element was found
                //     )->else(
                //         Apist::filter('.e-plate__img')->attr('alt')
                //     ),
                'shortDescription' => Apist::filter('.e-deal__text')->text(),
                'altShortDescription' => Apist::filter('.e-plate__img')->attr('alt'),
                'longDescription' => 'empty',
                'conditions' => 'empty',
                'features' => 'empty',
                'timeToCompletion' => 'empty',
				'originalCouponPrice' => Apist::filter('div.b-deal__info > span.e-deal__price.e-deal__price')->text(),
				'originalPrice' => Apist::filter('div.b-deal__info > span.e-deal__price.e-deal__price--old')->text(),
                'discountPercent' => Apist::filter('.e-deal__discount')->text(),
                'discountPrice' => Apist::filter('div.b-deal__info > span:nth-child(2)')->text(),
                'rawPageLink' => Apist::filter('.b-main_page__link')->attr('href')->call(function ($href) {
                    return $href;
                }),
                'pathPageLink' => Apist::filter('.b-main_page__link')->attr('href')->call(function ($href) {
                    return parse_url($href)['path']; 
                }),
				'pageLink' => Apist::filter('.b-main_page__link')->attr('href')->call(function ($href) {
                    return $href;
                    //return parse_url($href)['path']; 
                }),
                'altPageLink' => Apist::filter('.e-deal__imgs a')->attr('href')->call(function ($href) {
                    return $href;
                    //return parse_url($href)['path'];
                }),
                'boughtCount' => Apist::filter('div.b-deal__bought .e-deal__count')->text(),
                'sourceServiceCategories' => Apist::current()->attr('data-categories')->call(function ($str)
                {
                    return str_replace('[','',str_replace(']','',$str));
                }),
                'sourceServiceId' => $this->getSourceServiceId(),
                'imagesLinks' => 'empty',
                'mainImageLink' => Apist::filter('div.e-deal__imgs img')->attr('data-original'),
                'mainSrcImageLink' => Apist::filter('div.e-deal__imgs img')->attr('src'),
                'altMainImageLink' => Apist::filter('.e-plate__img')->attr('data-original'),
                'altSrcMainImageLink' => Apist::filter('.e-plate__img')->attr('src'),
			]),
        ]);

        $result = Tools::trimArray($result);

        $result['cityCode'] = Tools::ru2lat($result['city']);
        
        for($i = 0; $i < count($result['coupons']); $i++) {
            if (empty(trim($result['coupons'][$i]['mainImageLink']))) {
                $result['coupons'][$i]['mainImageLink'] = $result['coupons'][$i]['mainSrcImageLink'];
            }

            if (empty(trim($result['coupons'][$i]['altMainImageLink']))) {
                $result['coupons'][$i]['altMainImageLink'] = $result['coupons'][$i]['altSrcMainImageLink'];
            }

            if (empty(trim($result['coupons'][$i]['title']))) {
                $result['coupons'][$i]['title'] = $result['coupons'][$i]['altTitle'];
                $result['coupons'][$i]['shortDescription'] = $result['coupons'][$i]['altShortDescription'];
                $result['coupons'][$i]['mainImageLink'] = $result['coupons'][$i]['altMainImageLink'];
                $result['coupons'][$i]['pageLink'] = $result['coupons'][$i]['altPageLink'];
            }
        }

        return $result;
    }

    protected function couponAdvancedById($couponId)
    {
        $pageLink = \Yii::$app->db->createCommand('SELECT pageLink FROM coupon WHERE id=\''.$couponId.'\'')->queryScalar();

        $result = $this->get($pageLink, [
            'pageLink' => $pageLink,
            'couponId' => $couponId,
            'isOfficialCompleted' => Apist::filter('.e-offer__expire-text2')->text()->call(function($text){
                if (trim($text) == "Акция завершена") {
                    return 1;
                } else {
                    return 0;
                }
            }),
            'discountPrice' => Apist::filter('.e-offer__price')->text()->call(function($text){
                return trim(str_replace("от", "", str_replace("тг.", "", $text)));
            }),
            'longDescription' => Apist::filter('#information > .e-offer__description')->html(),
            'conditions' => Apist::filter('#information > .b-conditions')->html(),
            'features' => Apist::filter('#information > .b-offer__features')->html(),
            'imageLinks' => Apist::filter('.b-offer__imgs img')->each()->attr('src'),
            'timeToCompletion' => Apist::filter('.e-offer__expire-date')->text()->call(function($stamp){
                $stamp = substr(trim($stamp),0,10);
                $diff = intval($stamp) - time();
                return  $diff;
            }),
            'boughtCount' => Apist::filter('.b-offer__how_many_bought span')->text()->call(function ($str) {
                return Tools::getFirstWord($str);
            }),
        ]);

        //print_r($result);

        if (empty($result['longDescription']) && empty($result['conditions']) && empty($result['features']) && empty($result['timeToCompletion'])) {
            //TODO SpecialPage!!!
            return 0;
        }
        return $result;
    }
}