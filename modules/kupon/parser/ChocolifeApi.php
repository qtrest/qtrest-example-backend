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
        $result = $this->get('/', [
            'categories'  => Apist::filter('#b-deals__menunav__nav li a')->each([
                'categoryName' => Apist::filter('span')->text(),
                'categoryId' => Apist::current()->attr('cat_id'),
                'parentCategoryId' => Apist::current()->attr('parent_id'),
            ]),
        ]);

        $result = Tools::trimArray($result);
        $result = Tools::removeLastWordArray($result);

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
                'shortDescription' => Apist::filter('.e-deal__text')->text(),
                'longDescription' => 'empty',
                'conditions' => 'empty',
                'features' => 'empty',
                'timeToCompletion' => 'empty',

				'originalPrice' => Apist::filter('div.b-deal__info > span.e-deal__price.e-deal__price--old')->text(),
                'discountPercent' => Apist::filter('.e-deal__discount')->text(),
                'discountPrice' => Apist::filter('div.b-deal__info > span:nth-child(2)')->text(),
				'pageLink' => Apist::filter('div.b-deal__info > a')->attr('href')->call(function ($href) {
                    return parse_url($href)['path'];
                }),
                'boughtCount' => Apist::filter('div.b-deal__bought > span')->text()->call(function ($str)
                {
                    return Tools::getLastWord($str);
                }),
                'sourceServiceCategories' => Apist::current()->attr('data-categories')->call(function ($str)
                {
                    return str_replace('[','',str_replace(']','',$str));
                }),
                'sourceServiceId' => $this->getSourceServiceId(),
                'imagesLinks' => 'empty',
                'mainImageLink' => Apist::filter('div.e-deal__imgs img')->attr('data-original'),
			]),
        ]);

        $result = Tools::trimArray($result);

        $result['cityCode'] = Tools::ru2lat($result['city']);

        return $result;
    }

    protected function couponAdvancedById($couponId)
    {
        $connection=\Yii::$app->db;

        $pageLink = \Yii::$app->db->createCommand('SELECT pageLink FROM coupon WHERE id=\''.$couponId.'\'')->queryScalar();

        $result = $this->get($pageLink, [
            'longDescription' => Apist::filter('#information > p.e-offer__description')->text(),
            'conditions' => Apist::filter('#information > div.b-conditions')->html(),
            'features' => Apist::filter('#information > div.b-offer__features')->html(),
            'imageLinks' => Apist::filter('div.b-offer__imgs img')->each()->attr('src'),
            'timeToCompletion' => Apist::filter('.e-offer__expire-date')->text()->call(function($stamp){
                $stamp = substr(trim($stamp),0,10);
                $diff = intval($stamp) - time();
                return  $diff;
            }),
            'boughtCount' => Apist::filter('.b-offer__how_many_bought span')->text()->call(function ($str) {
                return Tools::getFirstWord($str);
            }),
        ]);

        if (empty($result['longDescription']) && empty($result['timeToCompletion'])) {
            //TODO SpecialPage!!!
            return 0;
        }
        return $result;
    }
}