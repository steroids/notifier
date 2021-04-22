<?php


namespace steroids\notifier\providers;


use steroids\notifier\models\Notification;
use steroids\notifier\NotifierMessage;
use steroids\notifier\NotifierModule;
use yii\helpers\ArrayHelper;
use function PHPUnit\Framework\isEmpty;

class StoreDbNotifierProvider extends BaseNotifierProvider
{

    public $modelClass = Notification::class;

    /**
     * @inheritDoc
     */
    public static function type()
    {
        return NotifierModule::PROVIDER_TYPE_STORE;
    }

    /**
     * @inheritDoc
     */
    public function send($message)
    {
        $refId = ArrayHelper::remove($message->params, 'refId');
        $params = !empty($message->params) ? json_encode($message->params) : null;

        $notification = new $this->modelClass([
            'userId' => $message->userId,
            'templateName' => $message->templateName,
            'refId' => $refId,
            'content' => (string)$message,
            'paramsJson' => $params
        ]);

        $notification->saveOrPanic();
    }
}