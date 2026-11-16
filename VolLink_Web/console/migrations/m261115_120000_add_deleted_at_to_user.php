<?php

use yii\db\Migration;

class m261115_120000_add_deleted_at_to_user extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'deleted_at', $this->integer()->null()->after('updated_at'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'deleted_at');
    }
}
