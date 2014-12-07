<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "city".
 *
 * @property integer $id
 * @property string $cityName
 * @property string $cityCode
 *
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
            [['cityName', 'cityCode'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'cityName' => Yii::t('app', 'City Name'),
            'cityCode' => Yii::t('app', 'City Code'),
        ];
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
