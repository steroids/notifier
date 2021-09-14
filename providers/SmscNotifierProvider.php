<?php

namespace steroids\notifier\providers;

use steroids\notifier\exceptions\NotifierException;
use steroids\notifier\NotifierModule;
use yii\helpers\Json;

/**
 * Registration page
 * https://smsc.ru/reg/
 */
class SmscNotifierProvider extends BaseNotifierProvider
{
    /**
     * Логин Клиента
     * @var string
     */
    public string $login;

    /**
     * Пароль Клиента (можно добавить или изменить на странице - https://smsc.ru/passwords/)
     * @var string
     */
    public string $password;

    /**
     * Имя отправителя, отображаемое в телефоне получателя. Разрешены английские буквы, цифры, пробел и некоторые
     * символы. Длина – 11 символов или 15 цифр. Все имена регистрируются в личном кабинете - https://smsc.ru/senders/
     * @var string
     */
    public ?string $sender = '';

    /**
     * @inheritDoc
     */
    public static function type()
    {
        return NotifierModule::PROVIDER_TYPE_SMS;
    }

    /**
     * @inheritDoc
     */
    public function send($message)
    {
        $ch = curl_init('https://smsc.ru/sys/send.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $to = $message->to;
        $to = preg_replace('/[^0-9]+/', '', $to);
        $to = '+' . preg_replace('/^8/', '7', $to);

        $post = [
            'login' => $this->login,
            'psw' => $this->password,
            'phones' => $to,
            'mes' => (string)$message,
            'fmt' => 3, // json
        ];
        if ($this->sender) {
            $post['sender'] = $this->sender;
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $response = curl_exec($ch);
        curl_close($ch);

        // Success path
        if (!is_string($response)) {
            throw new NotifierException("SMSC request failed: $response. \n\n Request: " . print_r($post, true));
        }

        $json = Json::decode($response);
        if (isset($json['error'])) {
            throw new NotifierException("SMSC request error: $response. \n\n Request: " . print_r($post, true));
        }
    }

}
