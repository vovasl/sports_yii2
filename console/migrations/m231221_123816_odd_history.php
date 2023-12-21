<?php

use yii\db\Migration;

/**
 * Class m231221_123816_odd_history
 */
class m231221_123816_odd_history extends Migration
{

    public function up()
    {
        $this->createTable('{{%sp_odd_history}}', [
            'id' => $this->primaryKey(),
            'event' => $this->integer()->notNull(),
            'type' => $this->integer(),
            'add_type' => $this->string(),
            'player_id' => $this->integer(),
            'value' => $this->string(),
            'odd' => $this->string()->notNull(),
            'created_at' => $this->timestamp()->defaultValue(NULL)
        ]);

        $this->addForeignKey('fk-odd-history-event', '{{%sp_odd_history}}', 'event', '{{%tn_event}}', 'id', 'CASCADE', 'RESTRICT');
        $this->addForeignKey('fk-odd-history-type', '{{%sp_odd_history}}', 'type', '{{%sp_odd_type}}', 'id', 'SET NULL', 'RESTRICT');
        $this->addForeignKey('fk-odd-history-player_id', '{{sp_odd_history}}', 'player_id', '{{%tn_player}}', 'id', 'CASCADE', 'RESTRICT');
    }

    public function down()
    {
        $this->dropTable('{{%sp_odd_history}}');
    }
}
