PHPUnit
=======
This doc provides additional information for using PHPUnit with our app. Since we have two separate sites using the same Symfony base, we had to make some changes to how PHPUnit normally runs in Symfony. For basic information on using PHPUnit with Symfony, refer to the links below.

For general information on using PHPUnit in Symfony, read [Symfony's Testing documentation](http://symfony.com/doc/2.1/book/testing.html).

Also check out the [PHPUnit Documentation](http://phpunit.de/manual/current/en/index.html).

Writing Tests
-------------
*TBD*

Running Tests
-------------

To run the tests, run the command below from the base directory of the project:
    
    phpunit -c app/

### Running tests from only one site (cdp or publicaccess)

If you want to run the tests from only one site (cdpAccess or publicAccess), you must specify the testsuite you wish to run like below.

For cdpAccess:
    
    phpunit -c app/ --testsuite cdp
    
Or for publicAccess:

    phpunit -c app/ --testsuite publicaccess
    
### Running tests from specific folder

Currently, you cannot run tests on a specific folder with the command: `phpunit -c app/ src/ICC/ProposalBundle`, because we rely on the top-level test suites in `app/phpunit.xml.dist` to specify which website configuration to load the tests under. 

**Note:** Ticket #1386 was filed for the aboved issue.

To work around the issue, you can add a new testsuite within either the cdp or publicaccess testsuite, depending on which site the test is for, and add the directory of the folder you want to run in that testsuite.

Then you can run:

    phpunit -c app/ --testsuite nameoftestsuite

### Running tests in groups
*TBD*