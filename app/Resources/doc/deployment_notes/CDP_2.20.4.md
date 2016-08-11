# CDP_2.20.4

## CDP_2.20.4_RC1 (CDP_2.20.4_BETA2)

### Codes Database Migrations (ICC-4853)

Due to the way the migrations for the codes DB were written for [ICC-4853](https://caxyinteractive.atlassian.net/browse/ICC-4853),
there are some extra steps that need to be taken before the migration can be run.

1. Download the [`cdp_codes_images.tar.gz`](https://caxyinteractive.atlassian.net/secure/attachment/14439/cdp_codes_images.tar.gz) file attached to the ticket
1. Transfer the file to the server you are deploying to, and put it in the `/var/www/html/cdpaccess/shared/web/uploads/cdp_codes_images.tar.gz` folder
1. Update permissions on the file so it can be read in the migration, if necessary
1. Run the migration on the codes database (`app/console doctrine:migrations:migrate --em=codes -e=prod`)




