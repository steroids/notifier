<?php

namespace steroids\notifier\providers;

use \Yii;
use yii\di\Instance;
use yii\swiftmailer\Mailer;
use steroids\notifier\exceptions\NotifierException;

class MailerNotifierProvider extends BaseNotifierProvider
{
    /**
     * @var Mailer
     */
    public $mailer = 'mailer';

    public function init()
    {
        parent::init();

        $this->mailer = Instance::ensure($this->mailer, Mailer::class);
    }

    public function send(string $templatePath, array $params)
    {
        if (empty($params['email'])) {
            throw new NotifierException('Not found email for send mail.');
        }

        // Send
        $message = $this->mailer->compose($templatePath, array_merge($params, ['user' => $this]));
        if (!$message->getSubject()) {
            $message->setSubject(Yii::$app->name);
        }
        $message->setTo($params['email'])->send();
    }
}
