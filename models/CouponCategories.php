<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "categories".
 *
 * @property integer $id
 * @property integer $sourceServiceId
 * @property string $categoryName
 * @property string $categoryCode
 * @property string $categoryIdentifier
 * @property string $parentCategoryIdentifier
 * @property string $categoryAdditionalInfo
 *
 * @property SourceService $sourceService
 */
class CouponCategories extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'categories';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sourceServiceId'], 'integer'],
            [['categoryName', 'categoryCode', 'categoryIdentifier', 'parentCategoryIdentifier'], 'required'],
            [['categoryName', 'categoryCode', 'categoryIdentifier', 'parentCategoryIdentifier', 'categoryAdditionalInfo'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('city', 'ID'),
            'sourceServiceId' => Yii::t('city', 'Source Service ID'),
            'categoryName' => Yii::t('city', 'Category Name'),
            'categoryCode' => Yii::t('city', 'Category Code'),
            'categoryIdentifier' => Yii::t('city', 'Category Identifier'),
            'parentCategoryIdentifier' => Yii::t('city', 'Parent Category Identifier'),
            'categoryAdditionalInfo' => Yii::t('city', 'Category Additional Info'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSourceService()
    {
        return $this->hasOne(SourceService::className(), ['id' => 'sourceServiceId']);
    }
}
