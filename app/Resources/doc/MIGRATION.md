Doctrine Schema Migration Guide
=============================

Creating Migrations
-------------------
PLEASE NOTE: That if you create migration scripts in a branch, you'll want to insure that when you merge the
branch in that your new migrations are versioned *after* the current ones in the repo you're merging into.

If you make database changes, you must follow this guide to assure that others can replicate your 
changes as well as assuring that the changes can safely be applied in a production environment.

In the following examples:
* Instances of ```--em=default``` can be replaced with whichever em you are using (current options are default and codes).
* Instances of ```TIMESTAMP``` is the version number of the migration class (e.g. 20140505151104)

Once you've made a set of DB changes do the following steps:

1. ```php app/console doctrine:migrations:diff --em=default```
2. Edit the appropriate VersionXXXXXX.php file in app/DoctrineMigrations/$PROJECT/*/ to match the output of:

```php app/console doctrine:schema:update --dump-sql --em=default```
3. ```php app/console doctrine:migrations:migrate --em=default```
4. Once you've verified that the changes work as expected, commit the appropriate VersionTIMESTAMP.php file in app/DoctrineMigrations/PROJECT/*/

Applying migrations
-------------------
1. ```php app/console doctrine:migrations:status --em=default```
   (this tells you if any migrations are available)
2. ```php app/console doctrine:migrations:migrate --em=default```

Undoing a migration
-------------------
```php app/console doctrine:migrations:execute --down TIMESTAMP --em=default```

Reapplying a migration after undoing it
-------------------
```php app/console doctrine:migrations:execute TIMESTAMP --em=default```
