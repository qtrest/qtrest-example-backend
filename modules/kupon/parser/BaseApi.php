<?php

namespace app\modules\kupon\parser;

use SleepingOwl\Apist\Apist;
use app\components\Tools;
use yii\db\Query;
use yii\db\QueryBuilder;


abstract class BaseApi extends Apist
{

    public function getBaseUrl()
    {
        return '';
    }

    protected function getSourceServiceCode()
    {
        return '';
    }

    protected function getSourceServiceId()
    {
        return \Yii::$app->db->createCommand('SELECT id FROM sourceService WHERE serviceCode=\''.$this->getSourceServiceCode().'\'')->queryScalar();
    }

    protected function getSourceServiceName()
    {
        return '';
    }

    public function initData()
    {
        if (
            empty($this->getBaseUrl())
            || empty($this->getSourceServiceCode()) 
            || empty($this->getSourceServiceName())
        ) {
            throw new \yii\web\HttpException(400, 'empty parameters', 405);
            return;
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
            ])->execute();
        }

        $this->fillInCityTable();
        $this->fillInCategoriesTable();
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
                ])->execute();
            }

            $cityId = $connection->createCommand('SELECT id FROM city WHERE cityCode=\''.Tools::ru2lat($value['city']).'\'')->queryScalar();

            $query = new Query;
            $res = $query->select('id')
                ->from('cityUrl')
                ->where('cityId=:cityId', [':cityId' => $cityId])
                ->createCommand()
                ->queryScalar();

            if (empty($res)) {
                $connection->createCommand()->insert('cityUrl', [
                    'cityId' => $cityId,
                    'url' => $value['link'],
                    'path' => $value['path'],
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
                ])->execute();
            }
        }
    }

    abstract protected function cities();
    abstract protected function index($cityId);
    abstract public function fetchKupons($cityId);
}
