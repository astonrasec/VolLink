<?php

declare(strict_types=1);

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Evento de voluntariado.
 *
 * @property int $id
 * @property int $creator_id
 * @property string $title
 * @property string $description
 * @property string $category
 * @property string $location
 * @property string|null $required_skills
 * @property string $start_time
 * @property string $end_time
 * @property string $application_deadline
 * @property int $capacity
 * @property string $status
 * @property int $created_at
 * @property int $updated_at
 * @property-read User|null $creator
 * @property-read Shift[] $shifts
 * @property-read Application[] $applications
 * @property-read Notification[] $notifications
 */
class Event extends ActiveRecord
{
    public const STATUS_DRAFT = 'DRAFT';
    public const STATUS_PUBLISHED = 'PUBLISHED';
    public const STATUS_CANCELLED = 'CANCELLED';
    public const STATUS_COMPLETED = 'COMPLETED';
    public const STATUS_ARCHIVED = 'ARCHIVED';

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%event}}';
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
            [
                [
                    'creator_id',
                    'title',
                    'description',
                    'category',
                    'location',
                    'start_time',
                    'end_time',
                    'application_deadline',
                    'capacity',
                ],
                'required',
            ],
            [['creator_id', 'capacity'], 'integer'],
            ['capacity', 'integer', 'min' => 1],
            ['title', 'string', 'max' => 150],
            ['description', 'string'],
            ['category', 'string', 'max' => 50],
            ['location', 'string', 'max' => 150],
            ['required_skills', 'string', 'max' => 255],
            [['start_time', 'end_time', 'application_deadline'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            ['status', 'default', 'value' => self::STATUS_DRAFT],
            ['status', 'in', 'range' => [
                self::STATUS_DRAFT,
                self::STATUS_PUBLISHED,
                self::STATUS_CANCELLED,
                self::STATUS_COMPLETED,
                self::STATUS_ARCHIVED,
            ]],
            ['creator_id', 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id'],
            ['end_time', 'validatePeriod'],
        ];
    }

    /**
     * Garante que o evento termina depois de começar e que o prazo de
     * candidatura não passa do início.
     */
    public function validatePeriod(string $attribute): void
    {
        if ($this->hasErrors('start_time') || $this->hasErrors('end_time') || $this->hasErrors('application_deadline')) {
            return;
        }

        $start = strtotime((string) $this->start_time);
        $end = strtotime((string) $this->end_time);
        $deadline = strtotime((string) $this->application_deadline);

        if ($start === false || $end === false || $deadline === false) {
            return;
        }

        if ($start >= $end) {
            $this->addError($attribute, 'A data de início tem de ser anterior à data de fim.');
        }

        if ($deadline > $start) {
            $this->addError('application_deadline', 'O prazo de candidatura tem de terminar até ao início do evento.');
        }
    }

    /**
     * Empresa que criou o evento.
     */
    public function getCreator(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'creator_id']);
    }

    /**
     * Turnos do evento.
     */
    public function getShifts(): ActiveQuery
    {
        return $this->hasMany(Shift::class, ['event_id' => 'id']);
    }

    /**
     * Candidaturas ao evento.
     */
    public function getApplications(): ActiveQuery
    {
        return $this->hasMany(Application::class, ['event_id' => 'id']);
    }

    /**
     * Notificações associadas ao evento.
     */
    public function getNotifications(): ActiveQuery
    {
        return $this->hasMany(Notification::class, ['event_id' => 'id']);
    }
}
