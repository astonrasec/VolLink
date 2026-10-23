<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%notification}}`.
 */
class m261022_203918_create_notification_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%notification}}', [
            'id'           => $this->primaryKey(),
            'user_id'      => $this->integer()->notNull(),
            'event_id'     => $this->integer(),
            'type'         => $this->string(40)->notNull(),
            'message'      => $this->string(500)->notNull(),
            'scheduled_at' => $this->dateTime()->notNull(),
            'sent_at'      => $this->dateTime(),
            'status'       => $this->string(15)->notNull()->defaultValue('PENDING'),
            'created_at'   => $this->integer()->notNull(),
            'updated_at'   => $this->integer()->notNull(),
        ]);

        $this->addForeignKey('fk-notification-user_id',  '{{%notification}}', 'user_id',  '{{%user}}',  'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-notification-event_id', '{{%notification}}', 'event_id', '{{%event}}', 'id', 'SET NULL','CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-notification-user_id',  '{{%notification}}');
        $this->dropForeignKey('fk-notification-event_id', '{{%notification}}');
        $this->dropTable('{{%notification}}');
    }
}
