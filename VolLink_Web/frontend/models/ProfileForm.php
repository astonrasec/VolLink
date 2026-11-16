<?php

declare(strict_types=1);

namespace frontend\models;

use common\models\UserDetails;
use yii\base\Model;

/**
 * Edição dos dados pessoais do utilizador autenticado.
 */
class ProfileForm extends Model
{
    public string $name = '';
    public string $skills = '';
    public string $birthDate = '';

    private UserDetails $details;

    public function __construct(UserDetails $details, array $config = [])
    {
        parent::__construct($config);

        $this->details = $details;
        $this->name = $details->name;
        $this->skills = (string) $details->skills;
        $this->birthDate = (string) $details->birth_date;
    }

    public function rules(): array
    {
        return [
            ['name', 'trim'],
            ['name', 'required'],
            ['name', 'string', 'max' => 100],
            ['skills', 'trim'],
            ['skills', 'string', 'max' => 255],
            ['birthDate', 'date', 'format' => 'php:Y-m-d'],
            ['birthDate', 'validateAge'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Nome',
            'skills' => 'Competências',
            'birthDate' => 'Data de nascimento',
        ];
    }

    public function validateAge(string $attribute): void
    {
        if ($this->birthDate !== '' && $this->birthDate > date('Y-m-d')) {
            $this->addError($attribute, 'A data de nascimento não pode ser no futuro.');
        }
    }

    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $this->details->name = $this->name;
        $this->details->skills = $this->skills === '' ? null : $this->skills;
        $this->details->birth_date = $this->birthDate === '' ? null : $this->birthDate;

        return $this->details->save();
    }
}
