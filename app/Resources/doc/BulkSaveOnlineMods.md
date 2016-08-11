# Required steps for bulk saving online modifications as PDFs

### parameters.yml
- Copy the **path_to_write_monograph_pdfs** parameter from the **app/config/parameters.yml.dist** file to your **app/config/parameters.yml** file
- Choose a directory where you'd like to save the PDFs and enter the **full path** as the value of the **path_to_write_monograph_pdfs** parameter. Example:

        path_to_write_monograph_pdfs: /home/cdpaccess/pdfs

### Directory permissions
- Create the directory in your path paramter if you haven't already:
```sh
$ mkdir /home/cdpaccess/pdfs
```
- **IMPORTANT:** Allow your webserver user to write to this directory as well. Be sure to replace the **<path_to_write_monograph_pdfs>** below with the actual path you've defined in parameters.yml above.
```sh
$ HTTPDUSER=`ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
$ sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX <path_to_write_monograph_pdfs>
$ sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX <path_to_write_monograph_pdfs>
```
    See http://symfony.com/doc/current/book/installation.html#book-installation-permissions for more details