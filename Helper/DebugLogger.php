<?php
/**
 * @package     Dadolun_SibCore
 * @copyright   Copyright (c) 2023 Dadolun (https://www.dadolun.com)
 * @license     Open Source License
 */

namespace Dadolun\SibCore\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use \Psr\Log\LoggerInterface;

/**
 * Class DebugLogger
 * @package Dadolun\SibCore\Helper
 */
class DebugLogger extends AbstractHelper
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var Configuration
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
     * @param Configuration $configurationHelper
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
