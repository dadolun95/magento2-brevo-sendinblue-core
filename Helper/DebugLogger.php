<?php
/**
 * @package     Dadolun_SibCore
 * @copyright   Copyright (c) 2021 Dadolun (https://github.com/dadolun95)
 * @license     Open Source License
 */

namespace Dadolun\SibCore\Helper;

use Magento\Framework\App\Helper\Context;
use \Psr\Log\LoggerInterface;

/**
 * Class DebugLogger
 * @package Dadolun\SibCore\Helper
 */
class DebugLogger extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var \Dadolun\SibCore\Helper\Configuration
     */
    protected $configurationHelper;
    /**
     * @var bool
     */
    protected $debugEnabled = false;

    /**
     * DebugLogger constructor.
     * @param Context $context
     * @param LoggerInterface $logger
     * @param \Dadolun\SibCore\Helper\Configuration $configurationHelper
     */
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        Configuration $configurationHelper
    )
    {
        $this->configurationHelper = $configurationHelper;
        $this->logger = $logger;
        $this->debugEnabled = $this->configurationHelper->getFlag('debug_enabled');
        parent::__construct($context);
    }

    /**
     * @param $message
     */
    public function info($message) {
        if ($this->debugEnabled) {
            $this->logger->info($message);
        }
    }

    /**
     * @param $message
     */
    public function error($message) {
        $this->logger->error($message);
    }
}
