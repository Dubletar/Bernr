cdpACCESS Production Installation
=================================

## Pre-requisites and conditions

* This document is intended to guide the deployment and installation of cdpACCESS into a *production* environment. For simplicity, we will install the web and database layers onto the same server. Scaling the architecture for performance is beyond the scope of this document.
* Specifically this details installation for [RedHat Enterprise Linux 7][RHEL7]. RHEL 7 is a subscription based platform. It is important that you maintain an active subscription to remain in compliance with RedHat usage agreements and to receive regular system and package updates and security patches. Documentation on RHEL 7 subscription management can be found here: https://access.redhat.com/articles/433903
* Before deploying cdpACCESS into production, you should be familiar and comfortable setting up the application into a development environment. You can find documentation for development set up in the app's main [README.md][].
* The `app/config/parameters.yml` file for your production
  environment should be secured and backed up. It should not be committed to source
  control repositories or shared.

[RHEL7]: https://access.redhat.com/documentation/en-US/Red_Hat_Enterprise_Linux/7/html/7.0_Release_Notes/
[README.md]: https://github.com/InternationalCodeCouncil/cdpaccess/blob/develop/README.md

## System Requirements
* [RedHat Enterprise Linux 7][RHEL7]
* PHP 5.4 with extensions: [Intl][], MySQL, [Enchant][], [Multibyte String][MbString], [GD][] [Alternative PHP Cache][APC] (Optional, but recommended)
* MySQL 5
* Apache 2 with [mod_ssl][]
* [Git][]
* [wkhtmltopdf][] - v0.12.2.1

[Intl]: http://php.net/manual/en/book.intl.php
[Enchant]: http://php.net/manual/en/book.enchant.php
[MbString]: http://php.net/manual/en/book.mbstring.php
[GD]: http://php.net/manual/en/book.image.php
[mod_ssl]: http://httpd.apache.org/docs/2.2/mod/mod_ssl.html
[Git]: https://git-scm.com/
[wkhtmltopdf]: http://wkhtmltopdf.org/index.html
[APC]: http://php.net/manual/en/book.apc.php
[dejavu]: http://dejavu-fonts.org/wiki/Main_Page

## System setup
1. Update packages:

    ```bash
    [root@rhel7 ~]# yum update
    ```

2. Add [RedHat's optional server package repository](http://linuxconfig.org/installation-of-missing-php-mbstring-on-rhel-7-linux) for php-mbstring and php-enchant:

    ```bash
    [root@rhel7 ~]# subscription-manager repos --enable=rhel-7-server-optional-rpms
    Repository 'rhel-7-server-optional-rpms' is enabled for this system.
    ```
3. Install system dependencies:

    ```bash
    [root@rhel7 ~]# yum install git httpd mod_ssl mariadb-server mariadb php php-mysql php-xml php-intl php-enchant php-mbstring php-soap php-gd libcurl
    ```
4. Restart httpd:

    ```bash
    [root@rhel7 ~]# apachectl restart
    ```
5. Run `mysql_secure_installation` and follow the script's instructions. You should disable root access to the MariaDB server remotely and set a [complex][] root password.

    ```bash
    [root@rhel7 ~]# mysql_secure_installation
    ```

6. Install [wkhtmltopdf] and [DejaVu Fonts][dejavu]:

    ```bash
    [root@rhel7 ~]# wget http://downloads.sourceforge.net/wkhtmltopdf/wkhtmltox-0.12.2.1_linux-centos7-amd64.rpm
    [root@rhel7 ~]# yum install wkhtmltox-0.12.2.1_linux-centos7-amd64.rpm
    [root@rhel7 ~]# yum install dejavu-serif-fonts dejavu-sans-fonts
    ```

7. Install [Alternative PHP Cache][APC]. While PHP >= 5.5 has its own opcode cache (making APC unnecessary), RHEL 7 ships with PHP 5.4.16

    ```bash
    [root@rhel7 ~]# yum install php-pear php-devel httpd-devel pcre-devel gcc make
    [root@rhel7 ~]# pecl install apc
    ```
    The installation will ask a few questions about which, if any additional features you'd like to install or turn on/off. You can safely accept all of the default options.
    After the install completes, enable the extension and restart apache:

    ```bash
    [root@rhel7 ~]# echo "extension=apc.so" > /etc/php.d/apc.ini
    [root@rhel7 ~]# apachectl restart
    ```
    APC provides a web interface with detailed information on the cache (memory usage, hits and misses, cache entries). By default it is not accessible so you need to copy the file /usr/share/pear/apc.php to somewhere you can browse to.
    Now from your browser, go to http://domain.com/apc.php. It's best to let the app run for a couple of days to get accurate statistics.

8. Ensure that SELinux is disabled. If it is not, [disable it][SELinux].

    ```bash
    [root@rhel7 ~]# sestatus
    SELinux status:                 disabled
    ```

9. It is best practice to create a new system user for the explicit purpose of deploying and managing the app. Create a new user `cdpaccess` and set a password:

    ```bash
    [root@rhel7 ~]# useradd cdpaccess
    [root@rhel7 ~]# passwd cdpaccess
    ```
    For more options on creating users, see the [documentation](https://access.redhat.com/documentation/en-US/Red_Hat_Enterprise_Linux/5/html/Deployment_Guide/s2-users-add.html).
    
[complex]: http://www.strongpasswordgenerator.com/
[SELinux]: https://access.redhat.com/documentation/en-US/Red_Hat_Enterprise_Linux/7/html/SELinux_Users_and_Administrators_Guide/sect-Security-Enhanced_Linux-Working_with_SELinux-Enabling_and_Disabling_SELinux.html#sect-Security-Enhanced_Linux-Enabling_and_Disabling_SELinux-Disabling_SELinux
## App Installation

1. Switch to your deployment user. Generate an SSH key pair and add the public key to the ICC Github repository deploy keys. For help with this step, see [Github's excellent documentation][SSH-key].

    ```bash
    [root@rhel7 ~]# su cdpaccess
    [cdpaccess@rhel7 root]$ ssh-keygen -t rsa -b 4096 -C "your_email@example.com"
    ```
2. Move into the default apache web directory. Clone the repository and change to that directory. All other commands assume that this is
   your current working directory.

    ```bash
    [cdpaccess@rhel7 root]$ cd /var/www/html/
    [cdpaccess@rhel7 html]$ git clone git@github.com:InternationalCodeCouncil/cdpaccess.git
    [cdpaccess@rhel7 html]$ cd cdpaccess
    ```
3. Setup the AvectraBundle `parameters.yml` file as described in the [AvectraBundle documentation](/src/ICC/AvectraBundle/README.md).
4. Download Composer

    ```bash
    $ curl -s https://getcomposer.org/installer | php
    ```
5. Install vendor files with Composer. This step might take several minutes as it
   downloads all the dependencies. The `SYMFONY_ENV=prod` part tells the `post-install-cmd` scripts to run in the prod environment.

    ```bash
    $ SYMFONY_ENV=prod php composer.phar install --no-dev
    ```
If this is a new server, you may see a PHP Warning related to your system's timezone setting. Follow the instructions and update the date.timezone setting on your server's `/etc/php.ini` file.

6. Respond to the questions after the "Some parameters are missing. Please provide them."
   prompt. You can safely accept the default values for all these, and you will edit them
   later in `app/config/parameters.yml`.  When you've done this, you will get a big
   gleaming error message! That is OK.
7. Configure the permissions as described in the [Permissions](#permissions) section below.
8. Import copies of the `cdpaccess` and `cdp_codes` databases into your MySQL server:

    ```bash
    [root@rhel7 ~]# mysqladmin create cdpaccess
    [root@rhel7 ~]# mysqladmin create cdp_codes
    [root@rhel7 ~]# mysql -e "grant all privileges on cdpaccess.* to cdpaccess@localhost identified by 'password'"
    [root@rhel7 ~]# mysql -e "grant all privileges on cdp_codes.* to cdp_codes@localhost identified by 'password'"
    [root@rhel7 ~]# mysqladmin flush-privileges
    [root@rhel7 ~]# mysql --max-allowed-packet=1G cdpaccess < cdpaccess.sql
    [root@rhel7 ~]# mysql --max-allowed-packet=1G cdp_codes < cdp_codes.sql
    ```
Be sure to replace the 'password' with a complex password. Keep your MySQL usernames and passwords handy as you will need them in the next step.
9. Configure your `app/config/parameters.yml` file as described in the [Application parameters documentation](application_parameters.md).
10. Continue with the instructions listed below in the section titled [Running the app](#running-the-app).

[SSH-key]: https://help.github.com/articles/generating-ssh-keys/

## Permissions

This comes from [the Symfony documentation][1]. It has been modified based on the assumption that the default httpd user `apache` is being used and the deployment user is named `cdpaccess`. The intention here is to allow
the web server user to create files (cache files, logs, and session data) that can still be read and
modified by the owner of the project directory. Run these commands as root from the project's root directory.

```bash
[root@rhel7 cdpaccess]# setfacl -R -m u:apache:rwX -m u:cdpaccess:rwX app/cache app/logs app/var
[root@rhel7 cdpaccess]# setfacl -dR -m u:apache:rwX -m u:cdpaccess:rwX app/cache app/logs app/var
```

[1]: http://symfony.com/doc/2.3/book/installation.html#checking-symfony-application-configuration-and-setup

## Running the app

While in development, Symfony recommends that you use the [PHP built in web server][built-in]. However, in production you will need a fully-featured web server. All of the active cdpACCESS deployments currently use Apache. For consistency and simplicity this documentation recommends the same.

Configuring Apache for cdpACCESS on RHEL 7 is as simple as adding a VirtualHost file in `/etc/httpd/conf.d/` and reloading Apache. Here is an example VirtualHost file which assumes you have an SSL certificate installed and that all traffic should be forced to HTTPS:

```bash
<VirtualHost *:80>
        ServerName test.cdpaccess.com
        Redirect permanent / https://test.cdpaccess.com/
</VirtualHost>

<VirtualHost *:443>
        ServerAdmin system@caxy.com
        ServerName test.cdpaccess.com

        DocumentRoot /var/www/html/cdpaccess/web
        <Directory /var/www/html/cdpaccess/web>
                AllowOverride All
                Order Allow,Deny
                Allow from All
        </Directory>

        ErrorLog /var/log/httpd/cdpaccess-error.log
        CustomLog /var/log/httpd/cdpaccess-access.log common

        SSLEngine On
        SSLCertificateFile /etc/pki/tls/certs/SSL_CERT.crt
        SSLCertificateKeyFile  /etc/pki/tls/private/PRIVATE_KEY.key
        SSLCACertificateFile /etc/pki/tls/certs/INTERMEDIATE-CA_SSL.crt
</VirtualHost>
```

Add this content to a file called `cdpaccess.conf` and place it in `/etc/httpd/conf.d/`. Finally, restart Apache:

```bash
[root@rhel7 cdpaccess]# apachectl restart
```

For more information, see Symfony's documentation on [Configuring a Web Server][].

[built-in]: http://symfony.com/doc/2.3/cookbook/web_server/built_in.html
[Configuring a Web Server]: http://symfony.com/doc/2.3/cookbook/configuration/web_server_configuration.html

## Updates

```bash
$ git pull
$ export SYMFONY_ENV=prod
$ php composer.phar install --no-dev
$ ./app/console doctrine:migrations:migrate --em=default
$ bash prod-setup.sh
```

If "bash prod-setup.sh" script complains about permissions that prevent it deleting files,
it would be safe and appropriate to run `sudo rm -rf app/cache/*` from the project's root.

## Where to get help
More documentation and resources can be found in the [app/Resources/doc](/app/Resources/doc) directory.

