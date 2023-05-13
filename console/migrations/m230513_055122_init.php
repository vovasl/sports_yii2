<?php

use yii\db\Migration;

/**
 * Class m230513_055122_init
 */
class m230513_055122_init extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%sp_sport}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()
        ]);

        $this->createTable('{{%sp_odd_type}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()
        ]);

        $this->createTable('{{%tn_tour}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()
        ]);

        $this->createTable('{{%tn_surface}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()
        ]);

        $this->createTable('{{%tn_round}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
        ]);

        $this->createTable('{{%tn_player_type}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()
        ]);

        $this->createTable('{{%tn_tournament}}', [
            'id' => $this->primaryKey(),
            'tour' => $this->integer(),
            'name' => $this->string()->notNull(),
            'surface' => $this->integer(),
            'comment' => $this->text()
        ]);

        $this->createIndex('idx-tournament-name', '{{%tn_tournament}}', 'name');

        $this->addForeignKey('fk-tournament-tour', '{{%tn_tournament}}', 'tour', '{{%tn_tour}}', 'id', 'SET NULL', 'RESTRICT');
        $this->addForeignKey('fk-tournament-surface', '{{%tn_tournament}}', 'surface', '{{%tn_surface}}', 'id', 'SET NULL', 'RESTRICT');

        $this->createTable('{{%tn_player}}', [
            'id' => $this->primaryKey(),
            'type' => $this->integer(),
            'name' => $this->string()->notNull(),
            'birthday' => $this->timestamp()->defaultValue(NULL),
            'plays' => $this->string(),
            'comment' => $this->text()
        ]);

        $this->createIndex('idx-player-name', '{{%tn_player}}', 'name');

        $this->addForeignKey('fk-player-type', '{{tn_player}}', 'type', '{{%tn_player_type}}', 'id', 'SET NULL', 'RESTRICT');

        $this->createTable('{{%tn_event}}', [
            'id' => $this->primaryKey(),
            'start_at' => $this->timestamp()->defaultValue(NULL),
            'tournament' => $this->integer(),
            'round' => $this->integer(),
            'home' => $this->integer(),
            'away' => $this->integer(),
            'home_result' => $this->integer(),
            'away_result' => $this->integer(),
            'winner' => $this->integer(),
            'total' => $this->integer(),
            'status' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'total_games' => $this->integer(),
            'five_sets' => $this->smallInteger(1)->notNull()->defaultValue(0)
        ]);

        $this->createIndex('idx-event-status', '{{%tn_event}}', 'status');

        $this->addForeignKey('fk-event-home', '{{%tn_event}}', 'home', '{{%tn_player}}', 'id', 'SET NULL', 'RESTRICT');
        $this->addForeignKey('fk-event-away', '{{%tn_event}}', 'away', '{{%tn_player}}', 'id', 'SET NULL', 'RESTRICT');
        $this->addForeignKey('fk-event-winner', '{{%tn_event}}', 'winner', '{{%tn_player}}', 'id', 'SET NULL', 'RESTRICT');
        $this->addForeignKey('fk-event-tournament', '{{%tn_event}}', 'tournament', '{{%tn_tournament}}', 'id', 'SET NULL', 'RESTRICT');
        $this->addForeignKey('fk-event-round', '{{%tn_event}}', 'round', '{{%tn_round}}', 'id', 'SET NULL', 'RESTRICT');

        $this->createTable('{{%tn_result_set}}', [
            'id' => $this->primaryKey(),
            'event' => $this->integer()->notNull(),
            'set' => $this->smallInteger(1)->notNull(),
            'home' => $this->integer(),
            'away' => $this->integer()
        ]);

        $this->createIndex('idx-result_set-set', '{{%tn_result_set}}', 'set');

        $this->addForeignKey('fk-result_set-event', '{{%tn_result_set}}', 'event', '{{%tn_event}}', 'id', 'CASCADE', 'RESTRICT');

        $this->createTable('{{%sp_odd}}', [
            'id' => $this->primaryKey(),
            'event' => $this->integer()->notNull(),
            'type' => $this->integer(),
            'value' => $this->string()->notNull(),
            'odd' => $this->string()->notNull(),
            'created_at' => $this->timestamp()->defaultValue(NULL)
        ]);

        $this->addForeignKey('fk-odd-event', '{{%sp_odd}}', 'event', '{{%tn_event}}', 'id', 'CASCADE', 'RESTRICT');
        $this->addForeignKey('fk-odd-type', '{{%sp_odd}}', 'type', '{{%sp_odd_type}}', 'id', 'SET NULL', 'RESTRICT');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%sp_odd}}');
        $this->dropTable('{{%tn_result_set}}');
        $this->dropTable('{{%tn_event}}');
        $this->dropTable('{{%tn_tournament}}');
        $this->dropTable('{{%tn_player}}');

        $this->dropTable('{{%sp_sport}}');
        $this->dropTable('{{%sp_odd_type}}');
        $this->dropTable('{{%tn_tour}}');
        $this->dropTable('{{%tn_surface}}');
        $this->dropTable('{{%tn_round}}');
        $this->dropTable('{{%tn_player_type}}');
    }

}
