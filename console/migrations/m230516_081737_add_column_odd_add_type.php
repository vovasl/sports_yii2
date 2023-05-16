<?php

use yii\db\Migration;

/**
 * Class m230516_081737_add_column_odd_add_type
 */
class m230516_081737_add_column_odd_add_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('{{%sp_odd}}', 'add_type', $this->string()->after('type'));

        $this->createIndex('idx-odd-add_type', '{{%sp_odd}}', 'add_type');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropColumn('{{%sp_odd}}', 'add_type');
    }

}
