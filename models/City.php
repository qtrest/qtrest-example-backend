<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "city".
 *
 * @property integer $id
 * @property string $cityName
 * @property string $cityCode
 * @property integer $countryId
 *
 * @property Country $country
 * @property CityUrl[] $cityUrls
 * @property Coupon[] $coupons
 */
class City extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'city';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cityName', 'cityCode'], 'required'],
            [['countryId'], 'integer'],
            [['cityName', 'cityCode'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('city', 'ID'),
            'cityName' => Yii::t('city', 'City Name'),
            'cityCode' => Yii::t('city', 'City Code'),
            'countryId' => Yii::t('city', 'Country ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['id' => 'countryId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCityUrls()
    {
        return $this->hasMany(CityUrl::className(), ['cityId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoupons()
    {
        return $this->hasMany(Coupon::className(), ['cityId' => 'id']);
    }
}
