<?php

declare(strict_types=1);

namespace frontend\models;

use common\models\User;
use common\models\UserDetails;
use Yii;
use yii\base\Model;
use yii\mail\MailerInterface;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public const ACCOUNT_VOLUNTEER = 'volunteer';
    public const ACCOUNT_COMPANY = 'company';

    public string $username = '';
    public string $companyName = '';
    public string $birthDate = '';
    public string $email = '';
    public string $password = '';
    public string $accountType = self::ACCOUNT_VOLUNTEER;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            ['accountType', 'required'],
            ['accountType', 'in', 'range' => [self::ACCOUNT_VOLUNTEER, self::ACCOUNT_COMPANY]],

            ['username', 'trim'],
            [
                'username',
                'required',
                'when' => fn (): bool => !$this->isCompany(),
                'whenClient' => $this->clientWhen(false),
            ],
            [
                'username',
                'unique',
                'targetClass' => User::class,
                'message' => 'This username has already been taken.',
                'when' => fn (): bool => !$this->isCompany(),
            ],
            [
                'username',
                'string',
                'min' => 2,
                'max' => 255,
                'when' => fn (): bool => !$this->isCompany(),
                'whenClient' => $this->clientWhen(false),
            ],

            ['companyName', 'trim'],
            [
                'companyName',
                'required',
                'when' => fn (): bool => $this->isCompany(),
                'whenClient' => $this->clientWhen(true),
            ],
            [
                'companyName',
                'string',
                'max' => 100,
                'when' => fn (): bool => $this->isCompany(),
                'whenClient' => $this->clientWhen(true),
            ],

            ['birthDate', 'date', 'format' => 'php:Y-m-d', 'skipOnEmpty' => true],
            [
                'birthDate',
                'compare',
                'compareValue' => date('Y-m-d'),
                'operator' => '<=',
                'message' => 'A data de nascimento não pode ser no futuro.',
                'when' => fn (): bool => $this->birthDate !== '',
                'whenClient' => 'function () { return false; }',
            ],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => User::class, 'message' => 'This email address has already been taken.'],

            ['password', 'required'],
            ['password', 'string', 'min' => Yii::$app->params['user.passwordMinLength']],
        ];
    }

    /**
     * Condição no browser: só valida o campo que está visível para o tipo de conta escolhido.
     */
    private function clientWhen(bool $company): string
    {
        $value = $company ? self::ACCOUNT_COMPANY : self::ACCOUNT_VOLUNTEER;

        return "function () { return $('input[name=\"SignupForm[accountType]\"]:checked').val() === '{$value}'; }";
    }

    /**
     * Indica se o registo é de uma empresa.
     */
    public function isCompany(): bool
    {
        return $this->accountType === self::ACCOUNT_COMPANY;
    }

    /**
     * Signs user up.
     *
     * @param MailerInterface $mailer the mailer component.
     * @param string $supportEmail the support email address.
     * @param string $appName the application name.
     *
     * @return bool|null whether the creating new account was successful and email was sent.
     */
    public function signup(MailerInterface $mailer, string $supportEmail, string $appName): bool|null
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();

        // A empresa não escolhe username: usa o email (único) para entrar.
        $user->username = $this->isCompany() ? $this->email : $this->username;
        $user->email = $this->email;

        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();

        if (!$user->save()) {
            $this->addErrors($user->getErrors());

            return null;
        }

        $details = new UserDetails([
            'user_id' => $user->id,
            'name' => $this->isCompany() ? $this->companyName : $this->username,
            'account_type' => $this->isCompany() ? UserDetails::TYPE_COMPANY : UserDetails::TYPE_VOLUNTEER,
            'birth_date' => !$this->isCompany() && $this->birthDate !== '' ? $this->birthDate : null,
            'account_status' => UserDetails::STATUS_PENDING,
            'email_verified' => false,
        ]);

        if (!$details->save()) {
            $this->addErrors($details->getErrors());
            $user->delete();

            return null;
        }

        $auth = Yii::$app->authManager;
        $role = $auth->getRole($this->isCompany() ? 'empresa' : 'voluntario');
        if ($role !== null) {
            $auth->assign($role, $user->id);
        }

        if (!Yii::$app->params['user.requireEmailVerification']) {
            $user->status = User::STATUS_ACTIVE;
            $user->save(false, ['status']);

            if (!$this->isCompany()) {
                $details->account_status = UserDetails::STATUS_APPROVED;
                $details->save(false, ['account_status']);
            }

            return true;
        }

        return $this->sendEmail($mailer, $user, $supportEmail, $appName);
    }

    /**
     * Sends confirmation email to user.
     *
     * @param MailerInterface $mailer the mailer component.
     * @param User $user user model to which the email should be sent.
     * @param string $supportEmail the support email address.
     * @param string $appName the application name.
     *
     * @return bool whether the email was sent.
     */
    protected function sendEmail(MailerInterface $mailer, User $user, string $supportEmail, string $appName): bool
    {
        return $mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user],
            )
            ->setFrom([$supportEmail => $appName . ' robot'])
            ->setTo($this->email)
            ->setSubject('Account registration at ' . $appName)
            ->send();
    }
}
