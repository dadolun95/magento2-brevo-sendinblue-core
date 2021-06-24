<?php
/**
 * @package     Dadolun_SibCore
 * @copyright   Copyright (c) 2021 Dadolun (https://github.com/dadolun95)
 * @license     Open Source License
 */

namespace Dadolun\SibCore\Helper;

use Magento\Framework\App\Helper\Context;
use \Dadolun\SibCore\Helper\Configuration;
use \Dadolun\SibCore\Model\SibClientFactory;

/**
 * Class SibClientConnector
 * @package Dadolun\SibCore\Helper
 */
class SibClientConnector extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var SibClientFactory
     */
    protected $sibClientFactory;

    /**
     * @var Configuration
     */
    protected $configHelper;

    /**
     * SibClientConnector constructor.
     * @param SibClientFactory $sibClientFactory
     * @param Configuration $configHelper
     * @param Context $context
     */
    public function __construct(
        SibClientFactory $sibClientFactory,
        Configuration $configHelper,
        Context $context
    )
    {
        $this->sibClientFactory = $sibClientFactory;
        $this->configHelper = $configHelper;
        parent::__construct($context);
    }

    /**
     * @param string $key
     * @return \Dadolun\SibCore\Model\SibClient
     */
    public function createSibClient($key = '')
    {
        if ($key === '') {
            $key = $this->configHelper->getValue('api_key_v3');
        }
        /**
         * @var \Dadolun\SibCore\Model\SibClient $sibClient
         */
        $sibClient = $this->sibClientFactory->create();
        $sibClient->setApiKey($key);
        return $sibClient;
    }

}
