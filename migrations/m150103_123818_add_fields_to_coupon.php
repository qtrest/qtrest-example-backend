<?php

use yii\db\Schema;
use yii\db\Migration;

class m150103_123818_add_fields_to_coupon extends Migration
{
    public function up()
    {
        $this->addColumn('{{%coupon}}', 'isArchive', 'boolean DEFAULT 0');
        $this->addColumn('{{%coupon}}', 'tryToUpdateCount', 'smallint DEFAULT 0');
    }

    public function down()
    {
        $this->dropColumn('{{%coupon}}', 'isArchive');
        $this->dropColumn('{{%coupon}}', 'tryToUpdateCount');
    }
}
