<?php

declare(strict_types=1);

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Notificação in-app de um utilizador.
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $event_id
 * @property string $type
 * @property string $message
 * @property string $scheduled_at
 * @property string|null $sent_at
 * @property string|null $read_at
 * @property string $status
 * @property int $created_at
 * @property int $updated_at
 * @property-read User|null $user
 * @property-read Event|null $event
 */
class Notification extends ActiveRecord
{
    public const STATUS_PENDING = 'PENDING';
    public const STATUS_SENT = 'SENT';
    public const STATUS_FAILED = 'FAILED';

    public const TYPE_APPLICATION_SUBMITTED = 'APPLICATION_SUBMITTED';
    public const TYPE_APPLICATION_APPROVED = 'APPLICATION_APPROVED';
    public const TYPE_APPLICATION_REJECTED = 'APPLICATION_REJECTED';
    public const TYPE_APPLICATION_WITHDRAWN = 'APPLICATION_WITHDRAWN';
    public const TYPE_EVENT_CANCELLED = 'EVENT_CANCELLED';
    public const TYPE_EVENT_REMINDER = 'EVENT_REMINDER';

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%notification}}';
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
            [['user_id', 'type', 'message', 'scheduled_at'], 'required'],
            [['user_id', 'event_id'], 'integer'],
            ['type', 'in', 'range' => [
                self::TYPE_APPLICATION_SUBMITTED,
                self::TYPE_APPLICATION_APPROVED,
                self::TYPE_APPLICATION_REJECTED,
                self::TYPE_APPLICATION_WITHDRAWN,
                self::TYPE_EVENT_CANCELLED,
                self::TYPE_EVENT_REMINDER,
            ]],
            ['message', 'string', 'max' => 500],
            [['scheduled_at', 'sent_at', 'read_at'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            ['status', 'default', 'value' => self::STATUS_PENDING],
            ['status', 'in', 'range' => [
                self::STATUS_PENDING,
                self::STATUS_SENT,
                self::STATUS_FAILED,
            ]],
            ['user_id', 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id'],
            [
                'event_id',
                'exist',
                'targetClass' => Event::class,
                'targetAttribute' => 'id',
                'skipOnEmpty' => true,
            ],
        ];
    }

    /**
     * Utilizador que recebe a notificação.
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Evento associado à notificação, quando existe.
     */
    public function getEvent(): ActiveQuery
    {
        return $this->hasOne(Event::class, ['id' => 'event_id']);
    }
}
