<?php

use yii\db\Schema;
use yii\db\Migration;

class m141207_072100_create_coupon_table extends Migration
{
    public function up()
    {
    	$tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        		//sourceServiceId
        		//cityId
        		//createTimestamp
        		//lastUpdateDateTime
                //recordHash

				//title
                //shortDescription
                //longDescription
                //originalPrice
                //discountPercent
                //discountPrice
                //boughtCount
                //sourceServiceCategories
                //imagesLinks
        		//pageLink

                

    	$this->createTable('{{%coupon}}', [
            'id' => Schema::TYPE_PK,
            'sourceServiceId' => Schema::TYPE_INTEGER,
            'cityId' => Schema::TYPE_INTEGER,
            'createTimestamp' => Schema::TYPE_TIMESTAMP,
            'lastUpdateDateTime' => Schema::TYPE_DATETIME,
            'recordHash' => Schema::TYPE_STRING,
            
            'title' => Schema::TYPE_STRING,
            'shortDescription' => Schema::TYPE_STRING,

            'longDescription' => Schema::TYPE_TEXT,
            'conditions' => Schema::TYPE_TEXT,
            'features' => Schema::TYPE_TEXT,
            'imagesLinks' => Schema::TYPE_TEXT,
            'timeToCompletion' => Schema::TYPE_STRING,

            'mainImageLink' => Schema::TYPE_STRING,
            'originalPrice' => Schema::TYPE_STRING,
            'discountPercent' => Schema::TYPE_STRING,
            'discountPrice' => Schema::TYPE_STRING,
            'boughtCount' => Schema::TYPE_STRING,
            'sourceServiceCategories' => Schema::TYPE_STRING,
            'pageLink' => Schema::TYPE_STRING,

        ], $tableOptions);

    	$this->createIndex('FK_coupon_service', '{{%coupon}}', 'sourceServiceId');
    	$this->addForeignKey(
            'FK_coupon_service', '{{%coupon}}', 'sourceServiceId', '{{%sourceService}}', 'id', 'SET NULL', 'CASCADE'
        );

		$this->createIndex('FK_coupon_city', '{{%coupon}}', 'cityId');
    	$this->addForeignKey(
            'FK_coupon_city', '{{%coupon}}', 'cityId', '{{%city}}', 'id', 'SET NULL', 'CASCADE'
        );


    }

    public function down()
    {
        $this->dropTable('{{%coupon}}');
    }
}
