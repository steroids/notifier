<?php

namespace steroids\notifier\providers;

use steroids\notifier\structures\NotifyParameters;
use yii\base\BaseObject;

abstract class BaseNotifierProvider extends BaseObject
{
    /**
     * Provider name
     *
     * @var string
     */
    public string $name = '';

    /**
     * @var array
     */
    public array $templates = [];

    /**
     * @param string $templatePath
     * @param NotifyParameters $params
     * @return mixed
     */
    public abstract function send(string $templatePath, $params);
}
