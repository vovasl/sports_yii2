<?php

use yii\db\Migration;

/**
 * Class m240110_193805_statistic_type
 */
class m240110_193805_statistic_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%tn_statistic}}', 'type', $this->integer()->after('event_id'));

        $this->addForeignKey('fk-statistic-type', '{{%tn_statistic}}', 'type', '{{%sp_odd_type}}', 'id', 'CASCADE', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%tn_statistic}}', 'type');
    }

}
