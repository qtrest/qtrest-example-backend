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
            'id' => $this->primaryKey(),
            'sourceId' => $this->integer(),
            'createDate' => $this->date(),
            'alias' => $this->string(),
            'codeType' => $this->string()->notNull(),//new or archive
            'count' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('source', '{{%statistics}}', 'sourceId', '{{%sourceService}}', 'id');
    }

    public function down()
    {
        $this->dropTable('{{%statistics}}');
    }
}
