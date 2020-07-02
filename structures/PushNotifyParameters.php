<?php

namespace steroids\notifier\structures;

class PushNotifyParameters extends NotifyParameters
{
    /**
     * @var string
     */
    public string $pushToken = '';

    /**
     * Title of push message
     *
     * @var string
     */
    public string $title = '';

    /**
     * Message text
     *
     * @var string
     */
    public string $message = '';

    /**
     * @var array
     */
    public array $data = [];

    /**
     * Subscribes a given channel to the Expo Push Notifications.
     *
     * @var string
     */
    public string $channel = 'default';
}