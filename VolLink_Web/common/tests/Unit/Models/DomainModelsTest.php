<?php

declare(strict_types=1);

namespace common\tests\Unit\Models;

use Codeception\Test\Unit;
use common\models\Application;
use common\models\Attendance;
use common\models\Event;
use common\models\Notification;
use common\models\Shift;
use common\models\User;
use common\models\UserDetails;
use common\tests\Support\UnitTester;

final class DomainModelsTest extends Unit
{
    protected UnitTester $tester;

    private User $_company;
    private User $_volunteer;

    public function _before(): void
    {
        $this->clearDomainTables();

        $this->_company = new User([
            'username' => 'empresa.teste',
            'email' => 'empresa.teste@example.com',
            'status' => User::STATUS_ACTIVE,
        ]);
        $this->_company->setPassword('Password123');
        $this->_company->generateAuthKey();
        verify($this->_company->save(false))->true();

        $this->_volunteer = new User([
            'username' => 'voluntario.teste',
            'email' => 'voluntario.teste@example.com',
            'status' => User::STATUS_ACTIVE,
        ]);
        $this->_volunteer->setPassword('Password123');
        $this->_volunteer->generateAuthKey();
        verify($this->_volunteer->save(false))->true();
    }

    public function _after(): void
    {
        $this->clearDomainTables();
        User::deleteAll();
    }

    public function testEventDefaultsAndRelations(): void
    {
        $event = $this->createEvent();
        $shift = $this->createShift($event);

        verify($event->status)->equals(Event::STATUS_DRAFT);
        verify($event->created_at)->notEmpty();
        verify($event->updated_at)->notEmpty();
        verify($event->creator->id)->equals($this->_company->id);
        verify($event->getShifts()->count())->equals(1);
        verify($event->getApplications()->count())->equals(0);
        verify($shift->event->id)->equals($event->id);
        verify($this->_company->getCreatedEvents()->count())->equals(1);
        verify($event->getOrganizerName())->equals('empresa.teste');
        verify($event->isOrganizerVerified())->false();
    }

    public function testPublishedEventsAndAvailableSeats(): void
    {
        $published = $this->createEvent(['status' => Event::STATUS_PUBLISHED]);
        $this->createEvent(['status' => Event::STATUS_DRAFT, 'title' => 'Rascunho']);

        verify(Event::findPublished()->count())->equals(1);
        verify(Event::publishedCategories())->equals(['Ambiente']);

        $shift = $this->createShift($published);

        $application = new Application([
            'user_id' => $this->_volunteer->id,
            'event_id' => $published->id,
            'shift_id' => $shift->id,
            'status' => Application::STATUS_APPROVED,
        ]);
        verify($application->save())->true();

        verify($published->getAvailableSeats())->equals(49);
        verify($shift->getAvailableSeats())->equals(19);
    }

    public function testShiftMustStayInsideEventDates(): void
    {
        $event = $this->createEvent();

        $beforeEvent = new Shift([
            'event_id' => $event->id,
            'start_time' => '2026-11-24 08:00:00',
            'end_time' => '2026-11-24 10:00:00',
            'capacity' => 10,
        ]);
        verify($beforeEvent->validate())->false();
        verify($beforeEvent->getFirstError('end_time'))->notEmpty();

        $afterEvent = new Shift([
            'event_id' => $event->id,
            'start_time' => '2026-11-24 12:00:00',
            'end_time' => '2026-11-24 14:00:00',
            'capacity' => 10,
        ]);
        verify($afterEvent->validate())->false();

        $inverted = new Shift([
            'event_id' => $event->id,
            'start_time' => '2026-11-24 13:00:00',
            'end_time' => '2026-11-24 09:00:00',
            'capacity' => 10,
        ]);
        verify($inverted->validate())->false();

        $inside = new Shift([
            'event_id' => $event->id,
            'start_time' => '2026-11-24 09:00:00',
            'end_time' => '2026-11-24 13:00:00',
            'capacity' => 10,
        ]);
        verify($inside->validate())->true();
    }

    public function testApplicationMustMatchShiftEvent(): void
    {
        $eventA = $this->createEvent();
        $shiftA = $this->createShift($eventA);

        $eventB = $this->createEvent([
            'title' => 'Banco alimentar',
            'start_time' => '2026-12-01 09:00:00',
            'end_time' => '2026-12-01 13:00:00',
            'application_deadline' => '2026-11-29 23:59:59',
        ]);
        $shiftB = $this->createShift($eventB, [
            'start_time' => '2026-12-01 09:00:00',
            'end_time' => '2026-12-01 13:00:00',
        ]);

        $wrongEvent = new Application([
            'user_id' => $this->_volunteer->id,
            'event_id' => $eventA->id,
            'shift_id' => $shiftB->id,
        ]);
        verify($wrongEvent->validate())->false();
        verify($wrongEvent->getFirstError('event_id'))->notEmpty();

        $application = new Application([
            'user_id' => $this->_volunteer->id,
            'event_id' => $eventA->id,
            'shift_id' => $shiftA->id,
        ]);
        verify($application->save())->true();
        verify($application->status)->equals(Application::STATUS_PENDING);
        verify($application->submitted_at)->notEmpty();
        verify($application->user->id)->equals($this->_volunteer->id);
        verify($application->event->id)->equals($eventA->id);
        verify($application->shift->id)->equals($shiftA->id);
        verify($this->_volunteer->getApplications()->count())->equals(1);
        verify($eventA->getApplications()->count())->equals(1);
        verify($shiftA->getApplications()->count())->equals(1);

        $duplicate = new Application([
            'user_id' => $this->_volunteer->id,
            'event_id' => $eventA->id,
            'shift_id' => $shiftA->id,
        ]);
        verify($duplicate->validate())->false();
        verify($duplicate->getFirstError('user_id'))->notEmpty();
    }

    public function testAttendanceMustMatchApplicationShift(): void
    {
        $event = $this->createEvent(['end_time' => '2026-11-24 18:00:00']);
        $shiftA = $this->createShift($event);
        $shiftB = $this->createShift($event, [
            'name' => 'Turno da tarde',
            'start_time' => '2026-11-24 14:00:00',
            'end_time' => '2026-11-24 18:00:00',
            'capacity' => 10,
        ]);

        $application = new Application([
            'user_id' => $this->_volunteer->id,
            'event_id' => $event->id,
            'shift_id' => $shiftA->id,
        ]);
        verify($application->save())->true();

        $wrongShift = new Attendance([
            'application_id' => $application->id,
            'shift_id' => $shiftB->id,
            'hours_worked' => 4,
        ]);
        verify($wrongShift->validate())->false();
        verify($wrongShift->getFirstError('shift_id'))->notEmpty();

        $attendance = new Attendance([
            'application_id' => $application->id,
            'shift_id' => $shiftA->id,
            'hours_worked' => 4,
        ]);
        verify($attendance->save())->true();
        verify((float) $attendance->hours_worked)->equals(4.0);
        verify((bool) $attendance->confirmed)->false();
        verify($attendance->application->id)->equals($application->id);
        verify($attendance->shift->id)->equals($shiftA->id);
        verify($application->attendance->id)->equals($attendance->id);
        verify($shiftA->getAttendances()->count())->equals(1);

        $confirmed = new Attendance([
            'application_id' => $application->id,
            'shift_id' => $shiftA->id,
            'hours_worked' => 4,
            'confirmed' => true,
        ]);
        verify($confirmed->validate())->false();
        verify($confirmed->getFirstError('confirmed_by_id'))->notEmpty();
        verify($confirmed->getFirstError('confirmed_at'))->notEmpty();
    }

    public function testAttendanceConfirmedByRelation(): void
    {
        $event = $this->createEvent();
        $shift = $this->createShift($event);

        $application = new Application([
            'user_id' => $this->_volunteer->id,
            'event_id' => $event->id,
            'shift_id' => $shift->id,
        ]);
        verify($application->save())->true();

        $attendance = new Attendance([
            'application_id' => $application->id,
            'shift_id' => $shift->id,
            'hours_worked' => 4,
            'confirmed' => true,
            'confirmed_by_id' => $this->_company->id,
            'confirmed_at' => '2026-11-24 18:00:00',
        ]);
        verify($attendance->save())->true();
        verify($attendance->confirmedBy->id)->equals($this->_company->id);
        verify($this->_company->getConfirmedAttendances()->count())->equals(1);
    }

    public function testNotificationTypesStatusAndRelations(): void
    {
        $event = $this->createEvent();

        $notification = new Notification([
            'user_id' => $this->_volunteer->id,
            'event_id' => $event->id,
            'type' => Notification::TYPE_APPLICATION_SUBMITTED,
            'message' => 'A tua candidatura foi recebida.',
            'scheduled_at' => '2026-11-20 10:00:00',
        ]);
        verify($notification->save())->true();
        verify($notification->status)->equals(Notification::STATUS_PENDING);
        verify($notification->created_at)->notEmpty();
        verify($notification->updated_at)->notEmpty();
        verify($notification->user->id)->equals($this->_volunteer->id);
        verify($notification->event?->id)->equals($event->id);
        verify($this->_volunteer->getNotifications()->count())->equals(1);
        verify($event->getNotifications()->count())->equals(1);

        $invalid = new Notification([
            'user_id' => $this->_volunteer->id,
            'type' => 'UNKNOWN',
            'message' => 'Mensagem',
            'scheduled_at' => '2026-11-20 10:00:00',
        ]);
        verify($invalid->validate())->false();
        verify($invalid->getFirstError('type'))->notEmpty();
    }

    public function testUserDetailsRelationBothWays(): void
    {
        $details = new UserDetails([
            'user_id' => $this->_company->id,
            'name' => 'Empresa Teste',
            'account_type' => UserDetails::TYPE_COMPANY,
            'account_status' => UserDetails::STATUS_APPROVED,
        ]);
        verify($details->save())->true();
        verify($details->created_at)->notEmpty();
        verify($details->user->id)->equals($this->_company->id);
        verify($this->_company->details?->id)->equals($details->id);
        verify($this->_company->isCompany())->true();
        verify($this->_company->canCreateEvents())->true();

        $event = $this->createEvent();
        verify($event->getOrganizerName())->equals('Empresa Teste');
        verify($event->isOrganizerVerified())->true();
    }

    private function createEvent(array $attributes = []): Event
    {
        $event = new Event(array_merge([
            'creator_id' => $this->_company->id,
            'title' => 'Limpeza da praia',
            'description' => 'Uma atividade aberta à comunidade.',
            'category' => 'Ambiente',
            'location' => 'Leiria',
            'start_time' => '2026-11-24 09:00:00',
            'end_time' => '2026-11-24 13:00:00',
            'application_deadline' => '2026-11-22 23:59:59',
            'capacity' => 50,
        ], $attributes));

        verify($event->save())->true();

        return $event;
    }

    private function createShift(Event $event, array $attributes = []): Shift
    {
        $shift = new Shift(array_merge([
            'event_id' => $event->id,
            'name' => 'Turno da manhã',
            'start_time' => '2026-11-24 09:00:00',
            'end_time' => '2026-11-24 13:00:00',
            'capacity' => 20,
        ], $attributes));

        verify($shift->save())->true();

        return $shift;
    }

    private function clearDomainTables(): void
    {
        Attendance::deleteAll();
        Application::deleteAll();
        Notification::deleteAll();
        Shift::deleteAll();
        Event::deleteAll();
        UserDetails::deleteAll();
    }
}
