<?php

use yii\db\Migration;

/**
 * Class m230518_174602_add_column_round_position
 */
class m230518_174602_add_column_round_position extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('{{%tn_round}}', 'rank', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropColumn('{{%tn_round}}', 'rank');
    }

}
