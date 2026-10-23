<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%event}}`.
 */
class m261022_203630_create_event_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%event}}', [
            'id'                   => $this->primaryKey(),
            'creator_id'           => $this->integer()->notNull(),
            'title'                => $this->string(150)->notNull(),
            'description'          => $this->text()->notNull(),
            'category'             => $this->string(50)->notNull(),
            'location'             => $this->string(150)->notNull(),
            'required_skills'      => $this->string(255),
            'start_time'           => $this->dateTime()->notNull(),
            'end_time'             => $this->dateTime()->notNull(),
            'application_deadline' => $this->dateTime()->notNull(),
            'capacity'             => $this->integer()->notNull(),
            'status'               => $this->string(15)->notNull()->defaultValue('DRAFT'),
            'created_at'           => $this->integer()->notNull(),
            'updated_at'           => $this->integer()->notNull(),
        ]);

        // RESTRICT: impede apagar um user que criou eventos
        $this->addForeignKey(
            'fk-event-creator_id',
            '{{%event}}', 'creator_id',
            '{{%user}}', 'id',
            'RESTRICT', 'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%event}}');
    }
}
