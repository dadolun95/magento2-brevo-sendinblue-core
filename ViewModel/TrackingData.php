<?php
/**
 * @package     Dadolun_SibCore
 * @copyright   Copyright (c) 2023 Dadolun (https://www.dadolun.com)
 * @license    This code is licensed under MIT license (see LICENSE for details)
 */

namespace Dadolun\SibCore\ViewModel;

use Dadolun\SibCore\Helper\Configuration;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Request\Http;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Registry;
use Magento\Framework\Json\Helper\Data as JsonHelper;

/**
 * Class TrackingData
 * @package Dadolun\SibCore\ViewModel
 */
class TrackingData implements ArgumentInterface
{

    /**
     * @var Configuration
     */
    protected $configurationHelper;

    /**
     * @var Http
     */
    protected $request;

    /**
     * @var UrlInterface
     */
    protected $url;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * @var array
     */
    public $pageData = [];

    /**
     * TrackingData constructor.
     * @param Configuration $configurationHelper
     * @param Http $request
     * @param UrlInterface $url
     * @param Registry $registry
     * @param JsonHelper $jsonHelper
     */
    public function __construct(
        Configuration $configurationHelper,
        Http $request,
        UrlInterface $url,
        Registry $registry,
        JsonHelper $jsonHelper
    )
    {
        $this->configurationHelper = $configurationHelper;
        $this->request = $request;
        $this->url = $url;
        $this->registry = $registry;
        $this->jsonHelper = $jsonHelper;
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

    /**
     * @return string
     */
    public function getPageData() {
        try {
            switch ($this->request->getFullActionName()) {
                case "cms_index_index":
                    $this->pageData = [
                        "homepage" => [
                            "ma_title" => "Homepage",
                            "ma_url" => $this->url->getCurrentUrl()
                        ]
                    ];
                    break;
                case "catalog_product_view":
                    /**
                     * @var Product $product
                     */
                    $product = $this->registry->registry('product');
                    if ($product && $product->getId()) {
                        $this->pageData = [
                            "productpage" => [
                                "ma_title" => $product->getName(),
                                "ma_url" => $this->url->getCurrentUrl(),
                            ]
                        ];
                    }
                    break;
                case "catalog_category_view":
                    /**
                     * @var Category $category
                     */
                    $category = $this->registry->registry('category');
                    if ($category && $category->getId()) {
                        $this->pageData = [
                            "categorypage" => [
                                "ma_title" => $category->getName(),
                                "ma_url" => $this->url->getCurrentUrl(),
                            ]
                        ];
                    }
                    break;
                case "checkout_cart_index":
                    $this->pageData = [
                        "cart" => [
                            "ma_title" => "Cart",
                            "ma_url" => $this->url->getCurrentUrl(),
                        ]
                    ];
                    break;
                case "checkout_index_index":
                    $this->pageData = [
                        "checkout" => [
                            "ma_title" => "Checkout",
                            "ma_url" => $this->url->getCurrentUrl(),
                        ]
                    ];
                    break;
                default:
                    break;
            }
        } catch (\Exception $e) {
            $this->pageData = [];
        }
        return $this->jsonHelper->jsonEncode($this->pageData);
    }
}
