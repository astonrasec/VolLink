<?php

declare(strict_types=1);

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property int $id
 * @property string $username
 * @property string $password_hash
 * @property string|null $password_reset_token
 * @property string $verification_token
 * @property string $email
 * @property string $auth_key
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property string $password write-only password
 * @property-read UserDetails|null $details
 * @property-read Event[] $createdEvents
 * @property-read Application[] $applications
 * @property-read Attendance[] $confirmedAttendances
 * @property-read Notification[] $notifications
 */
class User extends ActiveRecord implements IdentityInterface
{
    public const STATUS_DELETED = 0;
    public const STATUS_INACTIVE = 9;
    public const STATUS_ACTIVE = 10;
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%user}}';
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
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]],
        ];
    }

    /**
     * Perfil e estado de aprovação do utilizador.
     */
    public function getDetails(): ActiveQuery
    {
        return $this->hasOne(UserDetails::class, ['user_id' => 'id']);
    }

    /**
     * Eventos criados pelo utilizador (empresa).
     */
    public function getCreatedEvents(): ActiveQuery
    {
        return $this->hasMany(Event::class, ['creator_id' => 'id']);
    }

    /**
     * Candidaturas submetidas pelo utilizador.
     */
    public function getApplications(): ActiveQuery
    {
        return $this->hasMany(Application::class, ['user_id' => 'id']);
    }

    /**
     * Presenças confirmadas pelo utilizador (coordenador).
     */
    public function getConfirmedAttendances(): ActiveQuery
    {
        return $this->hasMany(Attendance::class, ['confirmed_by_id' => 'id']);
    }

    /**
     * Notificações do utilizador.
     */
    public function getNotifications(): ActiveQuery
    {
        return $this->hasMany(Notification::class, ['user_id' => 'id']);
    }

    /**
     * Indica se a conta pertence a uma empresa.
     */
    public function isCompany(): bool
    {
        return $this->details?->isCompany() ?? false;
    }

    /**
     * Uma conta só pode entrar com `status` ativo e, quando tem perfil, com
     * `account_status` aprovado. Contas sem perfil (por exemplo, o SysAdmin
     * criado à mão) não têm esta restrição.
     */
    public function canLogin(): bool
    {
        $details = $this->details;

        return $this->status === self::STATUS_ACTIVE && ($details === null || $details->isApproved());
    }

    /**
     * Só uma empresa aprovada pode criar e publicar eventos.
     */
    public function canCreateEvents(): bool
    {
        return $this->isCompany() && $this->details?->isApproved() === true;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id): User|null
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null): never
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername(string $username): User|null
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds an active user by email or username (MVP: login por email).
     *
     * @param string $login email or username
     * @return static|null
     */
    public static function findByLogin(string $login): User|null
    {
        $user = static::find()
            ->where(['status' => self::STATUS_ACTIVE])
            ->andWhere(['or', ['username' => $login], ['email' => $login]])
            ->one();

        return $user !== null && $user->canLogin() ? $user : null;
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken(string $token): User|null
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken(string $token): User|null
    {
        return static::findOne(
            [
                'verification_token' => $token,
                'status' => self::STATUS_INACTIVE,
            ],
        );
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string|null $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid(string|null $token): bool
    {
        if ($token === null || $token === '') {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);

        $expire = Yii::$app->params['user.passwordResetTokenExpire'];

        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): int
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey(): string
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey): bool
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey(): void
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken(): void
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates new token for email verification
     */
    public function generateEmailVerificationToken(): void
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken(): void
    {
        $this->password_reset_token = null;
    }
}
