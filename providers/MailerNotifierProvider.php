<?php

namespace steroids\notifier\providers;

use steroids\notifier\structures\MailNotifyParameters;
use \Yii;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\swiftmailer\Mailer;
use steroids\notifier\exceptions\NotifierException;

class MailerNotifierProvider extends BaseNotifierProvider
{
    /**
     * @var Mailer
     */
    public $mailer = 'mailer';

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        $this->mailer = Instance::ensure($this->mailer, Mailer::class);
    }

    /**
     * @param string $templatePath
     * @param MailNotifyParameters $params
     * @throws NotifierException
     */
    public function send(string $templatePath, $params)
    {
        if (empty($params->email)) {
            throw new NotifierException('Not found email for send mail.');
        }

        // Send
        $message = $this->mailer->compose($templatePath,
            array_merge(
                $params->composeParameters,
                ['user' => $params->user]
            )
        );
        if (!$message->getSubject()) {
            $message->setSubject(Yii::$app->name);
        }
        $message->setTo($params->email)->send();
    }
}
