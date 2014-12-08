<?php

use yii\db\Schema;
use yii\db\Migration;

class m141207_082233_create_cityUrls_table extends Migration
{
    public function up()
    {
		$tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%cityUrl}}', [
            'id' => Schema::TYPE_PK,
            'cityId' => Schema::TYPE_INTEGER,
            'url' => Schema::TYPE_STRING . ' NOT NULL',
            'path' => Schema::TYPE_STRING . ' NOT NULL',
            'lastUpdateDateTime' => Schema::TYPE_DATETIME,
            'sourceServiceId' => Schema::TYPE_INTEGER,
        ], $tableOptions);

        $this->createIndex('FK_cityUrl_city', '{{%cityUrl}}', 'cityId');
    	$this->addForeignKey(
            'FK_cityUrl_city', '{{%cityUrl}}', 'cityId', '{{%city}}', 'id', 'SET NULL', 'CASCADE'
        );

        $this->createIndex('FK_cityUrl_service', '{{%cityUrl}}', 'sourceServiceId');
    	$this->addForeignKey(
            'FK_cityUrl_service', '{{%cityUrl}}', 'sourceServiceId', '{{%sourceService}}', 'id', 'SET NULL', 'CASCADE'
        );
    }

    public function down()
    {
        $this->dropTable('{{%cityUrl}}');
    }
}
