## Application parameters
Symfony application parameters are stored in `app/config/parameters.yml`. An example of which parameters are actually required for cdpACCESS can be found in `app/config/parameters.yml.dist`.
* Database parameters:

    ```yml
        database_driver:       pdo_mysql
        database_host:         127.0.0.1
        database_port:         ~
        database_name:         cdpaccess
        database_user:         cdpaccess
        database_password:     password

        codes_database_driver:       pdo_mysql
        codes_database_host:         127.0.0.1
        codes_database_port:         ~
        codes_database_name:         cdpcodes
        codes_database_user:         cdpcodes
        codes_database_password:     password
    ```
* The `secret` parameter should be a random string composed of letters, numbers, and symbols - recommended to be about 32 characters. http://symfony.com/doc/current/reference/configuration/framework.html#secret

    ```yml
        secret: ThisTokenIsNotSoSecretChangeIt
    ```
* The `prod_tracking` parameter should only be enabled on production environments. This will enable application analytics.

    ```yml
        prod_tracking: false
    ```
* This parameter defines the location of the wkhtmltopdf binary necessary for generating PDFs. Installation packages for wkhtmltopdf can be found on their website http://wkhtmltopdf.org/downloads.html.
Unless you install this to a specific location, you probably won't need to touch this parameter.

    ```yml
        wkhtmltopdf_bin_path: /usr/local/bin/wkhtmltopdf
    ```
* Information about the next parameter `path_to_write_monograph_pdfs` can be found here: [BulkSaveOnlineMods.md](BulkSaveOnlineMods.md)