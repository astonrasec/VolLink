<?php

declare(strict_types=1);

namespace frontend\models;

use common\models\User;
use Yii;
use yii\base\Model;

/**
 * Alteração da palavra-passe do utilizador autenticado.
 */
class ChangePasswordForm extends Model
{
    public string $current = '';
    public string $new = '';
    public string $repeat = '';

    private User $user;

    public function __construct(User $user, array $config = [])
    {
        parent::__construct($config);

        $this->user = $user;
    }

    public function rules(): array
    {
        return [
            [['current', 'new', 'repeat'], 'required'],
            ['current', 'validateCurrent'],
            ['new', 'string', 'min' => Yii::$app->params['user.passwordMinLength']],
            ['repeat', 'compare', 'compareAttribute' => 'new', 'message' => 'As palavras-passe não coincidem.'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'current' => 'Palavra-passe atual',
            'new' => 'Nova palavra-passe',
            'repeat' => 'Confirmar palavra-passe',
        ];
    }

    public function validateCurrent(string $attribute): void
    {
        if (!$this->user->validatePassword($this->current)) {
            $this->addError($attribute, 'A palavra-passe atual está errada.');
        }
    }

    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $this->user->setPassword($this->new);
        $this->user->generateAuthKey();

        return $this->user->save(false);
    }
}
