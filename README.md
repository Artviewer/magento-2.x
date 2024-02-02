
# RozetkaPay_RozetkaPay module
RozetkaPay - payment gateway for Magento 2

## Installation details

For information about a module installation in Magento 2, see [Enable or disable modules](https://devdocs.magento.com/guides/v2.4/install-gde/install/cli/install-cli-subcommands-enable.html).

Please, copy module files to app/code directory.

After that run commands in magento root directory:

php bin/magento module:enable RozetkaPay_RozetkaPay
php bin/magento setup:upgrade
php bin/magento setup:di:compile

Go into admin
Stores -> Configuration -> Sales -> Payment Methods -> RozetkaPay
Configure your API credentials. Click save.
