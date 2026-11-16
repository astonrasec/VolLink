<?php

declare(strict_types=1);

namespace backend\controllers;

use backend\models\UserForm;
use common\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Gestão de contas de utilizadores.
 */
final class UserController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['userModerate']],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['delete' => ['post']],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $users = User::find()->with('details')->where(['deleted_at' => null])->orderBy(['id' => SORT_DESC])->all();

        return $this->render('index', ['users' => $users]);
    }

    public function actionView(int $id): string
    {
        return $this->render('view', ['user' => $this->findUser($id)]);
    }

    public function actionCreate(): string|Response
    {
        $form = new UserForm();

        if ($form->load(Yii::$app->request->post()) && ($user = $form->save()) !== null) {
            Yii::$app->session->setFlash('success', 'Utilizador criado.');
            return $this->redirect(['view', 'id' => $user->id]);
        }

        return $this->render('create', ['model' => $form]);
    }

    public function actionUpdate(int $id): string|Response
    {
        $user = $this->findUser($id);
        $form = new UserForm();
        $form->setUser($user);

        if ($form->load(Yii::$app->request->post()) && $form->save() !== null) {
            Yii::$app->session->setFlash('success', 'Utilizador atualizado.');
            return $this->redirect(['view', 'id' => $user->id]);
        }

        return $this->render('update', ['model' => $form, 'user' => $user]);
    }

    public function actionDelete(int $id): Response
    {
        $user = $this->findUser($id);

        if ($user->id === Yii::$app->user->id) {
            Yii::$app->session->setFlash('error', 'Não podes eliminar a tua própria conta.');
        } else {
            $user->softDelete();
            Yii::$app->session->setFlash('success', 'Utilizador eliminado.');
        }

        return $this->redirect(['index']);
    }

    private function findUser(int $id): User
    {
        $user = User::findOne(['id' => $id, 'deleted_at' => null]);
        if ($user === null) {
            throw new NotFoundHttpException('Utilizador não encontrado.');
        }

        return $user;
    }
}
