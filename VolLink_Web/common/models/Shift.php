<?php

declare(strict_types=1);

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Turno de um evento.
 *
 * @property int $id
 * @property int $event_id
 * @property string|null $name
 * @property string $start_time
 * @property string $end_time
 * @property int $capacity
 * @property int $created_at
 * @property int $updated_at
 * @property-read Event|null $event
 * @property-read Application[] $applications
 * @property-read Attendance[] $attendances
 */
class Shift extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%shift}}';
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
            [['event_id', 'start_time', 'end_time', 'capacity'], 'required'],
            [['event_id', 'capacity'], 'integer'],
            ['capacity', 'integer', 'min' => 1],
            ['name', 'string', 'max' => 50],
            [['start_time', 'end_time'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            ['event_id', 'exist', 'targetClass' => Event::class, 'targetAttribute' => 'id'],
            ['end_time', 'validatePeriod'],
        ];
    }

    /**
     * O turno tem de terminar depois de começar e ficar dentro das datas do evento.
     */
    public function validatePeriod(string $attribute): void
    {
        if ($this->hasErrors('start_time') || $this->hasErrors('end_time') || $this->hasErrors('event_id')) {
            return;
        }

        $start = strtotime((string) $this->start_time);
        $end = strtotime((string) $this->end_time);

        if ($start === false || $end === false) {
            return;
        }

        if ($start >= $end) {
            $this->addError($attribute, 'A hora de fim tem de ser posterior à hora de início.');

            return;
        }

        $event = $this->event;

        if ($event === null) {
            return;
        }

        $eventStart = strtotime((string) $event->start_time);
        $eventEnd = strtotime((string) $event->end_time);

        if ($eventStart === false || $eventEnd === false) {
            return;
        }

        if ($start < $eventStart || $end > $eventEnd) {
            $this->addError($attribute, 'O turno tem de estar dentro das datas do evento.');
        }
    }

    /**
     * Número de candidaturas aprovadas no turno.
     */
    public function getApprovedApplicationsCount(): int
    {
        return (int) $this->getApplications()
            ->andWhere(['status' => Application::STATUS_APPROVED])
            ->count();
    }

    /**
     * Vagas disponíveis: capacidade do turno menos candidaturas aprovadas.
     */
    public function getAvailableSeats(): int
    {
        return max(0, (int) $this->capacity - $this->getApprovedApplicationsCount());
    }

    /**
     * Evento a que o turno pertence.
     */
    public function getEvent(): ActiveQuery
    {
        return $this->hasOne(Event::class, ['id' => 'event_id']);
    }

    /**
     * Candidaturas ao turno.
     */
    public function getApplications(): ActiveQuery
    {
        return $this->hasMany(Application::class, ['shift_id' => 'id']);
    }

    /**
     * Presenças registadas neste turno.
     */
    public function getAttendances(): ActiveQuery
    {
        return $this->hasMany(Attendance::class, ['shift_id' => 'id']);
    }
}
