<?php

namespace steroids\notifier\providers;

use ExponentPhpSDK\Expo;
use steroids\notifier\NotifierModule;
use yii\helpers\ArrayHelper;

/**
 * @property-read Expo $pushClient
 */
class ExpoNotifierProvider extends BaseNotifierProvider
{
    private ?Expo $_pushClient = null;

    /**
     * @inheritDoc
     */
    public static function type()
    {
        return NotifierModule::PROVIDER_TYPE_PUSH;
    }

    /**
     * @return Expo
     */
    public function getPushClient()
    {
        if (!$this->_pushClient) {
            $this->_pushClient = new Expo();
        }
        return $this->_pushClient;
    }

    /**
     * @inheritDoc
     */
    public function send($message)
    {
        $notification = [
            'title' => $message->title,
            'body' => (string)$message,
            'data' => ArrayHelper::getValue($message->params, 'data','{}'),
        ];

        $this->pushClient->notify($message->to, $notification);
    }
}