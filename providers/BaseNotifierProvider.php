<?php

namespace steroids\notifier\providers;

use steroids\notifier\NotifierMessage;
use yii\base\BaseObject;

/**
 * Class BaseNotifierProvider
 * @package steroids\notifier\providers
 */
abstract class BaseNotifierProvider extends BaseObject
{
    /**
     * Provider type (mail, sms or push)
     * @return string
     */
    abstract public static function type();

    /**
     * Provider name
     * @var string
     */
    public string $name = '';

    /**
     * Custom template aliases
     * @var array
     */
    public array $templates = [];

    /**
     * Send message
     * @param NotifierMessage $message
     * @return mixed
     */
    abstract public function send($message);
}
