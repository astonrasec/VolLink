<?php

use yii\db\Migration;

/**
 * Adds the columns required by the back-office/frontoffice UI:
 * - shift.name (optional label, e.g. "Turno da manhã")
 * - user_details.suspension_reason (back-office suspension modal)
 * - notification.read_at (unread/read state; null = unread)
 */
class m261024_160000_add_ui_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            '{{%shift}}',
            'name',
            $this->string(50)->null()->after('event_id')
        );

        $this->addColumn(
            '{{%user_details}}',
            'suspension_reason',
            $this->string(255)->null()->after('suspended_until')
        );

        $this->addColumn(
            '{{%notification}}',
            'read_at',
            $this->dateTime()->null()->after('sent_at')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%notification}}', 'read_at');
        $this->dropColumn('{{%user_details}}', 'suspension_reason');
        $this->dropColumn('{{%shift}}', 'name');
    }
}
