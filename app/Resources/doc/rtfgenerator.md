# Libreoffice and Unoconv installation Steps RHEL 7

## Install unoconv and libreoffice-headless

As sudo, run:

```bash
yum install unoconv
yum install libreoffice-headless
```

## Set the file path for unoconv to use as temporary storage in parameters.yml

The default is set to the cache directory, like so:

```yaml
# app/config/parameters.yml
parameters:
    path_to_write_rtfs: %kernel.cache_dir%
```

## Verify unoconv is Working

Create test.html file with some html content and then test with following command to generate RTF.

```bash
sudo unoconv -vvv -o test.rtf -f rtf test.html
```

here '-vvv set the debugging level. 3 for this command'

## Troubleshooting

* If test.rtf is not created and if you will see error like `"uno.NoConnectException: Connector : couldn't connect to socket libreoffice"` then install libSM by following command
    ```bash
    sudo yum install libSM
    ```
* To check SELinux status use command
    ```bash
    sudo getenforce
    ```
    SELinux should be Disabled if so then nothing to do or else we can set SELinux to Permissive by following command.
    ```bash
    sudo sudo setenforce=0
    ```
