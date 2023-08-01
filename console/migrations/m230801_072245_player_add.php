<?php

use yii\db\Migration;

/**
 * Class m230801_072245_player_add
 */
class m230801_072245_player_add extends Migration
{

    public function up()
    {
        $this->createTable('{{%tn_player_add}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()
        ]);
        $this->createIndex('idx-player_add-name', '{{%tn_player_add}}', 'name');

        $this->createTable('{{%tn_player_add_event}}', [
            'id' => $this->primaryKey(),
            'player_id' => $this->integer()->notNull(),
            'date' => $this->timestamp()->defaultValue(NULL)
        ]);
        $this->addForeignKey('fk-player_add_event-player_id', '{{%tn_player_add_event}}', 'player_id', '{{%tn_player_add}}', 'id');
    }

    public function down()
    {
        $this->dropTable('{{%tn_player_add}}');
        $this->dropTable('{{%tn_player_add_event}}');
    }
}
