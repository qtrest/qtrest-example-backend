<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "coupon".
 *
 * @property integer $id
 * @property integer $sourceServiceId
 * @property integer $cityId
 * @property string $createTimestamp
 * @property string $lastUpdateDateTime
 * @property string $recordHash
 * @property string $title
 * @property string $shortDescription
 * @property string $longDescription
 * @property string $conditions
 * @property string $features
 * @property string $imagesLinks
 * @property string $timeToCompletion
 * @property string $mainImageLink
 * @property string $originalPrice
 * @property string $discountPercent
 * @property string $discountPrice
 * @property string $boughtCount
 * @property string $sourceServiceCategories
 * @property string $pageLink
 *
 * @property City $city
 * @property SourceService $sourceService
 */
class Coupon extends \yii\db\ActiveRecord
{
    public $fullTextStr;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'coupon';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sourceServiceId', 'cityId'], 'integer'],
            [['createTimestamp', 'lastUpdateDateTime'], 'safe'],
            [['longDescription', 'conditions', 'features', 'imagesLinks'], 'string'],
            [['recordHash', 'title', 'shortDescription', 'timeToCompletion', 'mainImageLink', 'originalPrice', 'discountPercent', 'discountPrice', 'boughtCount', 'sourceServiceCategories', 'pageLink'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'sourceServiceId' => Yii::t('app', 'Source Service ID'),
            'cityId' => Yii::t('app', 'Город'),
            'createTimestamp' => Yii::t('app', 'Create Timestamp'),
            'lastUpdateDateTime' => Yii::t('app', 'Last Update Date Time'),
            'recordHash' => Yii::t('app', 'Record Hash'),
            'title' => Yii::t('app', 'Title'),
            'shortDescription' => Yii::t('app', 'Short Description'),
            'longDescription' => Yii::t('app', 'Long Description'),
            'conditions' => Yii::t('app', 'Conditions'),
            'features' => Yii::t('app', 'Features'),
            'imagesLinks' => Yii::t('app', 'Images Links'),
            'timeToCompletion' => Yii::t('app', 'Time To Completion'),
            'mainImageLink' => Yii::t('app', 'Main Image Link'),
            'originalPrice' => Yii::t('app', 'Original Price'),
            'discountPercent' => Yii::t('app', 'Discount Percent'),
            'discountPrice' => Yii::t('app', 'Discount Price'),
            'boughtCount' => Yii::t('app', 'Bought Count'),
            'sourceServiceCategories' => Yii::t('app', 'Source Service Categories'),
            'pageLink' => Yii::t('app', 'Page Link'),
            'fullTextStr' => 'Полнотекстовый поиск (наименование, описание, условия и особенности)'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'cityId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSourceService()
    {
        return $this->hasOne(SourceService::className(), ['id' => 'sourceServiceId']);
    }
}
