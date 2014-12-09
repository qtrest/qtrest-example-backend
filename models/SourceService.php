<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sourceService".
 *
 * @property integer $id
 * @property string $serviceName
 * @property string $serviceCode
 * @property string $serviceBaseUrl
 * @property string $lastUpdateDateTime
 *
 * @property Categories[] $categories
 * @property CityUrl[] $cityUrls
 * @property Coupon[] $coupons
 */
class SourceService extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sourceService';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['serviceName', 'serviceCode', 'serviceBaseUrl'], 'required'],
            [['lastUpdateDateTime'], 'safe'],
            [['serviceName', 'serviceCode', 'serviceBaseUrl'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'serviceName' => 'Service Name',
            'serviceCode' => 'Service Code',
            'serviceBaseUrl' => 'Service Base Url',
            'lastUpdateDateTime' => 'Last Update Date Time',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Categories::className(), ['sourceServiceId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCityUrls()
    {
        return $this->hasMany(CityUrl::className(), ['sourceServiceId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoupons()
    {
        return $this->hasMany(Coupon::className(), ['sourceServiceId' => 'id']);
    }
}
