<?php
/**
 * @package     Dadolun_SibCore
 * @copyright   Copyright (c) 2021 Dadolun (https://github.com/dadolun95)
 * @license     Open Source License
 */

namespace Dadolun\SibCore\Model\Config\Backend;

use Dadolun\SibCore\Helper\SibClientConnector;
use Sendinblue\Sendinblue\Model\SibClient;
use Dadolun\SibCore\Helper\Configuration;

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
     * ApiKey constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param SibClientConnector $sibClientConnector
     * @param Configuration $configHelper
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        SibClientConnector $sibClientConnector,
        Configuration $configHelper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->sibClientConnector = $sibClientConnector;
        $this->configHelper = $configHelper;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * @return \Magento\Framework\App\Config\Value|void
     * @throws \SendinBlue\Client\ApiException
     */
    public function beforeSave()
    {
        $this->_dataSaveAllowed = false;
        $value = (string)$this->getValue();
        try {
            /**
             * @var \Dadolun\SibCore\Model\SibClient $sibClient
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
                    /* @var \Magento\Framework\App\Config */
                    $this->configHelper->setValue('sendin_config_lang', $lang);
                    $this->configHelper->setValue('sendin_date_format', $dateFormat);
                    $this->configHelper->setValue('api_key_status', 1);
                    $this->_dataSaveAllowed = true;
                } catch (\Exception $e) {
                    $this->_dataSaveAllowed = false;
                }
            } else {
                $this->configHelper->setValue('api_key_status', 0);
            }
        } catch (\Exception $e) {;
            $this->_dataSaveAllowed = false;
        }
        $this->setValue($value);
    }

    /**
     * @param \Dadolun\SibCore\Model\SibClient $sibClient $sibClient
     * @return array|bool
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
