<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_details}}`.
 */
class m261022_203230_create_user_details_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_details}}', [
            'id'               => $this->primaryKey(),
            'user_id'          => $this->integer()->notNull()->unique(),
            'name'             => $this->string(100)->notNull(),
            'account_type'     => $this->string(15)->notNull()->defaultValue('VOLUNTEER'),
            'birth_date'       => $this->date(),
            'skills'           => $this->string(255),
            'email_verified'   => $this->boolean()->notNull()->defaultValue(false),
            'account_status'   => $this->string(15)->notNull()->defaultValue('PENDING'),
            'rejection_reason' => $this->string(255),
            'created_at'       => $this->integer()->notNull(),
            'updated_at'       => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk-user_details-user_id',
            '{{%user_details}}', 'user_id',
            '{{%user}}', 'id',
            'CASCADE', 'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-user_details-user_id', '{{%user_details}}');
        $this->dropTable('{{%user_details}}');
    }
}
