<?php

declare(strict_types=1);

namespace frontend\models;

use common\models\User;
use common\models\UserDetails;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Model;

class VerifyEmailForm extends Model
{
    private User|null $_user = null;
    /**
     * Creates a form model with given token.
     *
     * @param string $token
     * @param array $config name-value pairs that will be used to initialize the object properties
     * @throws InvalidArgumentException if token is empty or not valid
     */
    public function __construct(string $token, array $config = [])
    {
        if ($token === '') {
            throw new InvalidArgumentException('Verify email token cannot be blank.');
        }

        $this->_user = User::findByVerificationToken($token);

        if ($this->_user === null) {
            throw new InvalidArgumentException('Wrong verify email token.');
        }

        parent::__construct($config);
    }

    /**
     * Verify email
     *
     * @return User|null the saved model or null if saving fails
     */
    public function verifyEmail(): User|null
    {
        $user = $this->_user;
        $details = $user->details;

        $transaction = Yii::$app->db->beginTransaction();

        try {
            if ($details !== null) {
                $details->email_verified = true;

                // A empresa só fica ativa depois da validação do SysAdmin.
                if (!$details->isCompany()) {
                    $details->account_status = UserDetails::STATUS_APPROVED;
                }

                if (!$details->save(false)) {
                    $transaction->rollBack();

                    return null;
                }
            }

            if ($details === null || !$details->isCompany()) {
                $user->status = User::STATUS_ACTIVE;
            }

            if (!$user->save(false)) {
                $transaction->rollBack();

                return null;
            }

            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();

            throw $e;
        }

        return $user;
    }
}
