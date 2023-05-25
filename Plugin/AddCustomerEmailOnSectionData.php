<?php
/**
 * @package     Dadolun_SibCore
 * @copyright   Copyright (c) 2023 Dadolun (https://www.dadolun.com)
 * @license    This code is licensed under MIT license (see LICENSE for details)
 */

namespace Dadolun\SibCore\Plugin;

use Dadolun\SibCore\Helper\Configuration;
use Magento\Customer\CustomerData\Customer;
use Magento\Customer\Helper\Session\CurrentCustomer;

/**
 * Class AddCustomerEmailOnSectionData
 * @package Dadolun\SibCore\Plugin
 */
class AddCustomerEmailOnSectionData
{
    /**
     * @var CurrentCustomer
     */
    private $currentCustomer;

    /**
     * @var Configuration
     */
    private $configurationHelper;

    /**
     * AddCustomerEmailOnSectionData constructor.
     * @param CurrentCustomer $currentCustomer
     * @param Configuration $configurationHelper
     */
    public function __construct(
        CurrentCustomer $currentCustomer,
        Configuration $configurationHelper
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->configurationHelper = $configurationHelper;
    }

    /**
     * @param Customer $subject
     * @param $result
     * @return mixed
     */
    public function afterGetSectionData(Customer $subject, $result)
    {
        if ($this->configurationHelper->getFlag('tracking_enabled') && $this->currentCustomer->getCustomerId()) {
            $customer = $this->currentCustomer->getCustomer();
            $result['email'] = $customer->getEmail();
        }
        return $result;
    }
}
