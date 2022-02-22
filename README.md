# Sendinblue Magento2 Module

### Dadolun_SibCore

## Features
Core functionality for Sendinblue - Magento2 integration.

In order to get a complete functionality you need to install at least Dadolun_SibContactSync module: https://github.com/dadolun95/magento2-sib-contact-sync

Another available free module is Dadolun_SibOrderSync: https://github.com/dadolun95/magento2-sib-order-sync

## Installation
You can install this module adding it on app/code folder or with composer.

##### COMPOSER
###### REPMAN.IO (Preferred)
Add Dadolun_Sib repman organization access token on composer:
```
composer config --global --auth http-basic.dadolun_sib.repo.repman.io token e8d9440ca95f7a67c6c70cce55a2352322b89e2fc8c1c7391cd9052578aa6e77
```
Add a "repositories" node on your composer.json:
```
{
    "type": "composer", 
    "url": "https://dadolun_sib.repo.repman.io"
}
```
and executing this command:
```
composer require dadolun95/magento2-sib-order-sync
```
###### VCS 
Same result specifing VCS type nodes on composer repositories section:
```
{
    "type": "vcs",
    "url":  "git@github.com:dadolun95/magento2-sib-core.git"
},
{
    "type": "vcs",
    "url":  "git@github.com:dadolun95/magento2-sib-contact-sync.git"
},
{
    "type": "vcs",
    "url":  "git@github.com:dadolun95/magento2-sib-order-sync.git"
}
```
```
composer require dadolun95/magento2-sib-order-sync
```
##### MAGENTO
Then you'll need to enable the module and update your database:
```
php bin/magento module:enable Dadolun_SibCore
php bin/magento module:enable Dadolun_SibContactSync
php bin/magento module:enable Dadolun_SibOrderSync
php bin/magento setup:upgrade
php bin/magento setup:di:compile
```

##### CONFIGURATION
You must enable the module from "Stores > Configurations > Dadolun > Sendinblue > General" section.
The debugger logs each API v3 call result and response code and also observer calls. 
Remember to disable the debugger on production enviroment cause can slow down the website.

## Contributing
Contributions are very welcome. In order to contribute, please fork this repository and submit a [pull request](https://docs.github.com/en/free-pro-team@latest/github/collaborating-with-issues-and-pull-requests/creating-a-pull-request).
