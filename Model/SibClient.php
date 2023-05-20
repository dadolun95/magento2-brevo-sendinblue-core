<?php
/**
 * @package     Dadolun_SibCore
 * @copyright   Copyright (c) 2023 Dadolun (https://www.dadolun.com)
 * @license     Open Source License
 */

namespace Dadolun\SibCore\Model;

use \GuzzleHttp\Client as HttpClient;
use SendinBlue\Client\ApiException;
use \SendinBlue\Client\Configuration as ClientConfiguration;
use \SendinBlue\Client\Api\ContactsApi;
use \SendinBlue\Client\Api\AccountApi;
use \SendinBlue\Client\Api\AttributesApi;
use \SendinBlue\Client\Api\TransactionalSMSApi;
use \SendinBlue\Client\Api\TransactionalEmailsApi;
use \SendinBlue\Client\Api\SMSCampaignsApi;
use \SendinBlue\Client\Api\SendersApi;
use \SendinBlue\Client\Model\CreatedProcessId;
use \SendinBlue\Client\Model\CreateModel;
use \SendinBlue\Client\Model\CreateSmtpEmail;
use \SendinBlue\Client\Model\CreateUpdateContactModel;
use \SendinBlue\Client\Model\GetAccount;
use \SendinBlue\Client\Model\GetAttributes;
use \SendinBlue\Client\Model\GetExtendedContactDetails;
use \SendinBlue\Client\Model\GetFolderLists;
use \SendinBlue\Client\Model\GetFolders;
use \SendinBlue\Client\Model\GetLists;
use \SendinBlue\Client\Model\GetSendersList;
use \SendinBlue\Client\Model\GetSmtpTemplateOverview;
use \SendinBlue\Client\Model\GetSmtpTemplates;
use \SendinBlue\Client\Model\RequestContactImport;
use \SendinBlue\Client\Model\CreateAttribute;
use \SendinBlue\Client\Model\CreateUpdateFolder;
use \SendinBlue\Client\Model\CreateContact;
use SendinBlue\Client\Model\SendSms;
use \SendinBlue\Client\Model\SendTransacSms;
use \SendinBlue\Client\Model\UpdateContact;
use \SendinBlue\Client\Model\SendSmtpEmail;
use \SendinBlue\Client\Model\CreateSmsCampaign;
use \SendinBlue\Client\Model\CreateList;
use \Dadolun\SibCore\Helper\DebugLogger;

/**
 * Class SibClient
 * @package Dadolun\SibCore\Model
 */
class SibClient
{
    const RESPONSE_CODE_OK = 200;
    const RESPONSE_CODE_CREATED = 201;
    const RESPONSE_CODE_ACCEPTED = 202;
    const RESPONSE_NO_CONTENT = 204;

    const NO_ERROR_CODES = [
        self::RESPONSE_CODE_OK,
        self::RESPONSE_CODE_CREATED,
        self::RESPONSE_CODE_ACCEPTED,
        self::RESPONSE_NO_CONTENT
    ];

    private $apiKey = null;
    private $lastResponseCode;
    private $config;

    /**
     * @var DebugLogger
     */
    protected $debugLogger;

    /**
     * SibClient constructor.
     * @param DebugLogger $debugLogger
     */
    public function __construct(
        DebugLogger $debugLogger
    )
    {
        $this->debugLogger = $debugLogger;
    }

    /**
     * @return GetAccount
     * @throws ApiException
     */
    public function getAccount()
    {
        $apiInstance = new AccountApi(
            new HttpClient(),
            $this->config
        );
        $result = $apiInstance->getAccountWithHttpInfo();
        $this->lastResponseCode = $result[1];
        if (in_array($result[1], self::NO_ERROR_CODES)) {
            $this->debugLogger->info(__('getAccount API call response with a %1 code', $result[1]));
        } else {
            $this->debugLogger->error(__('getAccount API call goes on error, response with a %1 code', $result[1]));
        }
        return $result[0];
    }

    /**
     * @return string|null
     */
    public function getApiKey() {
        return $this->apiKey;
    }

    /**
     * @param $key
     * @return $this
     */
    public function setApiKey($key)
    {
        $this->apiKey = trim($key);
        $this->config = ClientConfiguration::getDefaultConfiguration()
            ->setApiKey('api-key', $this->apiKey);
        return $this;
    }

    /**
     * @param $data
     * @return GetLists
     * @throws ApiException
     */
    public function getLists($data)
    {
        $apiInstance = new ContactsApi(
            new HttpClient(),
            $this->config
        );
        $result = $apiInstance->getListsWithHttpInfo($data['limit'], $data['offset']);
        $this->lastResponseCode = $result[1];
        if (in_array($result[1], self::NO_ERROR_CODES)) {
            $this->debugLogger->info(__('getLists API call response with a %1 code', $result[1]));
        } else {
            $this->debugLogger->error(__('getLists API call goes on error, response with a %1 code', $result[1]));
        }
        return $result[0];
    }


    /**
     * @param $folder
     * @param $data
     * @return GetFolderLists
     * @throws ApiException
     */
    public function getListsInFolder($folder, $data)
    {
        $apiInstance = new ContactsApi(
            new HttpClient(),
            $this->config
        );
        $result = $apiInstance->getFolderListsWithHttpInfo($folder, $data['limit'], $data['offset']);
        $this->lastResponseCode = $result[1];
        if (in_array($result[1], self::NO_ERROR_CODES)) {
            $this->debugLogger->info(__('getListsInFolder API call response with a %1 code', $result[1]));
        } else {
            $this->debugLogger->error(__('getListsInFolder API call goes on error, response with a %1 code', $result[1]));
        }
        return $result[0];
    }

    /**
     * @param $data
     * @return CreatedProcessId
     * @throws ApiException
     */
    public function importUsers($data)
    {
        $apiInstance = new ContactsApi(
            new HttpClient(),
            $this->config
        );
        $requestContactImport = new RequestContactImport($data);
        try {
            $result = $apiInstance->importContactsWithHttpInfo($requestContactImport);
        } catch (\Exception $e) {
            $this->debugLogger->info(__($e->getMessage()));
        }
        $this->lastResponseCode = $result[1];
        if (in_array($result[1], self::NO_ERROR_CODES)) {
            $this->debugLogger->info(__('importUsers API call response with a %1 code', $result[1]));
        } else {
            $this->debugLogger->error(__('importUsers API call goes on error, response with a %1 code', $result[1]));
        }
        return $result[0];
    }

    /**
     * @param int $folder
     * @return array
     * @throws ApiException
     */
    public function getAllLists($folder = 0)
    {
        $lists = array("lists" => array(), "count" => 0);
        $offset = 0;
        $limit = 50;
        do {
            if ($folder > 0) {
                $listData = $this->getListsInFolder($folder, array('limit' => $limit, 'offset' => $offset));
            } else {
                $listData = $this->getLists(array('limit' => $limit, 'offset' => $offset));
            }

            if (!$listData->getLists() || empty($listData->getLists())) {
                $listData = array("lists" => array(), "count" => 0);
            } else {
                $listData = $listData->getLists();
            }

            $lists["lists"] = array_merge($lists["lists"], $listData);

            $offset += 50;
        } while (count($lists["lists"]) < count($listData));
        $lists["count"] = count($listData);
        return $lists;
    }

    /**
     * @throws ApiException
     * @return GetAttributes
     */
    public function getAttributes()
    {
        $apiInstance = new AttributesApi(
            new HttpClient(),
            $this->config
        );
        $result = $apiInstance->getAttributesWithHttpInfo();
        $this->lastResponseCode = $result[1];
        if (in_array($result[1], self::NO_ERROR_CODES)) {
            $this->debugLogger->info(__('getAttributes API call response with a %1 code', $result[1]));
        } else {
            $this->debugLogger->error(__('getAttributes API call goes on error, response with a %1 code', $result[1]));
        }
        return $result[0];
    }

    /**
     * @param $type
     * @param $name
     * @param $data
     * @return array
     * @throws ApiException
     */
    public function createAttribute($type, $name, $data)
    {
        $apiInstance = new AttributesApi(
            new HttpClient(),
            $this->config
        );
        $attributeData = $createAttribute = new CreateAttribute($data);
        $result = $apiInstance->createAttributeWithHttpInfo($type, $name, $attributeData);
        $this->lastResponseCode = $result[1];
        if (in_array($result[1], self::NO_ERROR_CODES)) {
            $this->debugLogger->info(__('createAttribute API call response with a %1 code', $result[1]));
        } else {
            $this->debugLogger->error(__('createAttribute API call goes on error, response with a %1 code', $result[1]));
        }
        return $result[0];
    }

    /**
     * @param $data
     * @return GetFolders
     * @throws ApiException
     */
    public function getFolders($data)
    {
        $apiInstance = new ContactsApi(
            new HttpClient(),
            $this->config
        );
        $result = $apiInstance->getFoldersWithHttpInfo($data['limit'], $data['offset']);
        $this->lastResponseCode = $result[1];
        if (in_array($result[1], self::NO_ERROR_CODES)) {
            $this->debugLogger->info(__('getFolders API call response with a %1 code', $result[1]));
        } else {
            $this->debugLogger->error(__('getFolders API call goes on error, response with a %1 code', $result[1]));
        }
        return $result[0];
    }

    /**
     * @return array
     * @throws ApiException
     */
    public function getFoldersAll()
    {
        $folders = array("folders" => array(), "count" => 0);
        $offset = 0;
        $limit = 50;
        do {
            $folderData = $this->getFolders(array('limit' => $limit, 'offset' => $offset));
            $folders["folders"] = array_merge($folders["folders"], $folderData->getFolders());
            $offset += 50;
        } while (count($folders["folders"]) < $folderData->getCount());
        $folders["count"] = $folderData->getCount();
        return $folders;
    }

    /**
     * @param $data
     * @return CreateModel
     * @throws ApiException
     */
    public function createFolder($data)
    {
        $apiInstance = new ContactsApi(
            new HttpClient(),
            $this->config
        );
        $createFolder = new CreateUpdateFolder($data);
        $result = $apiInstance->createFolderWithHttpInfo($createFolder);
        $this->lastResponseCode = $result[1];
        if (in_array($result[1], self::NO_ERROR_CODES)) {
            $this->debugLogger->info(__('createFolder API call response with a %1 code', $result[1]));
        } else {
            $this->debugLogger->error(__('createFolder API call goes on error, response with a %1 code', $result[1]));
        }
        return $result[0];
    }

    /**
     * @param $data
     * @return GetLists
     * @throws ApiException
     */
    public function createList($data)
    {
        $apiInstance = new ContactsApi(
            new HttpClient(),
            $this->config
        );
        $createList = new CreateList($data);
        $result = $apiInstance->createListWithHttpInfo($createList);
        $this->lastResponseCode = $result[1];
        if (in_array($result[1], self::NO_ERROR_CODES)) {
            $this->debugLogger->info(__('createList API call response with a %1 code', $result[1]));
        } else {
            $this->debugLogger->error(__('createList API call goes on error, response with a %1 code', $result[1]));
        }
        return $result[0];
    }

    /**
     * @param $email
     * @return GetExtendedContactDetails
     * @throws ApiException
     */
    public function getUser($email)
    {
        $apiInstance = new ContactsApi(
            new HttpClient(),
            $this->config
        );
        $result = $apiInstance->getContactInfoWithHttpInfo($email);
        $this->lastResponseCode = $result[1];
        if (in_array($result[1], self::NO_ERROR_CODES)){
            $this->debugLogger->info(__('getUser API call response with a %1 code', $result[1]));
        } else {
            $this->debugLogger->error(__('getUser API call goes on error, response with a %1 code', $result[1]));
        }
        return $result[0];
    }

    /**
     * @param $data
     * @return CreateUpdateContactModel
     * @throws ApiException
     */
    public function createUser($data)
    {
        $apiInstance = new ContactsApi(
            new HttpClient(),
            $this->config
        );
        $createContact = new CreateContact($data);
        $result = $apiInstance->createContactWithHttpInfo($createContact);
        $this->lastResponseCode = $result[1];
        if (in_array($result[1], self::NO_ERROR_CODES)) {
            $this->debugLogger->info(__('createUser API call response with a %1 code', $result[1]));
        } else {
            $this->debugLogger->error(__('createUser API call goes on error, response with a %1 code', $result[1]));
        }
        return $result[0];
    }

    /**
     * @param $email
     * @param $data
     * @return array
     * @throws ApiException
     */
    public function updateUser($email, $data)
    {
        $apiInstance = new ContactsApi(
            new HttpClient(),
            $this->config
        );
        $updateContact = new UpdateContact($data);
        $result = $apiInstance->updateContactWithHttpInfo($email, $updateContact);
        $this->lastResponseCode = $result[1];
        if (in_array($result[1], self::NO_ERROR_CODES)){
            $this->debugLogger->info(__('updateUser API call response with a %1 code', $result[1]));
        } else {
            $this->debugLogger->error(__('updateUser API call goes on error, response with a %1 code', $result[1]));
        }
        return $result[0];
    }

    /**
     * @param $data
     * @return SendSms
     * @throws ApiException
     */
    public function sendSms($data)
    {
        $apiInstance = new TransactionalSMSApi(
            new HttpClient(),
            $this->config
        );
        $sendTransacSms = new SendTransacSms($data);
        $result = $apiInstance->sendTransacSmsWithHttpInfo($sendTransacSms);
        $this->lastResponseCode = $result[1];
        if (in_array($result[1], self::NO_ERROR_CODES)){
            $this->debugLogger->info(__('sendSms API call response with a %1 code', $result[1]));
        } else {
            $this->debugLogger->error(__('sendSms API call goes on error, response with a %1 code', $result[1]));
        }
        return $result[0];
    }

    /**
     * @param $data
     * @return CreateSmtpEmail
     * @throws ApiException
     */
    public function sendTransactionalTemplate($data)
    {
        $apiInstance = new TransactionalEmailsApi(
            new HttpClient(),
            $this->config
        );
        $sendSmtpEmail = new SendSmtpEmail($data);
        $result = $apiInstance->sendTransacEmailWithHttpInfo($sendSmtpEmail);
        $this->lastResponseCode = $result[1];
        if (in_array($result[1], self::NO_ERROR_CODES)){
            $this->debugLogger->info(__('sendTransactionalTemplate API call response with a %1 code', $result[1]));
        } else {
            $this->debugLogger->error(__('sendTransactionalTemplate API call goes on error, response with a %1 code', $result[1]));
        }
        return $result[0];
    }

    /**
     * @param $data
     * @return CreateModel
     * @throws ApiException
     */
    public function createSmsCampaign($data)
    {
        $apiInstance = new SMSCampaignsApi(
            new HttpClient(),
            $this->config
        );
        $createSmsCampaign = new CreateSmsCampaign($data);
        $result = $apiInstance->createSmsCampaignWithHttpInfo($createSmsCampaign);
        $this->lastResponseCode = $result[1];
        if (in_array($result[1], self::NO_ERROR_CODES)){
            $this->debugLogger->info(__('createSmsCampaign API call response with a %1 code', $result[1]));
        } else {
            $this->debugLogger->error(__('createSmsCampaign API call goes on error, response with a %1 code', $result[1]));
        }
        return $result[0];
    }


    /**
     * @param $data
     * @return GetSmtpTemplates
     * @throws ApiException
     */
    public function getEmailTemplates($data)
    {
        $apiInstance = new TransactionalEmailsApi(
            new HttpClient(),
            $this->config
        );
        $result = $apiInstance->getSmtpTemplatesWithHttpInfo($data['templateStatus'], $data['limit'], $data['offset']);
        $this->lastResponseCode = $result[1];
        if (in_array($result[1], self::NO_ERROR_CODES)){
            $this->debugLogger->info(__('getEmailTemplates API call response with a %1 code', $result[1]));
        } else {
            $this->debugLogger->error(__('getEmailTemplates API call goes on error, response with a %1 code', $result[1]));
        }
        return $result[0];
    }

    /**
     * @return array
     * @throws ApiException
     */
    public function getAllEmailTemplates()
    {
        $templates = array("templates" => array(), "count" => 0);
        $offset = 0;
        $limit = 50;
        do {
            /**
             * @var GetSmtpTemplates $templateData
             */
            $templateData = $this->getEmailTemplates(array('templateStatus' => 'true', 'limit' => $limit, 'offset' => $offset));
            $loadedTemplates = [];
            if (!$templateData->getTemplates() || $templateData->getTemplates() === null) {
                $loadedTemplates = array("templates" => array(), "count" => 0);
            } else {
                foreach($templateData->getTemplates() as $template) {
                    $loadedTemplates["templates"][] = [
                        "name" => $template->getName(),
                        "id" => $template->getId(),
                        "isActive" => $template->getIsActive(),
                        "htmlContent" => $template->getHtmlContent()
                    ];
                }
                $loadedTemplates["count"] = $templateData->getCount();
            }
            $templates["templates"] = array_merge($templates["templates"], $loadedTemplates['templates']);
            $offset += 50;
        } while (count($templates["templates"]) < $loadedTemplates["count"]);
        $templates["count"] = count($templates["templates"]);
        return $templates;
    }

    /**
     * @param $id
     * @return GetSmtpTemplateOverview
     * @throws ApiException
     */
    public function getTemplateById($id)
    {
        $apiInstance = new TransactionalEmailsApi(
            new HttpClient(),
            $this->config
        );
        $result = $apiInstance->getSmtpTemplateWithHttpInfo($id);
        $this->lastResponseCode = $result[1];
        if (in_array($result[1], self::NO_ERROR_CODES)){
            $this->debugLogger->info(__('getTemplateById API call response with a %1 code', $result[1]));
        } else {
            $this->debugLogger->error(__('getTemplateById API call goes on error, response with a %1 code', $result[1]));
        }
        return $result[0];
    }

    /**
     * @param $data
     * @return CreateSmtpEmail
     * @throws ApiException
     */
    public function sendEmail($data)
    {
        $apiInstance = new TransactionalEmailsApi(
            new HttpClient(),
            $this->config
        );
        $sendSmtpEmail = new SendSmtpEmail($data);
        $result = $apiInstance->sendTransacEmailWithHttpInfo($sendSmtpEmail);
        $this->lastResponseCode = $result[1];
        if (in_array($result[1], self::NO_ERROR_CODES)){
            $this->debugLogger->info(__('sendEmail API call response with a %1 code', $result[1]));
        } else {
            $this->debugLogger->error(__('sendEmail API call goes on error, response with a %1 code', $result[1]));
        }
        return $result[0];
    }

    /**
     * @return GetSendersList
     * @throws ApiException
     */
    public function getSenders()
    {
        $apiInstance = new SendersApi(
            new HttpClient(),
            $this->config
        );
        $result = $apiInstance->getSendersWithHttpInfo();
        $this->lastResponseCode = $result[1];
        if (in_array($result[1], self::NO_ERROR_CODES)){
            $this->debugLogger->info(__('getSenders API call response with a %1 code', $result[1]));
        } else {
            $this->debugLogger->error(__('getSenders API call goes on error, response with a %1 code', $result[1]));
        }
        return $result[0];
    }

    /**
     * @return int
     */
    public function getLastResponseCode()
    {
        return $this->lastResponseCode;
    }
}
