# Brevo Magento2 Module <img src="https://avatars.githubusercontent.com/u/168457?s=40&v=4" alt="magento" />

### Dadolun_SibCore

## Features
Core Sync functionality for Brevo (Sendinblue previously) - Magento2 integration.

In order to get a complete functionality you need to install at least Dadolun_SibContactSync module: https://github.com/dadolun95/magento2-sib-contact-sync

Another available free module is Dadolun_SibOrderSync: https://github.com/dadolun95/magento2-sib-order-sync

## Installation
You can install this module adding it on app/code folder or with composer.

##### COMPOSER
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
