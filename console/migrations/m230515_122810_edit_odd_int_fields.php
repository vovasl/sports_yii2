<?php

use yii\db\Migration;

/**
 * Class m230515_122810_edit_odd_int_fields
 */
class m230515_122810_edit_odd_int_fields extends Migration
{

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->alterColumn('{{%sp_odd}}', 'odd', $this->integer()->notNull());
        $this->alterColumn('{{%sp_odd}}', 'profit', $this->integer());
    }

    public function down()
    {
        $this->alterColumn('{{%sp_odd}}', 'odd', $this->string()->notNull());
        $this->alterColumn('{{%sp_odd}}', 'profit', $this->string());
    }
}
