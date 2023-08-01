<?php

use yii\db\Migration;

/**
 * Class m230801_100852_player_add_event_add_column_sofa_id
 */
class m230801_100852_player_add_event_add_column_sofa_id extends Migration
{

    public function up()
    {
        $this->addColumn('{{%tn_player_add_event}}', 'sofa_id', $this->integer());
        $this->createIndex('idx-player_add_event-sofa_id','{{%tn_player_add_event}}','sofa_id');
    }

    public function down()
    {
        $this->dropColumn('{{%tn_player_add_event}}', 'sofa_id');
    }

}
