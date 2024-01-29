<?php

use yii\db\Migration;

/**
 * Class m240129_182023_event_favorite
 */
class m240129_182023_event_favorite extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%tn_event}}', 'favorite', $this->integer()->after('away'));

        $this->addForeignKey('fk-event-favorite', '{{%tn_event}}', 'favorite', '{{%tn_player}}', 'id', 'SET NULL', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('tn_event', 'favorite');
    }

}
