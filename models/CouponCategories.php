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
 * @property integer $isActive
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
            [['sourceServiceId', 'isActive'], 'integer'],
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
            'id' => Yii::t('app', 'ID'),
            'sourceServiceId' => Yii::t('app', 'Source Service ID'),
            'categoryName' => Yii::t('app', 'Category Name'),
            'categoryCode' => Yii::t('app', 'Category Code'),
            'categoryIdentifier' => Yii::t('app', 'Category Identifier'),
            'parentCategoryIdentifier' => Yii::t('app', 'Parent Category Identifier'),
            'categoryAdditionalInfo' => Yii::t('app', 'Category Additional Info'),
            'isActive' => Yii::t('app', 'Is Active'),
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
