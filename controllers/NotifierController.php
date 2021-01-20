<?php

namespace steroids\notifier\controllers;

use steroids\auth\AuthModule;
use steroids\auth\enums\AuthAttributeTypeEnum;
use steroids\auth\UserInterface;
use yii\web\Controller;

class NotifierController extends Controller
{
    public static function apiMap()
    {
        return [
            'notifier' => [
                'items' => [
                    'mail-test' => 'GET /api/v1/notifier/mail-test',
                ],
            ],
        ];
    }


    public function actionMailTest($email = null)
    {
        if (!$email) {
            return 'Email required, add to query: ?email=test@test.com';
        }

        /** @var UserInterface $userClass */
        $userClass = AuthModule::getInstance()->userClass;
        $user = $userClass::findBy($email, [AuthAttributeTypeEnum::EMAIL]);
        if (!$user) {
            return 'User not found';
        }

        $user->sendNotify('notifier/test');
        return 'ok';
    }
}
