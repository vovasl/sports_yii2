<?php

use yii\db\Migration;

/**
 * Class m240109_133345_player_total_add_favorite
 */
class m240109_133345_player_total_add_favorite extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%tn_player_total}}', 'favorite', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%tn_player_total}}', 'favorite');
    }

}
