<?php

use yii\db\Migration;

/**
 * Class m230625_083422_round_add_column_sofascore_name
 */
class m230625_083422_round_add_column_sofascore_name extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tn_round}}', 'sofa_name', $this->string());
        $this->createIndex('idx-round-sofa_name', '{{%tn_round}}', 'sofa_name');
    }

    public function down()
    {
        $this->dropColumn('{{%tn_round}}', 'sofa_name');
    }
}
