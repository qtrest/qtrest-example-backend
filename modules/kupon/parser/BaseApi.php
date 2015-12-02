<?php

namespace app\modules\kupon\parser;

use app\models\Coupon;
use app\models\Statistics;
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
        \Yii::info('run testCities '.get_class($this), 'kupon');
        $cities = $this->cities();
        \Yii::info(serialize($cities), 'kupon');
        Tools::print_array('Cities', $cities);
    }

    public function testCategories()
    {
        \Yii::info('run testCategories '.get_class($this), 'kupon');
        $categories = $this->categories();
        \Yii::info(serialize($categories), 'kupon');
        Tools::print_array('Categories', $categories);
    }

    public function testCoupons($cityId, $write = false)
    {
        \Yii::info('run testCoupons '.get_class($this), 'kupon');
        $coupons = $this->couponsByCityId($cityId);
        \Yii::info(serialize($coupons), 'kupon');
        Tools::print_array('Coupons', $coupons);

        if ($write) {
            $this->fetchKuponsByCityId($cityId);
        }
    }

    public function testAdvancedCoupon($couponId)
    {
        \Yii::info('run testAdvancedCoupon '.get_class($this), 'kupon');
        $advancedCoupon = $this->couponAdvancedById($couponId);
        \Yii::info(serialize($advancedCoupon), 'kupon');
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

    public function getSourceServiceId()
    {
        return \Yii::$app->db->createCommand('SELECT id FROM sourceService WHERE serviceCode=\''.$this->getSourceServiceCode().'\'')->queryScalar();
    }

    protected function getSourceServiceName()
    {
        return '';
    }

    public function fetchAllCities()
    {
        \Yii::info('run fetchAllCities '.get_class($this), 'kupon');
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

    public function initData()
    {
        \Yii::info('run initData '.get_class($this), 'kupon');
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
        \Yii::info('run fillInCityTable '.get_class($this), 'kupon');
        $cities = $this->cities()['cities'];
        \Yii::info(serialize($cities), 'kupon');

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
        \Yii::info('run fillInCityTable '.get_class($this), 'kupon');
        $categories = $this->categories()['categories'];
        \Yii::info(serialize($categories), 'kupon');

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
        \Yii::info('run fetchKuponsByCityId '. $cityId . ' ' .get_class($this), 'kupon');

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
            \Yii::info(serialize($value), 'kupon');
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

        $this->createUpdateStatistics($this->getSourceServiceId(), static::getSourceServiceName(),
            'new', date('Y-m-d'), count($kupons));
    }

    private function createUpdateStatistics($sourceId, $sourceAlias, $codeType, $createDate, $countKupons)
    {
        $statistics = Statistics::find()->where('sourceId=:sId AND codeType=:cT AND createDate=:cD',[
            ':sId' => $sourceId,
            ':cT' => $codeType,
            ':cD' => $createDate
        ])->one();
        if ($statistics) {
            $count = (int)$statistics->count + count($countKupons);
        } else {
            $statistics = new Statistics();
            $count = count($countKupons);
        }
        $statistics->sourceId = $sourceId;
        $statistics->alias = $sourceAlias;
        $statistics->count = $count;
        $statistics->createDate = $createDate;
        $statistics->codeType = $codeType;
        $statistics->save();
    }

    public function updateAllCoupons()
    {
        \Yii::info('run updateAllCoupons '.get_class($this), 'kupon');

        $today = date_create(date('Y-m-d',time()));
        date_sub($today, date_interval_create_from_date_string('3 days'));

        $query = new Query;
        $res = $query->select('id')
            ->from('coupon')
            ->where('sourceServiceId=:sourceServiceId AND isArchive=:isArchive AND lastUpdateDateTime < :lastUpdateDateTime',
                [
                    ':sourceServiceId' => $this->getSourceServiceId(),
                    ':isArchive' => 0,
                    ':lastUpdateDateTime' => $today->format('Y-m-d H:i:s'),
                ]
            )
            ->orderBy('lastUpdateDateTime')
            ->limit(5) //only 5 coupons every 2 hours
            ->createCommand()
            ->queryColumn();

        foreach($res as $key => $value) {
            //sleep ( rand(1,2) );
            $this->updateCouponById($value);
        }
    }

    private function updateCouponById($couponId)
    {
        \Yii::info('run updateCouponById '. $couponId . ' ' .get_class($this), 'kupon');
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
                \Yii::info('skip update by time '. $couponId . ' ' .get_class($this), 'kupon');
                return;
            }
        }

        $result = $this->couponAdvancedById($couponId);
        \Yii::info(serialize($result), 'kupon');

        //Если данные пусты, то скорее всего запись обновить не удалось и она является архивной. Пробуем 5 раз для каждой записи. Если так и не получилось - архивируем запись.
        if (empty($result['longDescription']) && empty($result['timeToCompletion'])
        && empty($result['conditions']) && empty($result['boughtCount'])) {

            //обновить одну запись пробуем максимум пять раз после чего считаем её архивной.
            $tryToUpdateCount = $query->select('tryToUpdateCount')
                ->from('coupon')
                ->where('id=:couponId',
                    [
                        ':couponId' => $couponId,
                    ]
                )
                ->createCommand()
                ->queryScalar();

            \Yii::info('tryToUpdateCoupon '. $couponId . ' ' .get_class($this) . ' UPDATE COUNT ' . $tryToUpdateCount, 'kupon');

            if ($tryToUpdateCount >= 5) {
                $connection->createCommand()->update('coupon', [
                    'isArchive' => 1,
                ], ['id' => $couponId])->execute();
            } else {
                $connection->createCommand()->update('coupon', [
                    'tryToUpdateCount' => $tryToUpdateCount + 1,
                ], ['id' => $couponId])->execute();
            }

            return;
        } else {
            
            \Yii::info('tryToUpdateCoupon '. $couponId . ' ' .get_class($this) . ' UPDATE COMPLETED!', 'kupon');

            $connection->createCommand()->update('coupon', [
                'lastUpdateDateTime' => date('Y.m.d H:i:s', time()),
                'longDescription' => $result['longDescription'],
                'conditions' => $result['conditions'],
                'features' => $result['features'],
                'timeToCompletion' => $result['timeToCompletion'],
                'boughtCount' => $result['boughtCount'],
                'imagesLinks' => implode(', ', $result['imageLinks']),
            ], ['id' => $couponId])->execute();

            //$coupon = Coupon::findOne($couponId);
            $this->createUpdateStatistics($this->getSourceServiceId(), static::getSourceServiceName(),
                'archive', date('Y-m-d'), 1);
        }
    }
}
