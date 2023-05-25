<?php
/**
 * @package     Dadolun_SibCore
 * @copyright   Copyright (c) 2023 Dadolun (https://www.dadolun.com)
 * @license    This code is licensed under MIT license (see LICENSE for details)
 */

namespace Dadolun\SibCore\Model\Config\Backend;

use Dadolun\SibCore\Helper\SibClientConnector;
use Dadolun\SibCore\Model\SibClient;
use Dadolun\SibCore\Helper\Configuration;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use SendinBlue\Client\ApiException;
use Magento\Framework\Message\ManagerInterface;

/**
 * Class IntegrationStatus
 * @package Dadolun\SibCore\Model\Config\Backend
 */
class IntegrationStatus extends Value
{
    /**
     * @var Configuration
     */
    protected $configHelper;

    /**
     * IntegrationStatus constructor.
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param Configuration $configHelper
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        Configuration $configHelper,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->configHelper = $configHelper;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * @return Value|void
     */
    public function beforeSave()
    {
        $this->_dataSaveAllowed = false;
        $value = (string)$this->getValue();

        if ($value === "0") {
            $this->configHelper->setValue('api_key_status', 0);
        }
        $this->_dataSaveAllowed = true;
        $this->setValue($value);
    }
}
