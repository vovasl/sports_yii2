<?php

use yii\db\Migration;

/**
 * Class m230515_113957_edit_odd
 */
class m230515_113957_edit_odd extends Migration
{

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->alterColumn('{{%sp_odd}}', 'value', $this->string());
        $this->addColumn('{{%sp_odd}}', 'player_id', $this->integer()->after('type'));
        $this->addColumn('{{%sp_odd}}', 'profit', $this->string());

        $this->createIndex('idx-odd-player_id', '{{%sp_odd}}', 'player_id');

        $this->addForeignKey('fk-odd-player_id', '{{sp_odd}}', 'player_id', '{{%tn_player}}', 'id');
    }

    public function down()
    {
        $this->alterColumn('{{%sp_odd}}', 'value', $this->string()->notNull());
        $this->dropColumn('{{%sp_odd}}', 'player_id');
        $this->dropColumn('{{%sp_odd}}', 'profit');
    }

}
