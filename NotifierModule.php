<?php

namespace steroids\notifier;

use Yii;
use yii\base\Exception;
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

    /**
     * @var array
     */
    public $templates = [];

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
     * @param array $params
     * @param string|null $language
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function send($providerName, $templateName, $params = [], $language = null)
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
        $provider->send($templatePath, $params, $language);

        // Revert back language
        Yii::$app->language = $prevLanguage;
    }
}
