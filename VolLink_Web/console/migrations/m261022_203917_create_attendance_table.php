<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%attendance}}`.
 */
class m261022_203917_create_attendance_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%attendance}}', [
            'id'              => $this->primaryKey(),
            'application_id'  => $this->integer()->notNull(),
            'shift_id'        => $this->integer()->notNull(),
            'hours_worked'    => $this->decimal(5, 2)->notNull()->defaultValue(0),
            'confirmed'       => $this->boolean()->notNull()->defaultValue(false),
            'confirmed_by_id' => $this->integer(),
            'confirmed_at'    => $this->dateTime(),
            'created_at'      => $this->integer()->notNull(),
            'updated_at'      => $this->integer()->notNull(),
        ]);

        $this->createIndex('uq-attendance-application-shift', '{{%attendance}}', ['application_id', 'shift_id'], true);

        $this->addForeignKey('fk-attendance-application_id', '{{%attendance}}', 'application_id', '{{%application}}', 'id', 'CASCADE',   'CASCADE');
        $this->addForeignKey('fk-attendance-shift_id',       '{{%attendance}}', 'shift_id',       '{{%shift}}',      'id', 'CASCADE',   'CASCADE');
        $this->addForeignKey('fk-attendance-confirmed_by_id','{{%attendance}}', 'confirmed_by_id','{{%user}}',       'id', 'SET NULL',  'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-attendance-application_id',   '{{%attendance}}');
        $this->dropForeignKey('fk-attendance-shift_id',         '{{%attendance}}');
        $this->dropForeignKey('fk-attendance-confirmed_by_id',  '{{%attendance}}');
        $this->dropTable('{{%attendance}}');
    }
}
