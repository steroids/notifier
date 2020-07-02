<?php

namespace steroids\notifier\structures;

class ExpoNotifyParameters extends NotifyParameters
{
    public string $pushToken = '';
    public string $title = '';
    public string $message = '';
    public array $data = [];
    public string $channel = 'default';
}