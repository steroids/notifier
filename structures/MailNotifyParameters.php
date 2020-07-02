<?php

namespace steroids\notifier\structures;

class MailNotifyParameters extends NotifyParameters
{
    /**
     * Address of the receiver
     *
     * @var string
     */
    public string $receiver = '';

    /**
     * Address of the sender.
     * You may also specify sender name in addition to email address using format:
     * `[email => name]`.
     *
     * @var array|string
     */
    public $sender;

    /**
     * Parameters (name-value pairs) that will be extracted
     * and made available in the view file.
     *
     * @var array
     */
    public array $composeParameters = [];
}