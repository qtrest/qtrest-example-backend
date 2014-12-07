<?php

use yii\db\Schema;
use yii\db\Migration;

class m141207_071530_create_basic_tables extends Migration
{
    public function up()
    {
    	$tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

    	$this->createTable('{{%city}}', [
            'id' => Schema::TYPE_PK,
            'cityName' => Schema::TYPE_STRING . ' NOT NULL',
            'cityCode' => Schema::TYPE_STRING . ' NOT NULL',
        ], $tableOptions);

    	$this->createTable('{{%sourceService}}', [
            'id' => Schema::TYPE_PK,
            'serviceName' => Schema::TYPE_STRING . ' NOT NULL',
            'serviceCode' => Schema::TYPE_STRING . ' NOT NULL',
        ], $tableOptions);

    }

    public function down()
    {
    	$this->dropTable('{{%sourceService}}');
        $this->dropTable('{{%city}}');
    }
}
