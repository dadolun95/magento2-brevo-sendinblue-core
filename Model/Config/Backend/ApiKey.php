<?php
/**
 * @package     Dadolun_SibCore
 * @copyright   Copyright (c) 2023 Dadolun (https://www.dadolun.com)
 * @license     Open Source License
 */

namespace Dadolun\SibCore\Model\Config\Backend;

use Dadolun\SibCore\Helper\SibClientConnector;
use Dadolun\SibCore\Model\SibClient;
use Dadolun\SibCore\Helper\Configuration;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use SendinBlue\Client\ApiException;
use Magento\Framework\Message\ManagerInterface;

/**
 * Class ApiKey
 * @package Dadolun\SibCore\Model\Config\Backend
 */
class ApiKey extends \Magento\Framework\App\Config\Value
{
    const SIB_FR_COUNTRY_CODE = 'france';
    const SIB_FR_DATE_FORMAT = 'mm-dd-yyyy';
    const SIB_FR_LANG = 'fr';
    const SIB_DEFAULT_DATE_FORMAT = 'dd-mm-yyyy';
    const SIB_DEFAULT_LANG = 'en';
    const MAGENTO_FOLDER_NAME = 'magento';
    const MAGENTO_LIST_NAME = 'subscriptions';

    /**
     * @var SibClientConnector
     */
    protected $sibClientConnector;

    /**
     * @var Configuration
     */
    protected $configHelper;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * ApiKey constructor.
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param SibClientConnector $sibClientConnector
     * @param Configuration $configHelper
     * @param ManagerInterface $messageManager
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        SibClientConnector $sibClientConnector,
        Configuration $configHelper,
        ManagerInterface $messageManager,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->sibClientConnector = $sibClientConnector;
        $this->configHelper = $configHelper;
        $this->messageManager = $messageManager;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * @return \Magento\Framework\App\Config\Value|void
     * @throws ApiException
     */
    public function beforeSave()
    {
        $this->_dataSaveAllowed = false;
        $value = (string)$this->getValue();
        try {
            /**
             * @var SibClient $sibClient
             */
            $sibClient = $this->sibClientConnector->createSibClient($value);
            $sibClient->setApiKey($value);
            $account = $sibClient->getAccount();

            if (SibClient::RESPONSE_CODE_OK == $sibClient->getLastResponseCode()) {
                try {
                    $this->checkMagentoFolderList($sibClient);
                    $lang = self::SIB_DEFAULT_LANG;
                    $dateFormat = self::SIB_DEFAULT_DATE_FORMAT;
                    if (self::SIB_FR_COUNTRY_CODE == strtolower($account->getAddress()->getCountry())) {
                        $dateFormat = self::SIB_FR_DATE_FORMAT;
                        $lang = self::SIB_FR_LANG;
                    }
                    /* @var Config */
                    $this->configHelper->setValue('sendin_config_lang', $lang);
                    $this->configHelper->setValue('sendin_date_format', $dateFormat);
                    $this->configHelper->setValue('api_key_status', 1);
                    $this->_dataSaveAllowed = true;
                } catch (\Exception $e) {
                    $this->_dataSaveAllowed = false;
                }
            } else {
                $this->configHelper->setValue('api_key_status', 0);
                $this->messageManager->addErrorMessage(__('Invalid API key setted up'));
            }
        } catch (\Exception $e) {;
            $this->_dataSaveAllowed = false;
            $this->messageManager->addErrorMessage(__('Invalid API key setted up'));
        }
        $this->setValue($value);
    }

    /**
     * @param SibClient $sibClient $sibClient
     * @return array|bool
     * @throws ApiException
     */
    private function checkMagentoFolderList($sibClient)
    {
        $dataApi = array(
            "offset" => 0,
            "limit" => 50
        );
        $folderResp = $sibClient->getFolders($dataApi);
        $sibMainListArray = array();
        $folders = false;
        if (!empty($folderResp['folders'])) {
            foreach ($folderResp['folders'] as $value) {
                if ($value['name'] === self::MAGENTO_FOLDER_NAME) {
                    $listResp = $sibClient->getAllLists($value['id']);
                    if (!empty($listResp['lists']) && $listResp['count']) {
                        foreach ($listResp['lists'] as $val) {
                            if ($val['name'] === self::MAGENTO_LIST_NAME) {
                                $sibMainListArray['main_id'] = $val['id'];
                            }
                        }
                    }
                }
            }
            if (count($sibMainListArray) > 0) {
                $folders = $sibMainListArray;
            } else {
                $folders = false;
            }
        }
        if ($folders === false || !isset($folders['main_id'])) {

            $data = ['name' => self::MAGENTO_FOLDER_NAME];
            $folderRes = $sibClient->createFolder($data);
            $folderId = $folderRes['id'];

            $data = [
                'name' => self::MAGENTO_LIST_NAME,
                'folderId' => $folderId
            ];
            $listResult = $sibClient->createList($data);
            $listId = $listResult['id'];
            $folders['main_id'] = $listId;
        }
        return $folders;
    }
}
