<?php

use yii\db\Migration;

/**
 * Class m230624_163558_event_add_column_has_odd
 */
class m230624_163558_event_add_column_has_odd extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tn_event}}', 'has_odd', $this->smallInteger(1)->notNull()->defaultValue(1));
        $this->createIndex('idx-event-has_odd', '{{%tn_event}}', 'has_odd');
    }

    public function down()
    {
        $this->dropColumn('{{%tn_event}}', 'has_odd');
    }
}
