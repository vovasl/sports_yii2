<?php

use yii\db\Migration;

/**
 * Class m230603_144200_event_add_column_sofascore_id
 */
class m230603_144200_event_add_column_sofascore_id extends Migration
{

    public function up()
    {
        $this->addColumn('{{%tn_event}}', 'sofa_id', $this->integer());
        $this->createIndex('idx-event-sofa_id', '{{%tn_event}}', 'sofa_id');
    }

    public function down()
    {
        $this->dropColumn('{{%tn_event}}', 'sofa_id');
    }

}
