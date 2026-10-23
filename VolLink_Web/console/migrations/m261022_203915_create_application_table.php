<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%application}}`.
 */
class m261022_203915_create_application_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%application}}', [
            'id'           => $this->primaryKey(),
            'user_id'      => $this->integer()->notNull(),
            'event_id'     => $this->integer()->notNull(),
            'submitted_at' => $this->dateTime()->notNull(),
            'status'       => $this->string(15)->notNull()->defaultValue('PENDING'),
            'created_at'   => $this->integer()->notNull(),
            'updated_at'   => $this->integer()->notNull(),
        ]);

        $this->createIndex('uq-application-user-event', '{{%application}}', ['user_id', 'event_id'], true);

        $this->addForeignKey('fk-application-user_id',  '{{%application}}', 'user_id',  '{{%user}}',  'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-application-event_id', '{{%application}}', 'event_id', '{{%event}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-application-user_id',  '{{%application}}');
        $this->dropForeignKey('fk-application-event_id', '{{%application}}');
        $this->dropTable('{{%application}}');
    }
}
