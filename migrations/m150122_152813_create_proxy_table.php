<?php

use yii\db\Schema;
use yii\db\Migration;

class m150122_152813_create_proxy_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%proxy}}', [
            'id' => Schema::TYPE_PK,
            'createTimestamp' => Schema::TYPE_TIMESTAMP,
            'ip' => Schema::TYPE_STRING,
            'port' => Schema::TYPE_INTEGER

        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%proxy}}');
    }
}
