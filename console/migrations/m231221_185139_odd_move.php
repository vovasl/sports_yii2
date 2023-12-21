<?php

use yii\db\Migration;

/**
 * Class m231221_185139_odd_move
 */
class m231221_185139_odd_move extends Migration
{

    public function up()
    {
        $this->createTable('{{%sp_odd_move}}', [
            'id' => $this->primaryKey(),
            'event_id' => $this->integer()->notNull(),
            'type_id' => $this->integer(),
            'add_type' => $this->string(),
            'value' => $this->integer(),
            'status' => $this->smallInteger(1)->notNull()->defaultValue(1),
        ]);

        $this->createIndex('idx-odd_move-status', '{{%sp_odd_move}}', 'status');

        $this->addForeignKey('fk-odd_move-event', '{{%sp_odd_move}}', 'event_id', '{{%tn_event}}', 'id', 'CASCADE', 'RESTRICT');
        $this->addForeignKey('fk-odd_move-type', '{{%sp_odd_move}}', 'type_id', '{{%sp_odd_type}}', 'id', 'SET NULL', 'RESTRICT');
    }

    public function down()
    {
        $this->dropTable('{{%sp_odd_move}}');
    }
}
