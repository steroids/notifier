<?php

namespace steroids\notifier;

use Yii;
use yii\base\BaseObject;
use yii\web\View;

/**
 * Class NotifierMessage
 * @package steroids\notifier
 */
class NotifierMessage extends BaseObject
{
    /**
     * Absolut path to template
     * @var string
     */
    public string $path = '';

    /**
     * Receiver email/phone/token
     * @var string
     */
    public string $to = '';

    /**
     * Sender email/phone/token
     * @var string
     */
    public ?string $from = '';

    /**
     * Mail layout path
     * @var string
     */
    public ?string $layoutPath = '';

    /**
     * Message title or subject
     * @var string
     */
    public ?string $title = '';

    /**
     * Language
     * @var string
     */
    public ?string $language = '';

    /**
     * @var string
     */
    public string $templateName;

    /**
     * Key-value array with any parameters
     * @var array
     */
    public ?array $params = [];

    /**
     * Instance of View application component
     * @var View
     */
    public View $view;

    /**
     * ProviderType-To
     * User id
     * @var int
     */
    public int $userId;

    /**
     * @var array
     */
    public array $destinations;

    /**
     * Render message content (html or text)
     * @return string
     */
    public function __toString()
    {
        // Set language for render
        $prevLanguage = Yii::$app->language;
        Yii::$app->language = $this->language;

        // Add self to params
        $this->params['message'] = $this;

        $content = $this->view->renderFile($this->path, $this->params);
        if ($this->layoutPath) {
            return $this->view->renderFile($this->layoutPath, array_merge($this->params, [
                'content' => $content,
            ]));
        }

        // Revert back language
        Yii::$app->language = $prevLanguage;

        return $content;
    }
}
