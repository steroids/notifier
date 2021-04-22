<?php

namespace steroids\notifier\tests\unit;

use app\user\models\User;
use PHPUnit\Framework\TestCase;
use steroids\notifier\NotifierMessage;
use steroids\notifier\NotifierModule;
use steroids\notifier\providers\MailerNotifierProvider;
use steroids\notifier\providers\StoreDbNotifierProvider;
use yii\base\Exception;
use yii\base\InvalidConfigException;

class NotifyTest extends TestCase
{
    /**
     * Email does not send for real, used file transport
     *
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function testMailNotify()
    {
        $user = User::findOne(['id' => 1]);
        if (!$user) {
            $user = new User([
                'email' => 'test@test@example.com',
                'role' => 'user',
            ]);
            $user->saveOrPanic();
        }

        $notifier = NotifierModule::getInstance();

        $notifier->providers = [
            'mail' => [
                'class' => MailerNotifierProvider::class
            ],
        ];

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
        $user = User::findOne(['id' => 1]);
        if (!$user) {
            $user = new User([
                'email' => 'test@test@example.com',
                'role' => 'user',
            ]);
            $user->saveOrPanic();
        }

        $notifier = NotifierModule::getInstance();
        $notifier->providers = [
            'store' => [
                'class' => StoreDbNotifierProvider::class
            ],
        ];

        $notifier->send(new NotifierMessage([
            'destinations' => [
                NotifierModule::PROVIDER_TYPE_STORE => $user->id,
            ],
            'userId' => $user->id,
            'templateName' => 'notifier/template'
        ]));
    }
}
