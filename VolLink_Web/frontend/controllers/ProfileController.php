<?php

declare(strict_types=1);

namespace frontend\controllers;

use common\models\Attendance;
use common\models\User;
use common\models\UserDetails;
use frontend\models\ChangePasswordForm;
use frontend\models\ProfileForm;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

/**
 * Perfil do utilizador autenticado: dados pessoais, histórico de horas e palavra-passe.
 */
final class ProfileController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['@']],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['password' => ['post']],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $user = $this->currentUser();

        return $this->renderIndex($user, new ChangePasswordForm($user));
    }

    public function actionUpdate(): string|Response
    {
        $form = new ProfileForm($this->currentDetails($this->currentUser()));

        if ($form->load(Yii::$app->request->post()) && $form->save()) {
            Yii::$app->session->setFlash('success', 'Perfil atualizado.');
            return $this->redirect(['index']);
        }

        return $this->render('update', ['model' => $form]);
    }

    public function actionPassword(): string|Response
    {
        $user = $this->currentUser();
        $form = new ChangePasswordForm($user);

        if ($form->load(Yii::$app->request->post()) && $form->save()) {
            Yii::$app->session->setFlash('success', 'Palavra-passe alterada.');
            return $this->redirect(['index']);
        }

        return $this->renderIndex($user, $form);
    }

    private function renderIndex(User $user, ChangePasswordForm $passwordForm): string
    {
        $attendances = Attendance::find()
            ->innerJoinWith('application')
            ->with('shift.event')
            ->where(['application.user_id' => $user->id, 'attendance.confirmed' => 1])
            ->orderBy(['attendance.id' => SORT_DESC])
            ->all();

        $total = 0.0;
        foreach ($attendances as $attendance) {
            $total += (float) $attendance->hours_worked;
        }

        return $this->render('index', [
            'user' => $user,
            'details' => $this->currentDetails($user),
            'attendances' => $attendances,
            'total' => $total,
            'passwordForm' => $passwordForm,
        ]);
    }

    private function currentUser(): User
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;

        return $user;
    }

    private function currentDetails(User $user): UserDetails
    {
        return $user->details ?? new UserDetails(['user_id' => $user->id, 'name' => $user->username]);
    }
}
