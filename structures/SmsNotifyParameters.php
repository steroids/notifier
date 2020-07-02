<?php

namespace steroids\notifier\structures;

class SmsNotifyParameters extends NotifyParameters
{
    /**
     * Number of the receiver
     *
     * @var string
     */
    public string $receiver = '';

    /**
     * Name of sender. If you've letter sender you can use it.
     * Otherwise your sender will be the default sender.
     * Not required.
     *
     * @var string
     */
    public string $sender = '';

    /**
     * Message text
     *
     * @var string
     */
    public string $text = '';
}