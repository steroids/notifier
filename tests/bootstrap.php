<?php

use steroids\core\base\ConsoleApplication;
use steroids\notifier\providers\MailerNotifierProvider;
use steroids\notifier\providers\StoreDbNotifierProvider;
use \yii\helpers\ArrayHelper;
use yii\swiftmailer\Mailer;

define('STEROIDS_ROOT_DIR', realpath(__DIR__ . '/../../..'));
define('YII_ENV', 'test');

$config = require __DIR__ . '/../../../bootstrap.php';
$config = ArrayHelper::merge($config, [
    'modules' => [
        'notifier' => [
            'class' => 'steroids\notifier\NotifierModule',
            'viewPath' => __DIR__ .  '/testData/views',
            'providers' => [
                'store' => [
                    'class' => StoreDbNotifierProvider::class
                ],
                'mail' => [
                    'class' => MailerNotifierProvider::class,
                    'from' => 'test@test.ru',
                    'mailer' => [
                        'class' => Mailer::class,
                        'useFileTransport' => true,
                        'fileTransportPath' => __DIR__ . '/testData/mail',
                        'viewPath' => __DIR__ . '/testData',
                        'htmlLayout' => __DIR__ . 'layout/main.php'
                    ]
                ],
            ]
        ],
    ]
]);

new \steroids\core\base\WebApplication($config);
