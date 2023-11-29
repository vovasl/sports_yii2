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
            'tour_id' => $this->integer(),
            'surface_id' => $this->integer(),
            'five_sets' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'type' => $this->string(),
            'min_moneyline' => $this->integer(),
            'profit_0' => $this->integer(),
            'profit_1' => $this->integer(),
            'profit_2' => $this->integer(),
            'profit_3' => $this->integer(),
            'profit_4' => $this->integer(),
        ]);

        $this->createIndex('idx-total-type', '{{%sp_total}}', 'type');

        $this->addForeignKey('fk-total-player_id', '{{%sp_total}}', 'player_id', '{{%tn_player}}', 'id', 'SET NULL', 'RESTRICT');
        $this->addForeignKey('fk-total-tour_id', '{{%sp_total}}', 'tour_id', '{{%tn_tour}}', 'id', 'SET NULL', 'RESTRICT');
        $this->addForeignKey('fk-total-surface_id', '{{%sp_total}}', 'surface_id', '{{%tn_surface}}', 'id', 'SET NULL', 'RESTRICT');
        $this->addForeignKey('fk-total-event_id', '{{%sp_total}}', 'event_id', '{{%tn_event}}', 'id', 'SET NULL', 'RESTRICT');

    }

    public function down()
    {
        $this->dropTable('{{%sp_total}}');
    }
}
