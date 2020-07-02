<?php

namespace steroids\notifier\structures;

class SmsRuNotifyParameters extends NotifyParameters
{
    public string $to = '';
    public string $from = '';
    public string $text = '';
}