<?php

use yii\db\Migration;

/**
 * Class m230628_112655_event_drop_column_has_odd
 */
class m230628_112655_event_drop_column_has_odd extends Migration
{
    public function up()
    {
        $this->dropColumn('{{%tn_event}}', 'has_odd');
    }

    public function down()
    {
    }

}
