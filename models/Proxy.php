<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "proxy".
 *
 * @property integer $id
 * @property string $createTimestamp
 * @property string $ip
 * @property integer $port
 */
class Proxy extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'proxy';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['createTimestamp'], 'safe'],
            [['port'], 'integer'],
            [['ip'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'createTimestamp' => 'Create Timestamp',
            'ip' => 'Ip',
            'port' => 'Port',
        ];
    }

    public static function getRandom($count=1)
    {
        $proxyList = static::find()->all();
        $rKeys = array_rand($proxyList, $count);
        if($count>1) {
            foreach ($rKeys as $k) {
                $proxy = $proxyList[$k];
                $result[] = $proxy->ip.':'.$proxy->port;
            }
        } else {
            $proxy = $proxyList[$rKeys];
            $result[] = $proxy->ip.':'.$proxy->port;
        }
        return $result;
    }
}
