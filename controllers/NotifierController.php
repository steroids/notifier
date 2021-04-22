<?php

namespace steroids\notifier\controllers;

use steroids\auth\AuthModule;
use steroids\auth\enums\AuthAttributeTypeEnum;
use steroids\auth\UserInterface;
use steroids\notifier\forms\NotificationSearchForm;
use steroids\notifier\models\Notification;
use Yii;
use yii\web\Controller;

class NotifierController extends Controller
{
    public static function apiMap()
    {
        return [
            'notifier' => [
                'items' => [
                    'mail-test' => 'GET /api/v1/notifier/mail-test',
                    'notifications' => 'GET /api/v1/notifier/notifications',
                    'mark-read' => 'POST /api/v1/notifier/notifications/<id:\d+>/mark-read',
                    'mark-read-all' => 'POST /api/v1/notifier/notifications/mark-read-all',
                ],
            ],
        ];
    }


    /**
     * @return NotificationSearchForm
     */
    public function actionNotifications()
    {
        $formModel = new NotificationSearchForm([
            'userId' => Yii::$app->user->id
        ]);
        $formModel->search(Yii::$app->request->get());

        return $formModel;
    }

    /**
     * @param $id
     * @return Notification
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionMarkRead($id)
    {
        $notification = Notification::findOrPanic([
            'id' => $id,
            'userId' => Yii::$app->user->id
        ]);
        $notification->isRead = true;

        return $notification;
    }

    public function actionMarkReadAll()
    {
        return Notification::updateAll(['isRead' => true], ['userId' => Yii::$app->user->id]);
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
