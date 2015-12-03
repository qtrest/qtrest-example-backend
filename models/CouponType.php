<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "couponType".
 *
 * @property integer $id
 * @property string $couponTypeName
 * @property string $couponTypeCode
 */
class CouponType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'couponType';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['couponTypeName', 'couponTypeCode'], 'required'],
            [['couponTypeName', 'couponTypeCode'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('city', 'ID'),
            'couponTypeName' => Yii::t('city', 'Coupon Type Name'),
            'couponTypeCode' => Yii::t('city', 'Coupon Type Code'),
        ];
    }
}
