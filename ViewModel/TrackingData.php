<?php
/**
 * @package     Dadolun_SibCore
 * @copyright   Copyright (c) 2023 Dadolun (https://www.dadolun.com)
 * @license    This code is licensed under MIT license (see LICENSE for details)
 */

namespace Dadolun\SibCore\ViewModel;

use Dadolun\SibCore\Helper\Configuration;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class TrackingData
 * @package Dadolun\SibCore\ViewModel
 */
class TrackingData implements ArgumentInterface
{

    /**
     * @var Configuration
     */
    private $configurationHelper;

    /**
     * TrackingData constructor.
     * @param Configuration $configurationHelper
     */
    public function __construct(
        Configuration $configurationHelper
    )
    {
        $this->configurationHelper = $configurationHelper;
    }

    /**
     * @return mixed|null
     */
    public function getAutomationKey()
    {
        if ($this->configurationHelper->getFlag('enabled') && $this->configurationHelper->getFlag('tracking_enabled')) {
            return $this->configurationHelper->getValue('automation_key');
        }
        return null;
    }
}
