<?php

use yii\db\Migration;

/**
 * Class m230512_091225_pn_settings
 */
class m230512_091225_pn_settings extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('{{%pn_settings}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'value' => $this->string()->notNull()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('{{%pn_settings}}');
    }

}
