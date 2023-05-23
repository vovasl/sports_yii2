<?php

use yii\db\Migration;

/**
 * Class m230523_192000_add_event_logs
 */
class m230523_192000_add_event_logs extends Migration
{

    public function up()
    {
        $this->createTable('{{%sp_event_log}}', [
            'id' => $this->primaryKey(),
            'created_at' => $this->timestamp()->notNull(),
            'event_id' => $this->integer()->notNull(),
            'message' => $this->text()
        ]);

        $this->addForeignKey('fk-event_log-event_id', '{{%sp_event_log}}', 'event_id', '{{%tn_event}}', 'id', 'CASCADE', 'RESTRICT');
    }

    public function down()
    {
        $this->dropTable('{{%sp_event_log}}');
    }
}
