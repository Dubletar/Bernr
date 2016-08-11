# CDP_2.17.1

## CDP_2.17.1_RC1

### Migrations
Needed to SSH into the server in order to run the migrations with a higher PHP memory limit.

Command run was:

```bash
php -d memory_limit=-1 app/console doctrine:migrations:migrate --em=default -e=prod
```

### RTF Libraries

Install unoconv and libreoffice-headless as sudo

```bash
yum install unoconv
yum install libreoffice-headless
```

Switch user to cdpaccess

```bash
sudo su cdpaccess
```

Update app/config/parameters.yml file with the file path to create the RTF documents
(current parameters.yml.dist file points to an absolute path: web/RTF)

Clear the cache for the change to parameters.yml to be applied:

```bash
app/console cache:clear -e=prod
```

NOTE: The RTF document will fail to generate the first time after installation. Load the RTF page, and then refresh and it should work as expected. This should be a one-time thing. This is a known issue, as mentioned here: https://github.com/dagwieers/unoconv/issues/241

Verify the RTF is generated correctly by going to the Admin Dashboard -> Report Dashboard -> CAH/PCH Result Documentation links
