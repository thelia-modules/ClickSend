<?php

namespace ClickSend\Service;

use ClickSend\ApiException;
use ClickSend\ClickSend as ModuleClickSend;
use ClickSend;

class ApiSmsService
{
    private ClickSend\Api\SMSApi $apiInstance;

    public function __construct()
    {
        $config = ClickSend\Configuration::getDefaultConfiguration()
            ->setUsername(ModuleClickSend::getConfigValue(ModuleClickSend::API_USERNAME_CONFIG_KEY))
            ->setPassword(ModuleClickSend::getConfigValue(ModuleClickSend::API_KEY_CONFIG_KEY));

        $this->apiInstance =  new ClickSend\Api\SMSApi(null, $config);
    }

    public function createSmsMessage(string $source, string $body, string $phone): ClickSend\Model\SmsMessage
    {
        $msg = new ClickSend\Model\SmsMessage();

        $msg->setSource($source)
            ->setBody($body)
            ->setTo($phone)
        ;

        return $msg;
    }

    /**
     * @throws ApiException
     * @throws \JsonException
     */
    public function sendSms(array $messages)
    {
        $body = new ClickSend\Model\SmsMessageCollection();
        $body->setMessages($messages);

        return json_decode($this->apiInstance->smsSendPost($body), false, 512, JSON_THROW_ON_ERROR);
    }
}