<?php

namespace steroids\sms;

use steroids\notifier\providers\BaseNotifierProvider;
use steroids\notifier\structures\SmsRuNotifyParameters;
use yii\base\Exception;

class SmsRuNotifierProvider extends BaseNotifierProvider
{
    /**
     * @var string
     */
    public string $apiId;

    /**
     * @var array|null
     */
    public ?array $lastResult;

    /**
     * @param string $templatePath
     * @param SmsRuNotifyParameters $params
     * @throws Exception
     */
    public function send(string $templatePath, $params)
    //public function internalSend($to, $text, $from = null)
    {
        $ch = curl_init("http://sms.ru/sms/send");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $post = [
            "api_id" => $this->apiId,
            "to" => $params->to,
            "text" => $params->text,
        ];
        // check from
        if ($params->from) {
            if (!preg_match("/^[a-z0-9_-]+$/i", $params->from) || preg_match('/^[0-9]+$/', $params->from)) {
                throw new Exception('Illegal SMS.RU from number');
            }
            $post['from'] = $params->from;
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $this->lastResult = curl_exec($ch);
        curl_close($ch);

        // Success path
        if (is_string($this->lastResult)) {
            $this->lastResult = explode("\n", $this->lastResult);

            if ($this->lastResult[0] == 100) {
                return; // OK
            }
        }

        // Failure
        ob_start();
        var_dump($this->lastResult);
        $this->lastResult = ob_get_clean();

        throw new Exception('SMS.RU request failed: ' . $this->lastResult);
    }

}
