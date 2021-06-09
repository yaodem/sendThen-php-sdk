# SendThen PHP SDK

The SendThen PHP SDK makes it simpler to integrate communications into your PHP applications using the it's REST API. Using the SDK, you will be able to send SMS, check balance account, check your contacts and groups.

**Supported PHP Versions**: This SDk works with PHP 7.4 and greaters.

## Installation

### To install compsoer
#### Globaly in Mac

1. Download the latest version of [Composer](https://getcomposer.org/download/)
2. Run the following command in Terminal:

        $ php ~/Downloads/composer.phar --version
3. Run the following command to make it executable:

        $ cp ~/Downloads/composer.phar /usr/local/bin/composer
        $ sudo chmod +x /usr/local/bin/composer        
        $ Make sure you move the file to bin directory.
4. To check if the path has **/usr/local/bin**, use

        $ echo $PATH
    If the path is different, use the following command to update the $PATH:    
            
        $ export PATH = $PATH:/usr/local/bin
        $ source ~/.bash_profile 
4. You can also check the version of Composer by running the following command:
        
        $ composer --version

#### Globally in Linux

1. Run the following command:
        
        $ curl -sS https://getcomposer.org/installer | php

2. Run the following command to make the composer.phar file as executable:
        
        $ chmod +x composer.phar

3. Run the following command to make Composer globally available for all system users:
        
        $ mv composer.phar /usr/local/bin/composer

#### Windows 10

1. Download and run the [Windows Installer](https://getcomposer.org/download/) for Composer.

    **Note:** Make sure to allow Windows Installer for Composer to make changes to your **php.ini** file.

2. If you have any terminal windows open, close all instances and open a fresh terminal instance.
3. Run the Composer command.
        
        $ composer -V

### Steps to install SendThen PHP Package

- To install the **stable release**, run the following command in the project directory:
        
        $ composer require yaodem/sendthen-php-sdk

- To install a **specific release**, run the following command in the project directory:
        
        $ composer require yaodem/sendthen-php-sdk:release version

- To test the features in the **beta release**, run the following command in the project directory:
        
        $ composer require yaodem/sendthen-php-sdk:v1.0.1-beta1

- Alternatively, you can download this source and run
        
        $ composer install
