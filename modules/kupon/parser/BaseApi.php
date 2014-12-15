<?php

namespace app\modules\kupon\parser;

use SleepingOwl\Apist\Apist;
use app\components\Tools;
use yii\db\Query;
use yii\db\QueryBuilder;


abstract class BaseApi extends Apist
{
    abstract protected function cities();
    abstract protected function categories();
    abstract protected function couponsByCityId($cityId);
    abstract protected function couponAdvancedById($couponId);

    public function testCities()
    {
        $cities = $this->cities();
        Tools::print_array('Cities', $cities);
    }

    public function testCategories()
    {
        $categories = $this->categories();
        Tools::print_array('Categories', $categories);
    }

    public function testCoupons($cityId, $write = false)
    {
        $coupons = $this->couponsByCityId($cityId);
        Tools::print_array('Coupons', $coupons);

        if ($write) {
            $this->fetchKuponsByCityId($cityId);
        }
    }

    public function testAdvancedCoupon($couponId)
    {
        $advancedCoupon = $this->couponAdvancedById($couponId);
        Tools::print_array('Advanced coupon', $advancedCoupon);
    }

    public function getBaseUrl()
    {
        return '';
    }

    protected function getSourceServiceCode()
    {
        return '';
    }

    protected function getCountryName()
    {
        return '';
    }


    protected function getCountryCode()
    {
        return '';
    }

    protected function getCountryId()
    {
        return \Yii::$app->db->createCommand('SELECT id FROM country WHERE countryCode=\''.$this->getCountryCode().'\'')->queryScalar();
    }

    protected function getSourceServiceId()
    {
        return \Yii::$app->db->createCommand('SELECT id FROM sourceService WHERE serviceCode=\''.$this->getSourceServiceCode().'\'')->queryScalar();
    }

    protected function getSourceServiceName()
    {
        return '';
    }

    public function fetchAllCities()
    {
        $query = new Query;
        $res = $query->select('cityId')
            ->from('cityUrl')
            ->where('sourceServiceId=:sourceServiceId',
                [
                    ':sourceServiceId' => $this->getSourceServiceId()
                ]
            )
            ->createCommand()
            ->queryColumn();

        foreach($res as $key => $value) {
            $this->fetchKuponsByCityId($value);
        }
    }

    public function updateAllCoupons()
    {
        $query = new Query;
        $res = $query->select('id')
            ->from('coupon')
            ->where('sourceServiceId=:sourceServiceId',
                [
                    ':sourceServiceId' => $this->getSourceServiceId()
                ]
            )
            ->createCommand()
            ->queryColumn();

        foreach($res as $key => $value) {
            //sleep ( rand(1,2) );
            $this->updateCouponById($value);
        }
    }

    public function initData()
    {
		$baseUrl = $this->getBaseUrl();
		$sourceServiceCode = $this->getSourceServiceCode();
		$sourceServiceName = $this->getSourceServiceName();
		$countryName = $this->getCountryName();
		$countryCode = $this->getCountryCode();
        if (
            empty($baseUrl) 
            || empty($sourceServiceCode) 
            || empty($sourceServiceName)
            || empty($countryName)
            || empty($countryCode)
        ) {
            throw new \yii\web\HttpException(400, 'empty parameters', 405);
            return;
        }

        $query = new Query;
        $res = $query->select('lastUpdateDateTime')
            ->from('sourceService')
            ->where('id=:sourceServiceId',
                [
                    ':sourceServiceId' => $this->getSourceServiceId(),
                ]
            )
            ->createCommand()
            ->queryScalar();

        if ( !is_null ($res) ) {
            $time = time();
            $diff = $time - strtotime ($res);
            //update every 4 hours (14400 unix seconds)
            if ($diff <= 14400) {
                return;
            }
        }

        //check service source
        $connection=\Yii::$app->db;
        $query = new Query;
        $res = $query->select('id')
            ->from('sourceService')
            ->where('serviceCode=:serviceCode', [':serviceCode' => $this->getSourceServiceCode()])
            ->createCommand()
            ->queryScalar();

        if (empty($res)) {
            $connection->createCommand()->insert('sourceService', [
                'serviceName' => $this->getSourceServiceName(),
                'serviceCode' => $this->getSourceServiceCode(),
                'serviceBaseUrl' => $this->getBaseUrl(),
            ])->execute();
        }

        //check country
        $connection=\Yii::$app->db;
        $query = new Query;
        $res = $query->select('id')
            ->from('country')
            ->where('countryCode=:countryCode', [':countryCode' => $this->getCountryCode()])
            ->createCommand()
            ->queryScalar();

        if (empty($res)) {
            $connection->createCommand()->insert('country', [
                'countryName' => $this->getCountryName(),
                'countryCode' => $this->getCountryCode(),
            ])->execute();
        }

        $this->fillInCityTable();
        $this->fillInCategoriesTable();

        //update lastUpdateDateTime in sourceService
        $connection->createCommand()->update('sourceService', [
            'lastUpdateDateTime' => date('Y.m.d H:i:s', time()),
        ],['id' => $this->getSourceServiceId()])->execute();
    }

    private function fillInCityTable()
    {
        $cities = $this->cities()['cities'];

        $connection=\Yii::$app->db;

        foreach ($cities as $key => $value) {

            $query = new Query;
            $res = $query->select('id')
                ->from('city')
                ->where('cityCode=:cityCode', [':cityCode' => Tools::ru2lat($value['city'])])
                ->createCommand()
                ->queryScalar();

            if (empty($res)) {
                $connection->createCommand()->insert('city', [
                    'cityName' => $value['city'],
                    'cityCode' => Tools::ru2lat($value['city']),
                    'countryId' => $this->getCountryId(),
                ])->execute();
            }

            $cityId = $connection->createCommand('SELECT id FROM city WHERE cityCode=\''.Tools::ru2lat($value['city']).'\'')->queryScalar();

            $query = new Query;
            $res = $query->select('id')
                ->from('cityUrl')
                ->where('cityId=:cityId AND sourceServiceId=:sourceServiceId', [':cityId' => $cityId, ':sourceServiceId' => $this->getSourceServiceId()])
                ->createCommand()
                ->queryScalar();

            if (empty($res)) {
                $connection->createCommand()->insert('cityUrl', [
                    'cityId' => $cityId,
                    'url' => ($value['path'] == '#' ? '/' : $value['link']),
                    'path' => ($value['path'] == '#' ? '/' : $value['path']),
                    'sourceServiceId' => $this->getSourceServiceId(),
                ])->execute();
            }
        }
    }

    private function fillInCategoriesTable()
    {
        $categories = $this->categories()['categories'];

        $connection=\Yii::$app->db;

        foreach ($categories as $key => $value) {

            $query = new Query;
            $res = $query->select('id')
                ->from('categories')
                ->where('categoryCode=:categoryCode AND sourceServiceId=:sourceServiceId', 
                    [
                    ':categoryCode' => Tools::ru2lat($value['categoryName']), 
                    ':sourceServiceId' => $this->getSourceServiceId()
                    ]
                )
                ->createCommand()
                ->queryScalar();

            if (empty($res)) {
                $connection->createCommand()->insert('categories', [
                    'sourceServiceId' => $this->getSourceServiceId(),
                    'categoryName' => $value['categoryName'],
                    'categoryCode' => Tools::ru2lat($value['categoryName']),
                    'categoryIdentifier' => $value['categoryId'],
                    'parentCategoryIdentifier' => $value['parentCategoryId'],
                    'categoryAdditionalInfo' => $value['categoryAdditionalInfo'],
                ])->execute();
            }
        }
    }

    private function fetchKuponsByCityId($cityId)
    {
        $connection=\Yii::$app->db;

        $query = new Query;
        $res = $query->select('lastUpdateDateTime')
            ->from('cityUrl')
            ->where('cityId=:cityId AND sourceServiceId=:sourceServiceId',
                [
                    ':cityId' => $cityId,
                    ':sourceServiceId' => $this->getSourceServiceId(),
                ]
            )
            ->createCommand()
            ->queryScalar();

        if ( !is_null ($res) ) {
            $time = time();
            $diff = $time - strtotime ($res);
            //update every 4 hours (14400 unix seconds)
            if ($diff <= 14400) {
                return;
            }
        }

        $result = $this->couponsByCityId($cityId);
        $cityCode = $result['cityCode'];
        $kupons = $result['coupons'];

        foreach ($kupons as $key => $value) {
            $recordHashSrc = $cityCode.$value['sourceServiceId'].$value['pageLink'];
            $recordHash = md5($recordHashSrc);

            $query = new Query;
            $res = $query->select('id')
                ->from('coupon')
                ->where('recordHash=:recordHash',
                    [
                        ':recordHash' => $recordHash,
                    ]
                )
                ->createCommand()
                ->queryScalar();

            if (empty($res)) {
                $connection->createCommand()->insert('coupon', [
                    'sourceServiceId' => $this->getSourceServiceId(),
                    'cityId' => $cityId,
                    'lastUpdateDateTime' => '0000-00-00 00:00:00',
                    'createTimestamp' => date('Y.m.d H:i:s', time()),

                    'recordHash' => $recordHash,

                    'title' => $value['title'],
                    'shortDescription' => $value['shortDescription'],
                    'longDescription' => $value['longDescription'],
                    'conditions' => $value['conditions'],
                    'features' => $value['features'],
                    'timeToCompletion' => $value['timeToCompletion'],

                    'originalCouponPrice' => $value['originalCouponPrice'],
					'originalPrice' => $value['originalPrice'],
                    'discountPercent' => ($value['discountPercent'] > '' ? $value['discountPercent'] : '0%'),
                    'discountPrice' => $value['discountPrice'],

					'discountType' => ( ( isset($value['discountType']) && ($value['discountType'] > ''))
                        ? $value['discountType']
                        : ((($value['originalPrice'] > '') && ($value['discountPrice'] > ''))
                            ? 'full'
                            : ($value['originalCouponPrice'] > ''
                                ? ($value['originalCouponPrice'] == '0'
                                    ? 'freeCoupon'
                                    : 'coupon')
                                : 'undefined' ))),
					
                    'boughtCount' => $value['boughtCount'],
                    'sourceServiceCategories' => $value['sourceServiceCategories'],
                    'imagesLinks' => $value['imagesLinks'],
                    'pageLink' => $value['pageLink'],
                    'mainImageLink' => $value['mainImageLink'],
                ])->execute();
            }
        }

        //update lastUpdateDateTime in cityUrl
        $connection->createCommand()->update('cityUrl', [
            'lastUpdateDateTime' => date('Y.m.d H:i:s', time()),
        ],['cityId' => $cityId, 'sourceServiceId' => $this->getSourceServiceId()])->execute();
    }

    private function updateCouponById($couponId)
    {
        $connection=\Yii::$app->db;

        $query = new Query;
        $res = $query->select('lastUpdateDateTime')
            ->from('coupon')
            ->where('id=:couponId',
                [
                    ':couponId' => $couponId,
                ]
            )
            ->createCommand()
            ->queryScalar();

        if ( !is_null ($res) ) {
            $time = time();
            $diff = $time - strtotime ($res);
            //update every 4 hours (14400 unix seconds)
            if ($diff <= 14400) {
                return;
            }
        }

        $result = $this->couponAdvancedById($couponId);

        $connection->createCommand()->update('coupon', [
            'lastUpdateDateTime' => date('Y.m.d H:i:s', time()),
            'longDescription' => $result['longDescription'],
            'conditions' => $result['conditions'],
            'features' => $result['features'],
            'timeToCompletion' => $result['timeToCompletion'],
            'boughtCount' => $result['boughtCount'],
            'imagesLinks' => implode(', ', $result['imageLinks']),
        ],['id' => $couponId])->execute();
    }
}
