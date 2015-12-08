<?php

use yii\db\Schema;
use yii\db\Migration;

class m151208_162620_Add_ViewCount_field extends Migration
{
    public function up()
    {
        $this->addColumn('{{%coupon}}', 'viewCount', 'varchar(10) DEFAULT "0"');
    }

    public function down()
    {
        $this->dropColumn('{{%coupon}}', 'viewCount');

        return true;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
