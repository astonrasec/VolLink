<?php

declare(strict_types=1);

namespace backend\models;

use common\models\User;
use common\models\UserDetails;
use Yii;
use yii\base\Model;

class UserForm extends Model
{
    public const ROLES = [
        'voluntario' => 'Voluntário',
        'empresa' => 'Empresa',
        'sysAdmin' => 'Administrador',
    ];

    public $username;
    public $email;
    public $password;
    public $name;
    public $role = 'voluntario';
    public $status = User::STATUS_ACTIVE;

    private ?User $user = null;

    public function rules(): array
    {
        return [
            [['username', 'email', 'name', 'role'], 'required'],
            [['username', 'email', 'name'], 'trim'],
            ['email', 'email'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['name', 'string', 'max' => 100],
            ['password', 'required', 'when' => fn() => $this->user === null],
            ['password', 'string', 'min' => 8],
            ['role', 'in', 'range' => array_keys(self::ROLES)],
            ['status', 'in', 'range' => [User::STATUS_ACTIVE, User::STATUS_INACTIVE]],
            ['username', 'validateUnique'],
            ['email', 'validateUnique'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'username' => 'Username',
            'email' => 'Email',
            'password' => 'Palavra-passe',
            'name' => 'Nome',
            'role' => 'Perfil',
            'status' => 'Estado',
        ];
    }

    public function validateUnique(string $attribute): void
    {
        $query = User::find()->where([$attribute => $this->$attribute]);
        if ($this->user !== null) {
            $query->andWhere(['<>', 'id', $this->user->id]);
        }
        if ($query->exists()) {
            $this->addError($attribute, 'Já existe um utilizador com este valor.');
        }
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->status = $user->status;
        $this->name = $user->details?->name;

        $roles = array_keys(Yii::$app->authManager->getRolesByUser($user->id));
        $this->role = $roles[0] ?? 'voluntario';
    }

    public function save(): ?User
    {
        if (!$this->validate()) {
            return null;
        }

        $user = $this->user ?? new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->status = (int) $this->status;

        if ($this->password) {
            $user->setPassword($this->password);
        }
        if ($user->isNewRecord) {
            $user->generateAuthKey();
        }

        $transaction = Yii::$app->db->beginTransaction();

        if (!$user->save()) {
            $transaction->rollBack();
            $this->addErrors($user->getErrors());
            return null;
        }

        $details = $user->details ?? new UserDetails(['user_id' => $user->id]);
        $details->name = $this->name;
        $details->account_type = match ($this->role) {
            'empresa' => UserDetails::TYPE_COMPANY,
            'sysAdmin' => UserDetails::TYPE_ADMIN,
            default => UserDetails::TYPE_VOLUNTEER,
        };
        if ($details->isNewRecord) {
            $details->account_status = UserDetails::STATUS_APPROVED;
            $details->email_verified = true;
        }

        if (!$details->save()) {
            $transaction->rollBack();
            $this->addErrors($details->getErrors());
            return null;
        }

        $auth = Yii::$app->authManager;
        $auth->revokeAll($user->id);
        $auth->assign($auth->getRole($this->role), $user->id);

        $transaction->commit();

        return $user;
    }
}
