# Installing the Enchant PHP extension

The tinyMCE plugin we use for spellcheck requires the Enchant PHP extension installed on the system. These instructions are for installing the extension on Ubuntu 14.04, however installing on other systems should be similar.

First, get the PHP extension:
```
sudo apt-get install php5-enchant
```

Next, enable the extension. You can do so by adding this line to your php.ini file:
```
extension=php_enchant.so
```

Restart Apache and the extension should then be enabled:
```
sudo service apache2 restart
```
# Configuration

Full Configuration Options (showing defaults):

``` yaml
# app/config/stfalcon/tinymce_spell_check.yml

tinymce_spell_check:
    engine:               enchant # alternative option is pspell
    enchant_dicts_path:   ~
    additional_word_list: []
    pspell:
        mode:     ~
        spelling: ~
        jargon:   ~
        encoding: ~

```

## Add Additional Words to the Dictionary

The spellchecker engine will use the default engine provided by the library, but it is possible to add additional words to the dictionary by adding them to the `additional_word_list` config option.

For example:

``` yaml
# app/config/stfalcon/tinymce_spell_check.yml

tinymce_spell_check:
    ...
    additional_word_list:
        - newword
        - anotherword
        - onemoreword
```

Currently the configuration is set up to use a parameter for the additional_word_list (`%tinymce_spell_check.additional_word_list%`), which produces the same result as above.
