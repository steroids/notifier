<?php


namespace steroids\notifier\providers;


use steroids\notifier\models\Notification;
use steroids\notifier\NotifierMessage;
use steroids\notifier\NotifierModule;
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
        $notification = new $this->modelClass([
//            'userId' => \Yii::$app->,
//            'templateName',
            'refId' => $message->params['refId'] ?? null,
            'content' => (string)$message,
            'isRead' => false,
            'paramsJson' => empty($message->params) ? json_encode($message->params) : null
        ]);

        $notification->saveOrPanic();
    }
}