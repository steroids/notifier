<?php

namespace steroids\notifier\providers;

use ExponentPhpSDK\Exceptions\ExpoException;
use ExponentPhpSDK\Exceptions\UnexpectedResponseException;
use ExponentPhpSDK\Expo;
use steroids\notifier\structures\ExpoNotifyParameters;

class ExpoNotifierProvider extends BaseNotifierProvider
{
    private Expo $pushClient;

    public function init()
    {
        parent::init();

        $this->pushClient = Expo::normalSetup();
    }

    /**
     * @param string $templatePath
     * @param ExpoNotifyParameters $params
     * @return mixed|void
     * @throws ExpoException
     * @throws UnexpectedResponseException
     */
    public function send(string $templatePath, $params)
    {
        if (!empty($params->pushToken)) {
            $this->pushClient->subscribe($params->channel, $params->pushToken);
            $notification = [
                'title' => $params->title,
                'body' => $params->message,
                'data' => $params->data
            ];
            $this->pushClient->notify([$params->channel], $notification);
        }
    }
}