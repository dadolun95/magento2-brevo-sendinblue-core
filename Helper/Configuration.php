<?php
/**
 * @package     Dadolun_SibCore
 * @copyright   Copyright (c) 2021 Dadolun (https://github.com/dadolun95)
 * @license     Open Source License
 */

namespace Dadolun\SibCore\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Configuration
 * @package Dadolun\SibCore\Helper
 */
class Configuration extends \Magento\Framework\App\Helper\AbstractHelper
{

    const CONFIG_SECTION_PATH = 'dadolun_sib';
    const CONFIG_GROUP_PATH = 'sendinblue';
    const MODULE_CONFIG_PATH = self::CONFIG_SECTION_PATH . DS . self::CONFIG_GROUP_PATH;

    /**
     * @var WriterInterface
     */
    protected $configWriter;

    /**
     * Configuration constructor.
     * @param Context $context
     * @param WriterInterface $configWriter
     */
    public function __construct(
        Context $context,
        WriterInterface $configWriter
    )
    {
        $this->configWriter = $configWriter;
        parent::__construct($context);
    }

    /**
     * Check if sendinblue is authenticated
     *
     * @return bool
     */
    public function isServiceActive() {
        if ($this->getFlag('enabled') && $this->getValue('api_key_v3') !== null && $this->getValue('api_key_status')) {
            return true;
        }
        return false;
    }

    /**
     * Get module config value
     *
     * @param $val
     * @return mixed
     */
    public function getValue($val)
    {
        return $this->scopeConfig->getValue(self::MODULE_CONFIG_PATH . DS . $val, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Set module config value
     *
     * @param $pathVal
     * @param $val
     * @param string $scope
     * @param int $scopeId
     */
    public function setValue($pathVal, $val, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeId = 0)
    {
        $this->configWriter->save(self::MODULE_CONFIG_PATH . DS . $pathVal, $val, $scope, $scopeId);
    }

    /**
     * Set magento config value by path
     *
     * @param $path
     * @param $val
     * @param string $scope
     * @param int $scopeId
     */
    public function setPathValue($path, $val, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeId = 0)
    {
        $this->configWriter->save($path, $val, $scope, $scopeId);
    }

    /**
     * Get module config flag
     *
     * @param $val
     * @return mixed
     */
    public function getFlag($val)
    {
        return $this->scopeConfig->isSetFlag(self::MODULE_CONFIG_PATH . DS . $val, ScopeInterface::SCOPE_STORE);
    }

}
