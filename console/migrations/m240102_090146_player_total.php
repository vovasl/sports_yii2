<?php

use yii\db\Migration;

/**
 * Class m240102_090146_player_total
 */
class m240102_090146_player_total extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%tn_player_total}}', [
            'id' => $this->primaryKey(),
            'player_id' => $this->integer()->notNull(),
            'tour_id' => $this->integer()->notNull(),
            'surface_id' => $this->integer()->notNull(),
            'type' => $this->string()->notNull(),
        ]);

        $this->addForeignKey('fk-player_total-player', '{{%tn_player_total}}', 'player_id', '{{%tn_player}}', 'id', 'CASCADE', 'RESTRICT');
        $this->addForeignKey('fk-player_total-tour', '{{%tn_player_total}}', 'tour_id', '{{%tn_tour}}', 'id', 'CASCADE', 'RESTRICT');
        $this->addForeignKey('fk-player_total-surface', '{{%tn_player_total}}', 'surface_id', '{{%tn_surface}}', 'id', 'CASCADE', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%tn_player_total}}');
    }

}
