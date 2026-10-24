<?php

use yii\db\Migration;

/**
 * Adds the core columns required by the MVP.
 *
 * - shift.capacity
 * - user_details.suspended_until / last_login_at
 * - user_details.description / website / location
 */
class m261024_130000_add_core_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            '{{%shift}}',
            'capacity',
            $this->integer()->notNull()->defaultValue(1)->after('end_time')
        );

        $this->addColumn(
            '{{%user_details}}',
            'suspended_until',
            $this->dateTime()->null()->after('rejection_reason')
        );
        $this->addColumn(
            '{{%user_details}}',
            'last_login_at',
            $this->dateTime()->null()->after('suspended_until')
        );
        $this->addColumn(
            '{{%user_details}}',
            'description',
            $this->string(500)->null()->after('skills')
        );
        $this->addColumn(
            '{{%user_details}}',
            'website',
            $this->string(255)->null()->after('description')
        );
        $this->addColumn(
            '{{%user_details}}',
            'location',
            $this->string(150)->null()->after('website')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_details}}', 'location');
        $this->dropColumn('{{%user_details}}', 'website');
        $this->dropColumn('{{%user_details}}', 'description');
        $this->dropColumn('{{%user_details}}', 'last_login_at');
        $this->dropColumn('{{%user_details}}', 'suspended_until');
        $this->dropColumn('{{%shift}}', 'capacity');
    }
}
