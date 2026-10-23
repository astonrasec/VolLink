<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shift}}`.
 */
class m261022_203912_create_shift_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shift}}', [
            'id'         => $this->primaryKey(),
            'event_id'   => $this->integer()->notNull(),
            'start_time' => $this->dateTime()->notNull(),
            'end_time'   => $this->dateTime()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey('fk-shift-event_id', '{{%shift}}', 'event_id', '{{%event}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-shift-event_id', '{{%shift}}');
        $this->dropTable('{{%shift}}');
    }
}
