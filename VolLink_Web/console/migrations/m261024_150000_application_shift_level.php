<?php

use yii\db\Migration;

/**
 * Shift-level applications.
 *
 * A volunteer now applies to a specific shift:
 * - application.shift_id is required;
 * - unique (user_id, shift_id) prevents duplicate applications to the same shift;
 * - the old unique (user_id, event_id) is removed, so a volunteer can apply to
 *   multiple shifts of the same event.
 *
 * application.event_id is kept for queries and reporting. The model must ensure
 * that shift.event_id matches application.event_id.
 */
class m261024_150000_application_shift_level extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            '{{%application}}',
            'shift_id',
            $this->integer()->notNull()->after('event_id')
        );

        // A FK de user_id usa o índice único antigo (user_id, event_id).
        // Criar primeiro o novo único (user_id, shift_id) para o substituir.
        $this->createIndex(
            'uq-application-user-shift',
            '{{%application}}',
            ['user_id', 'shift_id'],
            true
        );
        $this->dropIndex('uq-application-user-event', '{{%application}}');

        $this->addForeignKey(
            'fk-application-shift_id',
            '{{%application}}',
            'shift_id',
            '{{%shift}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-application-shift_id', '{{%application}}');
        // Recriar o índice antigo antes de remover o novo (a FK de user_id precisa de um).
        $this->createIndex(
            'uq-application-user-event',
            '{{%application}}',
            ['user_id', 'event_id'],
            true
        );
        $this->dropIndex('uq-application-user-shift', '{{%application}}');
        $this->dropColumn('{{%application}}', 'shift_id');
    }
}
