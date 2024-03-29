<?php

namespace steroids\notifier;

use steroids\notifier\providers\BaseNotifierProvider;
use steroids\notifier\providers\StoreDbNotifierProvider;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use steroids\core\traits\ModuleProvidersTrait;
use steroids\core\base\Module;
use steroids\notifier\providers\MailerNotifierProvider;
use yii\helpers\FileHelper;

class NotifierModule extends Module
{
    use ModuleProvidersTrait;

    const PROVIDER_TYPE_MAIL = 'mail';
    const PROVIDER_TYPE_SMS = 'sms';
    const PROVIDER_TYPE_PUSH = 'push';
    const PROVIDER_TYPE_STORE = 'store';

    /**
     * Custom template aliases
     * @var array
     */
    public array $templates = [];

    /**
     * General layout for mail
     * @var string
     */
    public string $mailLayout = '@steroids/core/views/layout-mail';

    /**
     * Provider default classes
     * @var array|string[]
     */
    public array $providersClasses = [
        'mailer' => MailerNotifierProvider::class,
        'store' => StoreDbNotifierProvider::class,
    ];

    /**
     * Send message via provider
     * @param string|NotifierMessage $message
     * @param string|null $templateName
     * @param mixed $to Receiver email/phone/token
     * @param array $params
     * @param string|null $language
     * @throws InvalidConfigException
     * @throws \Exception
     */
    public function send($message, string $templateName = null, $to = null, $params = [], $language = null)
    {
        $language = $language ?: Yii::$app->language;

        if(is_string($message)){
            $message = static::getDefaultNotifierMessage($message, $to, $language, $templateName, $params);
        }

        foreach ($message->destinations as $providerType => $to){
            $providerName = $this->resolveProviderName($providerType, $message->templateName, $to, $params);

            // Get providers
            $provider = $this->getProvider($providerName);
            if (!$provider) {
                throw new Exception('Not found notifier provider "' . $providerName . '".');
            }

            // Get template path
            $templatePath = $this->resolveTemplatePath($provider, $message->templateName, $to, $params, $language);

            // Get layout
            $layoutPath = null;
            if ($providerType === self::PROVIDER_TYPE_MAIL && $this->mailLayout) {
                $layoutPath = Yii::getAlias($this->mailLayout);

                // Add extension
                if (pathinfo($layoutPath, PATHINFO_EXTENSION) === '') {
                    $layoutPath = $layoutPath . '.' . Yii::$app->view->defaultExtension;
                }
            }

            $message->path = $templatePath;
            $message->to = $to;
            $message->layoutPath = $layoutPath;
            $message->view = Yii::$app->view;

            // Send
            $provider->send($message);
        }
    }

    /**
     * Resolve provider name with simple logic:
     *  - Get first in `providers` array with relative provider type
     *  - Or return first provider name from array
     * @param string $providerType
     * @param string $templateName
     * @param $to
     * @param array $params
     * @return int|mixed|string
     * @throws InvalidConfigException
     */
    protected function resolveProviderName(string $providerType, string $templateName, $to, array $params)
    {
        if (empty($this->providers)) {
            throw new InvalidConfigException('Notifier providers is not configured.');
        }

        foreach ($this->providers as $name => $provider) {
            /** @var BaseNotifierProvider $className */
            $className = is_object($provider)
                ? get_class($provider)
                : (ArrayHelper::getValue($provider, 'class') ?: ArrayHelper::getValue($this->providersClasses, $name));
            if ($className && $className::type() === $providerType) {
                return $name;
            }
        }

        return array_keys($this->providers)[0];
    }

    /**
     * Resolve template path in some locations (for example, templateName = auth/confirm):
     *  - app/auth/views/mail/ru-RU/confirm.php
     *  - app/auth/views/mail/ru/confirm.php
     *  - app/auth/views/mail/confirm.php
     *  - app/auth/views/ru-RU/confirm.php
     *  - app/auth/views/ru/confirm.php
     *  - app/auth/views/confirm.php
     *  - vendor/steroids/auth/views/mail/ru-RU/confirm.php
     *  - vendor/steroids/auth/views/mail/ru/confirm.php
     *  - vendor/steroids/auth/views/mail/confirm.php
     *  - vendor/steroids/auth/views/ru-RU/confirm.php
     *  - vendor/steroids/auth/views/ru/confirm.php
     *  - vendor/steroids/auth/views/confirm.php
     * @param BaseNotifierProvider $provider
     * @param string $templateName
     * @param $to
     * @param array $params
     * @param string $language
     * @return string
     * @throws Exception
     */
    protected function resolveTemplatePath(BaseNotifierProvider $provider, string $templateName, $to, array $params, string $language)
    {

        $path = ArrayHelper::getValue($provider->templates, $templateName)
            ?: ArrayHelper::getValue($this->templates, $templateName)
            ?: $templateName;

        // Check is real path
        if (substr($path, 0, 1) === '/' || strpos($path, '@') !== false || is_file($path)) {
            return $path;
        }

        // Find in module
        $parts = explode('/', $path);
        if (count($parts) > 0) {
            // Find in module views
            $fileName = array_pop($parts);

            // Add extension
            if (pathinfo($fileName, PATHINFO_EXTENSION) === '') {
                $fileName = $fileName . '.' . Yii::$app->view->defaultExtension;
            }
            /** @var Module $module */
            $module = Yii::$app;

            foreach ($parts as $moduleName) {
                $module = $module->getModule($moduleName);
            }

            // Module dir
            $modulePaths = [$module->viewPath];

            // Add library (steroids) module path
            if ($module instanceof Module) {
                $libPath = $module->libraryBasePath;
                if ($libPath) {
                    $modulePaths[] = $libPath . '/views';
                }
            }

            foreach ($modulePaths as $modulePath) {
                $dirs = [
                    // Find in type dir [moduleDir]/views/[providerType]/[fileName]
                    implode(DIRECTORY_SEPARATOR, [$modulePath, $provider::type(), $fileName]),

                    // Find in views [moduleDir]/views/[fileName]
                    implode(DIRECTORY_SEPARATOR, [$modulePath, $fileName]),
                ];

                foreach ($dirs as $dir) {
                    $path = FileHelper::localize($dir, $language);
                    if (is_file($path)) {
                        break 2;
                    }
                }
            }
        }
        if (!is_file($path)) {
            throw new Exception('Not found notifier template "' . $templateName . '", path: ' . $path);
        }

        return $path;
    }

    /**
     * @param string $message
     * @param $to
     * @param string $language
     * @param string|null $templateName
     * @param array $params
     * @return NotifierMessage
     */
    public static function getDefaultNotifierMessage(string $message, $to, string $language, ?string $templateName, array $params): NotifierMessage
    {
        return new NotifierMessage([
            'destinations' => [
                $message => $to,
            ],
            'language' => $language,
            'templateName' => $templateName,
            'params' => $params
        ]);
    }
}
