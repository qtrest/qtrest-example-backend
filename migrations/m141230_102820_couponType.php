<?php

use yii\db\Schema;
use yii\db\Migration;

class m141230_102820_couponType extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%couponType}}', [
            'id' => Schema::TYPE_PK,
            'couponTypeName' => Schema::TYPE_STRING . ' NOT NULL',
            'couponTypeCode' => Schema::TYPE_STRING . ' NOT NULL',
        ], $tableOptions);

        $this->insert('{{%couponType}}', [
            'couponTypeName' => 'Купон',
            'couponTypeCode' => 'coupon',
        ]);
        $this->insert('{{%couponType}}', [
            'couponTypeName' => 'Бесплатный купон',
            'couponTypeCode' => 'freeCoupon',
        ]);
        $this->insert('{{%couponType}}', [
            'couponTypeName' => 'Сертификат',
            'couponTypeCode' => 'full',
        ]);
        $this->insert('{{%couponType}}', [
            'couponTypeName' => 'Не определено',
            'couponTypeCode' => 'undefined',
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%couponType}}');
    }
}
