<?php

use yii\db\Migration;

/**
 * Class m231128_174507_total
 */
class m231128_174507_total extends Migration
{

    public function up()
    {
        $this->createTable('{{%sp_total}}', [
            'id' => $this->primaryKey(),
            'player_id' => $this->integer(),
            'event_id' => $this->integer(),
            'type' => $this->string(),
            'min_moneyline' => $this->integer(),
            'odd_id_0' => $this->integer(),
            'odd_id_1' => $this->integer(),
            'odd_id_2' => $this->integer(),
            'odd_id_3' => $this->integer(),
            'odd_id_4' => $this->integer(),
            'profit_0' => $this->integer(),
            'profit_1' => $this->integer(),
            'profit_2' => $this->integer(),
            'profit_3' => $this->integer(),
            'profit_4' => $this->integer(),
        ]);

        $this->createIndex('idx-total-type', '{{%sp_total}}', 'type');

        $this->addForeignKey('fk-total-player_id', '{{%sp_total}}', 'player_id', '{{%tn_player}}', 'id', 'SET NULL', 'RESTRICT');
        $this->addForeignKey('fk-total-event_id', '{{%sp_total}}', 'event_id', '{{%tn_event}}', 'id', 'SET NULL', 'RESTRICT');
        $this->addForeignKey('fk-total-odd_id_0', '{{%sp_total}}', 'odd_id_0', '{{%sp_odd}}', 'id', 'SET NULL', 'RESTRICT');
        $this->addForeignKey('fk-total-odd_id_1', '{{%sp_total}}', 'odd_id_1', '{{%sp_odd}}', 'id', 'SET NULL', 'RESTRICT');
        $this->addForeignKey('fk-total-odd_id_2', '{{%sp_total}}', 'odd_id_2', '{{%sp_odd}}', 'id', 'SET NULL', 'RESTRICT');
        $this->addForeignKey('fk-total-odd_id_3', '{{%sp_total}}', 'odd_id_3', '{{%sp_odd}}', 'id', 'SET NULL', 'RESTRICT');
        $this->addForeignKey('fk-total-odd_id_4', '{{%sp_total}}', 'odd_id_4', '{{%sp_odd}}', 'id', 'SET NULL', 'RESTRICT');
    }

    public function down()
    {
        $this->dropTable('{{%sp_total}}');
    }
}
