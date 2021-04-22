<?php

namespace steroids\notifier\tests\unit;

use app\user\models\User;
use PHPUnit\Framework\TestCase;
use steroids\core\tests\traits\ModelsCleanupTrait;
use steroids\notifier\models\Notification;
use steroids\notifier\NotifierMessage;
use steroids\notifier\NotifierModule;
use steroids\core\tests\traits\ApiCallTrait;
use yii\base\Exception;
use yii\base\InvalidConfigException;

class NotifyTest extends TestCase
{
    use ApiCallTrait;
    use ModelsCleanupTrait;

    /**
     * Email does not send for real, used file transport
     *
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function testMailNotify()
    {
        $user = $this->createNotificationUser();

        $notifier = NotifierModule::getInstance();

        $mailParams = [
            'sender' => ['sender@mail.ru' => 'Test sending'],
            'receiver' => 'receiver@mail.ru',
        ];

        $notifier->send(new NotifierMessage([
            'destinations' => [
                NotifierModule::PROVIDER_TYPE_MAIL => 'receiver@mail.ru',
            ],
            'userId' => $user->id,
            'templateName' => 'notifier/template'
        ]));

        // directory that contains mails
        $mails = scandir(dirname(__DIR__) . '/testData/mail', 1);

        //extension of sending file
        $ext = pathinfo($mails[0], PATHINFO_EXTENSION);

        // file exist
        $this->assertTrue($ext === 'eml');
    }

    public function testStoreNotifier()
    {
        $user = $this->createNotificationUser();

        $notifier = NotifierModule::getInstance();

        $notifier->send(new NotifierMessage([
            'destinations' => [
                NotifierModule::PROVIDER_TYPE_STORE => $user->id,
            ],
            'userId' => $user->id,
            'templateName' => 'notifier/template',
            'params' => [
                'refId' => 1,
                'testNotification' => true
            ]
        ]));

        $notifier->send(new NotifierMessage([
            'destinations' => [
                NotifierModule::PROVIDER_TYPE_STORE => $user->id,
            ],
            'userId' => $user->id,
            'templateName' => 'notifier/template',
            'params' => [
                'refId' => 2,
                'testNotification' => true
            ]
        ]));

        $notification = Notification::findOne([
            'userId' => $user->id,
            'templateName' => 'notifier/template',
            'refId' => 1,
            'isRead' => false,
            'paramsJson' => json_encode(['testNotification' => true])
        ]);
        $secondNotification = Notification::findOne([
            'userId' => $user->id,
            'templateName' => 'notifier/template',
            'refId' => 2,
            'isRead' => false,
            'paramsJson' => json_encode(['testNotification' => true])
        ]);

        $this->assertNotNull($notification);

        //get notifications
        $request = $this->callApi('GET /api/v1/notifier/notifications', $user->id);
        $this->assertNotNull($request);

        //mark single notification is read
        $this->callApi('POST /api/v1/notifier/notifications/'.$notification->id.'/mark-read', $user->id);
        $notification->refresh();
        $this->assertNotFalse($notification->isRead);


        //mark all notifications is read
        $this->callApi('POST /api/v1/notifier/notifications/mark-read-all', $user->id);
        $secondNotification->refresh();
        $this->assertNotFalse($secondNotification->isRead);
    }

    private function createNotificationUser()
    {
        $user = User::findOne(['id' => 1]);
        if (!$user) {
            $user = new User([
                'email' => 'test@test@example.com',
                'role' => 'user',
            ]);
            $user->saveOrPanic();
        }

        return $user;
    }
}
