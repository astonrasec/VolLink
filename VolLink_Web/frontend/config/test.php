<?php

declare(strict_types=1);

return [
    'id' => 'app-frontend-tests',
    'params' => [
        'user.requireEmailVerification' => true,
    ],
    'components' => [
        'assetManager' => [
            'basePath' => __DIR__ . '/../web/assets',
        ],
        'urlManager' => [
            'showScriptName' => true,
        ],
        'request' => [
            'cookieValidationKey' => 'test',
        ],
        'mailer' => [
            'messageClass' => \yii\symfonymailer\Message::class,
            'viewPath' => '@common/mail',
        ],
    ],
];
