<?php

use yii\db\Schema;
use yii\db\Migration;

class m160127_175349_add_active_column_to_categories_table extends Migration
{
    public function up()
    {
        $this->addColumn('categories', 'isActive', $this->integer(1)->notNull()->defaultValue(0));
        $this->createIndex('sourceServiceCategories', 'coupon', 'sourceServiceCategories');
    }

    public function down()
    {
        $this->dropColumn('categories', 'isActive');
        $this->dropIndex('coupon', 'sourceServiceCategories');
    }
}
