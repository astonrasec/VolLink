<?php

declare(strict_types=1);

namespace frontend\controllers;

use common\models\Event;
use common\models\LoginForm;
use frontend\models\ContactForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResendVerificationEmailForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\captcha\CaptchaAction;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\mail\MailerInterface;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly MailerInterface $mailer,
        $config = [],
    ) {
        parent::__construct($id, $module, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
            'captcha' => [
                'class' => CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeAction($action): bool
    {
        $this->layout = match ($action->id) {
            'login', 'signup' => 'auth',
            'request-password-reset', 'reset-password', 'resend-verification-email' => 'card',
            default => 'main',
        };

        return parent::beforeAction($action);
    }

    /**
     * Feed público de eventos publicados (RF-FO-04 e RF-FO-06).
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $filters = [
            'q' => trim((string) Yii::$app->request->get('q', '')),
            'category' => trim((string) Yii::$app->request->get('category', '')),
            'location' => trim((string) Yii::$app->request->get('location', '')),
            'date_from' => trim((string) Yii::$app->request->get('date_from', '')),
            'date_to' => trim((string) Yii::$app->request->get('date_to', '')),
        ];

        $query = Event::findPublished();

        if ($filters['q'] !== '') {
            $query->andWhere([
                'or',
                ['like', 'title', $filters['q']],
                ['like', 'description', $filters['q']],
            ]);
        }

        if ($filters['category'] !== '') {
            $query->andWhere(['category' => $filters['category']]);
        }

        if ($filters['location'] !== '') {
            $query->andWhere(['like', 'location', $filters['location']]);
        }

        $dateFrom = $this->normalizeDate($filters['date_from']);
        if ($dateFrom !== null) {
            $query->andWhere(['>=', 'start_time', $dateFrom . ' 00:00:00']);
        }

        $dateTo = $this->normalizeDate($filters['date_to']);
        if ($dateTo !== null) {
            $query->andWhere(['<', 'start_time', date('Y-m-d', strtotime($dateTo . ' +1 day')) . ' 00:00:00']);
        }

        $pagination = new Pagination([
            'totalCount' => (int) (clone $query)->count(),
            'pageSize' => 9,
        ]);

        return $this->render('index', [
            'events' => $query->offset($pagination->offset)->limit($pagination->limit)->all(),
            'categories' => Event::publishedCategories(),
            'filters' => $filters,
            'pagination' => $pagination,
        ]);
    }

    /**
     * Devolve a data em `Y-m-d` ou `null` quando o valor não é válido.
     */
    private function normalizeDate(string $value): ?string
    {
        if ($value === '') {
            return null;
        }

        $date = \DateTime::createFromFormat('Y-m-d', $value);

        return $date !== false && $date->format('Y-m-d') === $value ? $value : null;
    }

    /**
     * Logs in a user.
     *
     * @return string|Response
     */
    public function actionLogin(): string|Response
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logs out the current user.
     *
     * @return Response
     */
    public function actionLogout(): Response
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return string|Response
     */
    public function actionContact(): string|Response
    {
        $model = new ContactForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $sent = $model->sendEmail(
                $this->mailer,
                Yii::$app->params['adminEmail'],
                Yii::$app->params['senderEmail'],
                Yii::$app->params['senderName'],
            );

            if ($sent) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        }

        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout(): string
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return string|Response
     */
    public function actionSignup(): string|Response
    {
        $model = new SignupForm();

        $signed = $model->load(Yii::$app->request->post()) && $model->signup(
            $this->mailer,
            Yii::$app->params['supportEmail'],
            Yii::$app->name,
        );

        if ($signed) {
            $verify = Yii::$app->params['user.requireEmailVerification'];
            if ($model->isCompany()) {
                $message = $verify
                    ? 'Conta de empresa criada. Confirma o email e aguarda a validação do SysAdmin.'
                    : 'Conta de empresa criada. Aguarda a validação do SysAdmin.';
            } else {
                $message = $verify
                    ? 'Thank you for registration. Please check your inbox for verification email.'
                    : 'Conta criada. Já podes entrar.';
            }
            Yii::$app->session->setFlash('success', $message);
            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return string|Response
     */
    public function actionRequestPasswordReset(): string|Response
    {
        $model = new PasswordResetRequestForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $sent = $model->sendEmail(
                $this->mailer,
                Yii::$app->params['supportEmail'],
                Yii::$app->name,
            );

            if ($sent) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            }

            Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return string|Response
     * @throws BadRequestHttpException
     */
    public function actionResetPassword(string $token): string|Response
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionVerifyEmail(string $token): Response
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->verifyEmail()) {
            Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
            return $this->goHome();
        }

        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return string|Response
     */
    public function actionResendVerificationEmail(): string|Response
    {
        $model = new ResendVerificationEmailForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $sent = $model->sendEmail(
                $this->mailer,
                Yii::$app->params['supportEmail'],
                Yii::$app->name,
            );

            if ($sent) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }

            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model,
        ]);
    }
}
