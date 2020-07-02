<?php

namespace steroids\notifier\structures;

use steroids\auth\UserInterface;

class MailNotifyParameters extends NotifyParameters
{
    public string $email = '';
    public ?UserInterface $user;
    public array $composeParameters = [];
}