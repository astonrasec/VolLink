<?php

declare(strict_types=1);

namespace frontend\tests\Unit\Models;

use Codeception\Test\Unit;
use common\fixtures\UserFixture;
use common\models\User;
use common\models\UserDetails;
use frontend\models\SignupForm;
use frontend\tests\Support\UnitTester;
use Yii;
use yii\mail\MessageInterface;

final class SignupFormTest extends Unit
{
    protected UnitTester $tester;
    public function _before(): void
    {
        // user_details não tem fixture própria; limpar entre testes.
        UserDetails::deleteAll();
        Yii::$app->db->createCommand()->delete('{{%auth_assignment}}')->execute();

        $this->tester->haveFixtures(
            [
                'user' => [
                    'class' => UserFixture::class,
                    'dataFile' => codecept_data_dir() . 'user.php',
                ],
            ],
        );
    }

    public function testCorrectSignup(): void
    {
        $model = new SignupForm(
            [
                'username' => 'some_username',
                'email' => 'some_email@example.com',
                'password' => 'some_password',
            ],
        );

        $user = $model->signup(Yii::$app->mailer, Yii::$app->params['supportEmail'], Yii::$app->name);

        verify($user)->notEmpty();

        /** @var User $user */
        $user = $this->tester->grabRecord(
            User::class,
            [
                'username' => 'some_username',
                'email' => 'some_email@example.com',
                'status' => User::STATUS_INACTIVE,
            ],
        );

        $this->tester->seeEmailIsSent();
        $mail = $this->tester->grabLastSentEmail();

        verify($mail)
            ->instanceOf(MessageInterface::class);
        verify($mail->getTo())
            ->arrayHasKey('some_email@example.com');
        verify($mail->getFrom())
            ->arrayHasKey(Yii::$app->params['supportEmail']);
        verify($mail->getSubject())
            ->equals('Account registration at ' . Yii::$app->name);
        verify($mail->toString())
            ->stringContainsString($user->verification_token);
    }

    public function testCompanySignupStaysPending(): void
    {
        $model = new SignupForm(
            [
                'accountType' => SignupForm::ACCOUNT_COMPANY,
                'companyName' => 'Associação Horizonte',
                'email' => 'empresa@example.com',
                'password' => 'some_password',
            ],
        );

        verify($model->signup(Yii::$app->mailer, Yii::$app->params['supportEmail'], Yii::$app->name))
            ->notEmpty();

        $user = $this->tester->grabRecord(
            User::class,
            [
                'username' => 'empresa@example.com',
                'email' => 'empresa@example.com',
                'status' => User::STATUS_INACTIVE,
            ],
        );

        $details = $this->tester->grabRecord(UserDetails::class, ['user_id' => $user->id]);

        verify($details->name)->equals('Associação Horizonte');
        verify($details->account_type)->equals(UserDetails::TYPE_COMPANY);
        verify($details->account_status)->equals(UserDetails::STATUS_PENDING);
        verify((bool) $details->email_verified)->false();

        $this->tester->seeEmailIsSent();
    }

    public function testSignupSavesBirthDate(): void
    {
        $model = new SignupForm(
            [
                'username' => 'com_data',
                'email' => 'com_data@example.com',
                'password' => 'some_password',
                'birthDate' => '2001-07-12',
            ],
        );

        verify($model->signup(Yii::$app->mailer, Yii::$app->params['supportEmail'], Yii::$app->name))
            ->notEmpty();

        $user = $this->tester->grabRecord(User::class, ['username' => 'com_data']);
        $details = $this->tester->grabRecord(UserDetails::class, ['user_id' => $user->id]);

        verify($details->birth_date)->equals('2001-07-12');
    }

    public function testBirthDateCannotBeInTheFuture(): void
    {
        $model = new SignupForm(
            [
                'username' => 'futuro',
                'email' => 'futuro@example.com',
                'password' => 'some_password',
                'birthDate' => date('Y-m-d', strtotime('+1 day')),
            ],
        );

        verify($model->validate())->false();
        verify($model->getErrors())->arrayHasKey('birthDate');
    }

    public function testSignupWithoutEmailVerification(): void
    {
        Yii::$app->params['user.requireEmailVerification'] = false;

        $model = new SignupForm(
            [
                'username' => 'sem_email',
                'email' => 'sem_email@example.com',
                'password' => 'some_password',
            ],
        );

        verify($model->signup(Yii::$app->mailer, Yii::$app->params['supportEmail'], Yii::$app->name))
            ->notEmpty();

        $user = $this->tester->grabRecord(User::class, ['username' => 'sem_email']);
        $details = $this->tester->grabRecord(UserDetails::class, ['user_id' => $user->id]);

        verify($user->status)->equals(User::STATUS_ACTIVE);
        verify($details->account_status)->equals(UserDetails::STATUS_APPROVED);
        verify($user->canLogin())->true();
        $this->tester->dontSeeEmailIsSent();
    }

    public function testCompanyWithoutEmailVerificationWaitsForApproval(): void
    {
        Yii::$app->params['user.requireEmailVerification'] = false;

        $model = new SignupForm(
            [
                'accountType' => SignupForm::ACCOUNT_COMPANY,
                'companyName' => 'Grupo Sol',
                'email' => 'gruposol@example.com',
                'password' => 'some_password',
            ],
        );

        verify($model->signup(Yii::$app->mailer, Yii::$app->params['supportEmail'], Yii::$app->name))
            ->notEmpty();

        $user = $this->tester->grabRecord(User::class, ['email' => 'gruposol@example.com']);

        verify($user->status)->equals(User::STATUS_ACTIVE);
        verify($user->canLogin())->false();
    }

    public function testCompanySignupRequiresName(): void
    {
        $model = new SignupForm(
            [
                'accountType' => SignupForm::ACCOUNT_COMPANY,
                'companyName' => '',
                'email' => 'empresa@example.com',
                'password' => 'some_password',
            ],
        );

        verify($model->signup(Yii::$app->mailer, Yii::$app->params['supportEmail'], Yii::$app->name))
            ->empty();
        verify($model->getErrors('companyName'))
            ->notEmpty();
    }

    public function testNotCorrectSignup(): void
    {
        $model = new SignupForm(
            [
                'username' => 'troy.becker',
                'email' => 'nicolas.dianna@hotmail.com',
                'password' => 'some_password',
            ],
        );

        verify($model->signup(Yii::$app->mailer, Yii::$app->params['supportEmail'], Yii::$app->name))
            ->empty();
        verify($model->getErrors('username'))
            ->notEmpty();
        verify($model->getErrors('email'))
            ->notEmpty();
        verify($model->getFirstError('username'))
            ->equals('This username has already been taken.');
        verify($model->getFirstError('email'))
            ->equals('This email address has already been taken.');
    }
}
