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
            ['username', 'required', 'when' => fn (): bool => !$this->isCompany()],
            [
                'username',
                'unique',
                'targetClass' => User::class,
                'message' => 'This username has already been taken.',
                'when' => fn (): bool => !$this->isCompany(),
            ],
            ['username', 'string', 'min' => 2, 'max' => 255, 'when' => fn (): bool => !$this->isCompany()],

            ['companyName', 'trim'],
            ['companyName', 'required', 'when' => fn (): bool => $this->isCompany()],
            ['companyName', 'string', 'max' => 100, 'when' => fn (): bool => $this->isCompany()],

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
