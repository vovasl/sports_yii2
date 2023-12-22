<?php

use yii\db\Migration;

/**
 * Class m231222_213250_odd_move_value_type
 */
class m231222_213250_odd_move_value_type extends Migration
{

    public function up()
    {
        $this->addColumn('{{%sp_odd_move}}', 'value_type', $this->smallInteger(1)->after('value'));
        $this->createIndex('idx-odd_move-value_type', '{{%sp_odd_move}}', 'value_type');
    }

    public function down()
    {
        $this->dropColumn('{{%sp_odd_move}}', 'value_type');
    }
}
