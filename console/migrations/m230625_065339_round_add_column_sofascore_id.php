<?php

use yii\db\Migration;

/**
 * Class m230625_065339_round_add_column_sofascore_id
 */
class m230625_065339_round_add_column_sofascore_id extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tn_round}}', 'sofa_id', $this->integer());
        $this->createIndex('idx-round-sofa_id', '{{%tn_round}}', 'sofa_id');
    }

    public function down()
    {
        $this->dropColumn('{{%tn_round}}', 'sofa_id');
    }
}
