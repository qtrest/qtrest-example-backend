<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "statistics".
 *
 * @property string $source
 * @property string $alias
 * @property integer $count
 */
class Statistics extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'statistics';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['source'], 'required'],
            [['count'], 'integer'],
            [['source', 'alias'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'source' => Yii::t('app', 'Source'),
            'alias' => Yii::t('app', 'Alias'),
            'count' => Yii::t('app', 'Count'),
        ];
    }
}
