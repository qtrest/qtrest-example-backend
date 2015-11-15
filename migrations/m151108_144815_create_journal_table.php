<?php

use yii\db\Schema;
use yii\db\Migration;

class m151108_144815_create_journal_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%statistics}}', [
            'source' => Schema::TYPE_STRING,
            'alias' => Schema::TYPE_STRING,
            'count' => Schema::TYPE_INTEGER,
            'PRIMARY KEY (`source`)'

        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%statistics}}');
    }
}
