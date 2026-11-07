<?php

declare(strict_types=1);

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Candidatura de um voluntário a um turno.
 *
 * @property int $id
 * @property int $user_id
 * @property int $event_id
 * @property int $shift_id
 * @property string|null $submitted_at
 * @property string $status
 * @property int $created_at
 * @property int $updated_at
 * @property-read User|null $user
 * @property-read Event|null $event
 * @property-read Shift|null $shift
 * @property-read Attendance|null $attendance
 */
class Application extends ActiveRecord
{
    public const STATUS_PENDING = 'PENDING';
    public const STATUS_APPROVED = 'APPROVED';
    public const STATUS_REJECTED = 'REJECTED';
    public const STATUS_WITHDRAWN = 'WITHDRAWN';

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%application}}';
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
    public function beforeValidate(): bool
    {
        if ($this->isNewRecord && ($this->submitted_at === null || $this->submitted_at === '')) {
            $this->submitted_at = date('Y-m-d H:i:s');
        }

        return parent::beforeValidate();
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['user_id', 'event_id', 'shift_id', 'submitted_at'], 'required'],
            [['user_id', 'event_id', 'shift_id'], 'integer'],
            ['submitted_at', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            ['status', 'default', 'value' => self::STATUS_PENDING],
            ['status', 'in', 'range' => [
                self::STATUS_PENDING,
                self::STATUS_APPROVED,
                self::STATUS_REJECTED,
                self::STATUS_WITHDRAWN,
            ]],
            ['user_id', 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id'],
            ['event_id', 'exist', 'targetClass' => Event::class, 'targetAttribute' => 'id'],
            ['shift_id', 'exist', 'targetClass' => Shift::class, 'targetAttribute' => 'id'],
            [['user_id', 'shift_id'], 'unique', 'targetClass' => self::class, 'message' => 'Já existe uma candidatura a este turno.'],
            ['event_id', 'validateShiftEvent'],
        ];
    }

    /**
     * O evento da candidatura tem de ser o evento do turno escolhido.
     */
    public function validateShiftEvent(string $attribute): void
    {
        if ($this->hasErrors($attribute) || $this->hasErrors('shift_id')) {
            return;
        }

        $shift = $this->shift;

        if ($shift === null) {
            return;
        }

        if ((int) $shift->event_id !== (int) $this->event_id) {
            $this->addError($attribute, 'O evento da candidatura tem de coincidir com o evento do turno.');
        }
    }

    /**
     * Voluntário que se candidatou.
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Evento a que a candidatura se refere.
     */
    public function getEvent(): ActiveQuery
    {
        return $this->hasOne(Event::class, ['id' => 'event_id']);
    }

    /**
     * Turno a que a candidatura se refere.
     */
    public function getShift(): ActiveQuery
    {
        return $this->hasOne(Shift::class, ['id' => 'shift_id']);
    }

    /**
     * Presença registada para a candidatura.
     */
    public function getAttendance(): ActiveQuery
    {
        return $this->hasOne(Attendance::class, ['application_id' => 'id']);
    }
}
