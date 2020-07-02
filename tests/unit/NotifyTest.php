<?php

namespace steroids\notifier\tests\unit;

use PHPUnit\Framework\TestCase;
use steroids\notifier\NotifierModule;
use steroids\notifier\providers\MailerNotifierProvider;
use steroids\notifier\structures\MailNotifyParameters;
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
        $notifier = NotifierModule::getInstance();
        $notifier->templates = [
            'mail' => 'views/template.php'
        ];
        $notifier->providers = [
            'mail' => [
                'class' => MailerNotifierProvider::class
            ],
        ];

        $mailParams = new MailNotifyParameters([
            'sender' => ['sender@mail.ru' => 'Test sending'],
            'receiver' => 'receiver@mail.ru',
        ]);

        $notifier->send(NotifierModule::PROVIDER_TYPE_MAIL, 'mail', $mailParams);

        // directory that contains mails
        $mails = scandir(dirname(__DIR__) . '/testData/mail', 1);

        //extension of sending file
        $ext = pathinfo($mails[0], PATHINFO_EXTENSION);

        // file exist
        $this->assertTrue($ext === 'eml');
    }
}
