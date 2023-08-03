<?php

use yii\db\Migration;

/**
 * Class m230803_140812_event_add_column_created_at
 */
class m230803_140812_event_add_column_created_at extends Migration
{

    public function up()
    {
        $this->addColumn('{{%tn_event}}', 'created', $this->timestamp()->defaultValue(NULL));
    }

    public function down()
    {
        $this->dropColumn('{{%tn_event}}', 'created');
    }

}
