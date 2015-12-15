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
 * @property string $discountType
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
            [['recordHash', 'title', 'shortDescription', 'timeToCompletion', 'mainImageLink',
                'originalPrice', 'discountPercent', 'discountType', 'discountPrice', 'boughtCount',
                'sourceServiceCategories', 'pageLink'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'Идентификатор'),
            'sourceServiceId' => Yii::t('app', 'Купонатор'),
            'cityId' => Yii::t('app', 'Город'),
            'createTimestamp' => Yii::t('app', 'Дата и время создания (UTC)'),
            'lastUpdateDateTime' => Yii::t('app', 'Дата и время последнего обновления (UTC)'),
            'recordHash' => Yii::t('app', 'Хеш записи'),
            'title' => Yii::t('app', 'Заголовок'),
            'shortDescription' => Yii::t('app', 'Краткое описание'),
            'longDescription' => Yii::t('app', 'Полное описание'),
            'conditions' => Yii::t('app', 'Условия'),
            'features' => Yii::t('app', 'Особенности'),
            'imagesLinks' => Yii::t('app', 'Ссылки на изображения'),
            'timeToCompletion' => Yii::t('app', 'Время до завершения'),
            'mainImageLink' => Yii::t('app', 'Ссылка на главное изображение'),
            'originalPrice' => Yii::t('app', 'Оригинальная цена'),
            'discountPercent' => Yii::t('app', 'Процент скидки'),
            'discountPrice' => Yii::t('app', 'Цена по скидке'),
            'discountType' => Yii::t('app', 'Тип скидки'),
            'boughtCount' => Yii::t('app', 'Количество купивших'),
            'sourceServiceCategories' => Yii::t('app', 'Категории'),
            'pageLink' => Yii::t('app', 'Прямая ссылка на страницу'),
            'fullTextStr' => 'Полнотекстовый поиск (наименование, описание, условия и особенности)',
            'isArchive' => 'Архив'
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
