<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "statistics".
 *
 * @property integer $id
 * @property integer $sourceId
 * @property string $createDate
 * @property string $alias
 * @property string $codeType
 * @property integer $count
 *
 * @property SourceService $source
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
            [['sourceId', 'count'], 'integer'],
            [['createDate'], 'safe'],
            [['codeType'], 'required'],
            [['alias', 'codeType'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sourceId' => 'Source ID',
            'createDate' => 'Create Date',
            'alias' => 'Alias',
            'codeType' => 'Code Type',
            'count' => 'Count',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSource()
    {
        return $this->hasOne(SourceService::className(), ['id' => 'sourceId']);
    }
}
