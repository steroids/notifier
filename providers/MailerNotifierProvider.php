<?php

namespace steroids\notifier\providers;

use steroids\notifier\structures\MailNotifyParameters;
use \Yii;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\swiftmailer\Mailer;
use steroids\notifier\exceptions\NotifierException;

/**
 * @property-read Mailer $mailer
 */
class MailerNotifierProvider extends BaseNotifierProvider
{
    /**
     * @var Mailer
     */
    private $_mailer = null;

    /**
     * @throws InvalidConfigException
     */
    public function getMailer()
    {
        if (!$this->_mailer) {
            $this->_mailer = Instance::ensure($this->_mailer, Mailer::class);
        }
        return $this->_mailer;
    }

    /**
     * @param string $templatePath
     * @param MailNotifyParameters $params
     * @throws NotifierException
     */
    public function send(string $templatePath, $params)
    {
        if (empty($params->receiver)) {
            throw new NotifierException('Not found email for send mail.');
        }

        // Send
        $message = $this->mailer->compose($templatePath,$params->composeParameters);
        if (!$message->getSubject()) {
            $message->setSubject(Yii::$app->name);
        }
        $message->setFrom($params->sender);
        $message->setTo($params->receiver)->send();
    }
}
