<?php

use yii\db\Migration;

/**
 * Class m230625_065353_tournament_add_column_sofascore_id
 */
class m230625_065353_tournament_add_column_sofascore_id extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tn_tournament}}', 'sofa_id', $this->integer());
        $this->createIndex('idx-tournament-sofa_id', '{{%tn_tournament}}', 'sofa_id');
    }

    public function down()
    {
        $this->dropColumn('{{%tn_tournament}}', 'sofa_id');
    }
}
