<?php

declare(strict_types=1);

namespace frontend\tests\Unit\Models;

use Codeception\Test\Unit;
use common\fixtures\UserFixture;
use common\models\User;
use common\models\UserDetails;
use frontend\models\VerifyEmailForm;
use frontend\tests\Support\UnitTester;
use yii\base\InvalidArgumentException;

final class VerifyEmailFormTest extends Unit
{
    protected UnitTester $tester;
    public function _before(): void
    {
        // user_details não tem fixture própria; limpar entre testes.
        UserDetails::deleteAll();

        $this->tester->haveFixtures(
            [
                'user' => [
                    'class' => UserFixture::class,
                    'dataFile' => codecept_data_dir() . 'user.php',
                ],
            ],
        );
    }

    public function testVerifyWrongToken(): void
    {
        $this->tester->expectThrowable(
            InvalidArgumentException::class,
            static function (): void {
                new VerifyEmailForm('');
            },
        );

        $this->tester->expectThrowable(
            InvalidArgumentException::class,
            static function (): void {
                new VerifyEmailForm('notexistingtoken_1391882543');
            }
        );
    }

    public function testAlreadyActivatedToken(): void
    {
        $this->tester->expectThrowable(
            InvalidArgumentException::class,
            static function (): void {
                new VerifyEmailForm('already_used_token_1548675330');
            },
        );
    }

    public function testVerifyCorrectToken(): void
    {
        $model = new VerifyEmailForm('4ch0qbfhvWwkcuWqjN8SWRq72SOw1KYT_1548675330');

        $user = $model->verifyEmail();

        verify($user)
            ->instanceOf(User::class);
        verify($user->username)
            ->equals('test.test');
        verify($user->email)
            ->equals('test@mail.com');
        verify($user->status)
            ->equals(User::STATUS_ACTIVE);
        verify($user->validatePassword('Test1234'))
            ->true();
    }

    public function testCompanyEmailVerificationKeepsAccountPending(): void
    {
        $user = new User([
            'username' => 'empresa@example.com',
            'email' => 'empresa@example.com',
            'status' => User::STATUS_INACTIVE,
            'verification_token' => 'company_token_1548675330',
        ]);
        $user->setPassword('Test1234');
        $user->generateAuthKey();
        verify($user->save(false))->true();

        $details = new UserDetails([
            'user_id' => $user->id,
            'name' => 'Empresa Teste',
            'account_type' => UserDetails::TYPE_COMPANY,
            'account_status' => UserDetails::STATUS_PENDING,
            'email_verified' => false,
        ]);
        verify($details->save(false))->true();

        $verified = (new VerifyEmailForm('company_token_1548675330'))->verifyEmail();

        verify($verified)->instanceOf(User::class);
        verify($verified->status)->equals(User::STATUS_INACTIVE);

        $details->refresh();
        verify((bool) $details->email_verified)->true();
        verify($details->account_status)->equals(UserDetails::STATUS_PENDING);
    }
}
