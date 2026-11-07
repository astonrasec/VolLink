<?php

declare(strict_types=1);

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Perfil e estado de aprovação de um utilizador (relação 1:1 com User).
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $account_type
 * @property string|null $birth_date
 * @property string|null $skills
 * @property string|null $description
 * @property string|null $website
 * @property string|null $location
 * @property bool $email_verified
 * @property string $account_status
 * @property string|null $rejection_reason
 * @property string|null $suspended_until
 * @property string|null $suspension_reason
 * @property string|null $last_login_at
 * @property int $created_at
 * @property int $updated_at
 * @property-read User|null $user
 */
class UserDetails extends ActiveRecord
{
    public const TYPE_VOLUNTEER = 'VOLUNTEER';
    public const TYPE_COMPANY = 'COMPANY';
    public const TYPE_ADMIN = 'ADMIN';

    public const STATUS_PENDING = 'PENDING';
    public const STATUS_APPROVED = 'APPROVED';
    public const STATUS_REJECTED = 'REJECTED';
    public const STATUS_SUSPENDED = 'SUSPENDED';

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%user_details}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['user_id', 'name'], 'required'],
            [['user_id'], 'integer'],
            [['user_id'], 'unique'],
            [['email_verified'], 'boolean'],
            [['birth_date', 'suspended_until', 'last_login_at'], 'safe'],
            ['account_type', 'in', 'range' => [self::TYPE_VOLUNTEER, self::TYPE_COMPANY, self::TYPE_ADMIN]],
            ['account_status', 'in', 'range' => [
                self::STATUS_PENDING,
                self::STATUS_APPROVED,
                self::STATUS_REJECTED,
                self::STATUS_SUSPENDED,
            ]],
            [['name'], 'string', 'max' => 100],
            [['skills'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 500],
            [['website'], 'string', 'max' => 255],
            [['location'], 'string', 'max' => 150],
            [['rejection_reason', 'suspension_reason'], 'string', 'max' => 255],
        ];
    }

    /**
     * Utilizador a que este perfil pertence.
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Indica se a conta pertence a uma empresa.
     */
    public function isCompany(): bool
    {
        return $this->account_type === self::TYPE_COMPANY;
    }

    /**
     * Indica se a conta já foi aprovada pelo SysAdmin.
     */
    public function isApproved(): bool
    {
        return $this->account_status === self::STATUS_APPROVED;
    }
}
