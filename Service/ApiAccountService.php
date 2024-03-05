<?php

namespace ClickSend\Service;

use ClickSend;
use ClickSend\ApiException;
use ClickSend\ClickSend as ModuleClickSend;

class ApiAccountService
{
    private ClickSend\Api\AccountApi $apiInstance;

    public function __construct()
    {
        $config = ClickSend\Configuration::getDefaultConfiguration()
            ->setUsername(ModuleClickSend::getConfigValue(ModuleClickSend::API_USERNAME_CONFIG_KEY))
            ->setPassword(ModuleClickSend::getConfigValue(ModuleClickSend::API_KEY_CONFIG_KEY));

        $this->apiInstance = new ClickSend\Api\AccountApi(null, $config);
    }

    /**
     * @throws ApiException
     * @throws \JsonException
     */
    public function getAccount()
    {
        return json_decode($this->apiInstance->accountGet(), false, 512, JSON_THROW_ON_ERROR);
    }
}