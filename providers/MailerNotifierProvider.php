<?php

namespace steroids\notifier\providers;

use steroids\notifier\NotifierModule;
use \Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\swiftmailer\Mailer;

/**
 * @property-read Mailer $mailer
 */
class MailerNotifierProvider extends BaseNotifierProvider
{
    public string $from = '';
    public string $host = '';
    public string $username = '';
    public string $password = '';
    public string $port = '587';
    public string $encryption = 'tsl';

    /**
     * @var Mailer|array
     */
    private $_mailer = [];

    /**
     * @inheritDoc
     */
    public static function type()
    {
        return NotifierModule::PROVIDER_TYPE_MAIL;
    }

    /**
     * @throws InvalidConfigException
     */
    public function getMailer()
    {
        if (is_array($this->_mailer)) {
            $this->_mailer = Yii::createObject(ArrayHelper::merge(
                $this->defaultMailer(),
                $this->_mailer ?: []
            ));
        }
        return $this->_mailer;
    }

    /**
     * @param Mailer|array $mailer
     */
    public function setMailer($mailer)
    {
        $this->_mailer = $mailer;
    }

    /**
     * @inheritDoc
     */
    public function send($message)
    {
        $this->mailer->compose()
            ->setTo($message->to)
            ->setHtmlBody((string)$message)
            ->send();
    }

    protected function defaultMailer()
    {
        return [
            'class' => '\yii\swiftmailer\Mailer',
            'messageConfig' => [
                'subject' => Yii::$app->name,
                'from' => $this->from,
            ],
            'transport' => $this->host
                ? [
                    'class' => 'Swift_SmtpTransport',
                    'host' => $this->host,
                    'username' => $this->host,
                    'password' => $this->host,
                    'port' => $this->port,
                    'encryption' => $this->encryption,
                ]
                : [],
        ];
    }
}
