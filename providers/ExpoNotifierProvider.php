<?php

namespace steroids\notifier\providers;

use ExponentPhpSDK\Exceptions\ExpoException;
use ExponentPhpSDK\Exceptions\UnexpectedResponseException;
use ExponentPhpSDK\Expo;
use steroids\notifier\exceptions\NotifierException;
use steroids\notifier\structures\PushNotifyParameters;

/**
 * @property-read Expo $pushClient
 */
class ExpoNotifierProvider extends BaseNotifierProvider
{
    private ?Expo $_pushClient = null;

    /**
     * @return Expo
     */
    public function getPushClient()
    {
        if (!$this->_pushClient) {
            $this->_pushClient = Expo::normalSetup();
        }
        return $this->_pushClient;
    }

    /**
     * @param string $templatePath
     * @param PushNotifyParameters $params
     * @return mixed|void
     * @throws ExpoException
     * @throws UnexpectedResponseException|NotifierException
     */
    public function send(string $templatePath, $params)
    {
        if (!empty($params->pushToken)) {
            throw new NotifierException("Push token cannot be empty");
        }

        $this->pushClient->subscribe($params->channel, $params->pushToken);
        $notification = [
            'title' => $params->title,
            'body' => $params->message,
            'data' => $params->data
        ];
        $this->pushClient->notify([$params->channel], $notification);
    }
}