<?php

declare(strict_types=1);

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Presença e horas de um voluntário num turno.
 *
 * @property int $id
 * @property int $application_id
 * @property int $shift_id
 * @property string $hours_worked
 * @property bool $confirmed
 * @property int|null $confirmed_by_id
 * @property string|null $confirmed_at
 * @property int $created_at
 * @property int $updated_at
 * @property-read Application|null $application
 * @property-read Shift|null $shift
 * @property-read User|null $confirmedBy
 */
class Attendance extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%attendance}}';
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
            [['application_id', 'shift_id'], 'required'],
            [['application_id', 'shift_id', 'confirmed_by_id'], 'integer'],
            ['hours_worked', 'default', 'value' => 0],
            ['hours_worked', 'number', 'min' => 0],
            ['confirmed', 'default', 'value' => false],
            ['confirmed', 'boolean'],
            ['confirmed_at', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            ['application_id', 'exist', 'targetClass' => Application::class, 'targetAttribute' => 'id'],
            ['shift_id', 'exist', 'targetClass' => Shift::class, 'targetAttribute' => 'id'],
            [
                'confirmed_by_id',
                'exist',
                'targetClass' => User::class,
                'targetAttribute' => 'id',
                'skipOnEmpty' => true,
            ],
            [
                ['confirmed_by_id', 'confirmed_at'],
                'required',
                'when' => fn (): bool => (bool) $this->confirmed,
            ],
            [
                ['application_id', 'shift_id'],
                'unique',
                'targetClass' => self::class,
                'message' => 'Já existe uma presença para esta candidatura e turno.',
            ],
            ['shift_id', 'validateApplicationShift'],
        ];
    }

    /**
     * O turno da presença tem de ser o turno da candidatura.
     */
    public function validateApplicationShift(string $attribute): void
    {
        if ($this->hasErrors($attribute) || $this->hasErrors('application_id')) {
            return;
        }

        $application = $this->application;

        if ($application === null) {
            return;
        }

        if ((int) $application->shift_id !== (int) $this->shift_id) {
            $this->addError($attribute, 'O turno da presença tem de coincidir com o turno da candidatura.');
        }
    }

    /**
     * Candidatura a que a presença pertence.
     */
    public function getApplication(): ActiveQuery
    {
        return $this->hasOne(Application::class, ['id' => 'application_id']);
    }

    /**
     * Turno em que a presença foi registada.
     */
    public function getShift(): ActiveQuery
    {
        return $this->hasOne(Shift::class, ['id' => 'shift_id']);
    }

    /**
     * Coordenador que confirmou a presença.
     */
    public function getConfirmedBy(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'confirmed_by_id']);
    }
}
