<?php

use yii\db\Schema;
use yii\db\Migration;

class m141207_102420_create_categories_table extends Migration
{
    public function up()
    {
    	$tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%categories}}', [
            'id' => Schema::TYPE_PK,
			'sourceServiceId' => Schema::TYPE_INTEGER,
            'categoryName' => Schema::TYPE_STRING . ' NOT NULL',
            'categoryCode' => Schema::TYPE_STRING . ' NOT NULL',
            'categoryIdentifier' => Schema::TYPE_STRING . ' NOT NULL',
            'parentCategoryIdentifier' => Schema::TYPE_STRING . ' NOT NULL',
        ], $tableOptions);

        $this->createIndex('FK_categories_service', '{{%categories}}', 'sourceServiceId');
    	$this->addForeignKey(
            'FK_categories_service', '{{%categories}}', 'sourceServiceId', '{{%sourceService}}', 'id', 'SET NULL', 'CASCADE'
        );
    }

    public function down()
    {
        $this->dropTable('{{%categories}}');
    }
}
