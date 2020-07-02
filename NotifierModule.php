<?php

namespace steroids\notifier;

use steroids\notifier\structures\NotifyParameters;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use steroids\core\traits\ModuleProvidersTrait;
use steroids\auth\providers\BaseAuthProvider;
use steroids\core\base\Module;
use steroids\notifier\providers\MailerNotifierProvider;

class NotifierModule extends Module
{
    use ModuleProvidersTrait;

    const PROVIDER_TYPE_MAIL = 'mail';
    const PROVIDER_TYPE_SMS = 'sms';
    const PROVIDER_TYPE_PUSH = 'push';

    public array $templates = [];

    /**
     * @var BaseAuthProvider[]|array
     */
    public array $providers;

    public array $providersClasses = [
        'mailer' => MailerNotifierProvider::class,
    ];

    /**
     * @param string $providerName
     * @param string $templateName
     * @param NotifyParameters $params
     * @param string|null $language
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function send($providerName, $templateName, $params, $language = null)
    {
        $providerName = $providerName ?: array_keys($this->providers)[0];

        // Get provider
        $provider = $this->getProvider($providerName);
        if (!$provider) {
            throw new Exception('Not found notifier provider "' . $providerName . '".');
        }

        // Get template path
        $templatePath = ArrayHelper::getValue($provider->templates, $templateName)
            ?: ArrayHelper::getValue($this->templates, $templateName);
        if (!$templatePath) {
            throw new Exception('Not found notifier template "' . $templateName . '".');
        }

        // Set language for render
        $prevLanguage = Yii::$app->language;
        if ($language) {
            Yii::$app->language = $language;
        }

        // Send
        $provider->send($templatePath, $params);

        // Revert back language
        Yii::$app->language = $prevLanguage;
    }
}
