<?php

use yii\db\Migration;

/**
 * Class m230513_183006_add_column_pin_id
 */
class m230513_183006_add_column_pin_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%tn_event}}', 'pin_id', $this->integer());
        $this->createIndex('idx-event-pin_id', '{{%tn_event}}', 'pin_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%tn_event}}', 'pin_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230513_183006_add_column_pin_id cannot be reverted.\n";

        return false;
    }
    */
}
