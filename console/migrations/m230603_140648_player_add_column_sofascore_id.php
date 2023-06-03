<?php

use yii\db\Migration;

/**
 * Class m230603_140648_player_add_column_sofascore_id
 */
class m230603_140648_player_add_column_sofascore_id extends Migration
{

    public function up()
    {
        $this->addColumn('{{%tn_player}}', 'sofa_id', $this->integer());
        $this->createIndex('idx-player-sofa_id', '{{%tn_player}}', 'sofa_id');
    }

    public function down()
    {
        $this->dropColumn('{{%tn_player}}', 'sofa_id');
    }

}
