<?php

namespace steroids\notifier\structures;

class MailNotifyParameters extends NotifyParameters
{
    /**
     * Address of the receiver
     *
     * @var string
     */
    public string $to = '';

    /**
     * Parameters (name-value pairs) that will be extracted
     * and made available in the view file.
     *
     * @var array
     */
    public array $params = [];
}