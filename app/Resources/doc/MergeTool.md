BookMergeBundle
=======

###Setup
 - Un-comment `new ICC\BookMergeBundle\BookMergeBundle(),` in  `app/AppKernel.php` 
 - un-comment `- { resource: "@BookMergeBundle/Resources/config/config.yml" }` in `app/config/config.yml`
 - set proper parameters for the following block in `app/config/parameters.yml`
```yml
    # The following are only needed if you're working with MSSQL data imports
    #orig_codes_database_driver:   pdo_mysql
    #orig_codes_database_host:     localhost
    #orig_codes_database_port:     ~
    #orig_codes_database_name:     cdp_codes_from_mssql
    #orig_codes_database_user:
    #orig_codes_database_password:
```
 - Put populate database configured above with new book
````
    mysql -u {username} -p {database_name} < sql_file.sql
```
  - Note: the file may need to be edited to remove any lines that create or use a different database name than is desired.
  - If there is an existing database you may need to drop it if you have foreign key warnings
   
###Usage
 - Check to see if there is any schema updates needed 
```
    app/console doctrine:schema:update --website=publicaccess --em=orig_codes --dump-sql
```
 - Review SQL and update schema if needed
```
    app/console doctrine:schema:update --website=publicaccess --em=orig_codes --force
```

###Other stuff

####Useful SQL
 - select counts of all tables
```SQL
SELECT 
	(SELECT COUNT(*) FROM   `cdp_book`) AS book,
	(SELECT COUNT(*) FROM   `cdp_book_part`) AS book_part,
	(SELECT COUNT(*) FROM   `cdp_chapter`) AS chapter  ,
	(SELECT COUNT(*) FROM   `cdp_chapter_part`) AS chapter_part,
	(SELECT COUNT(*) FROM   `cdp_content`) AS content,
	(SELECT COUNT(*) FROM   `cdp_equation`) AS equation,
	(SELECT COUNT(*) FROM   `cdp_figure`) AS figure,
	(SELECT COUNT(*) FROM   `cdp_figure_footnotes`) AS figure_footnotes  ,
	(SELECT COUNT(*) FROM   `cdp_footnote`) AS footnotes ,
	(SELECT COUNT(*) FROM   `cdp_list_items`) AS list_items ,
	(SELECT COUNT(*) FROM   `cdp_list_items_content`) AS list_items_content ,
	(SELECT COUNT(*) FROM   `cdp_lists`) AS lists  ,
	(SELECT COUNT(*) FROM   `cdp_lists_list_items`) AS lists_list_items  ,
	(SELECT COUNT(*) FROM   `cdp_section`) AS section  ,
	(SELECT COUNT(*) FROM   `cdp_section_content`) AS section_content  ,
	(SELECT COUNT(*) FROM   `cdp_table`) AS count_table  ,
	(SELECT COUNT(*) FROM   `cdp_table_footnotes`) AS table_footnotes  ,
	(SELECT COUNT(*) FROM   `cdp_table_group`) AS table_group;
```
 - Clear table
```SQL
set foreign_key_checks = 0;
TRUNCATE `cdp_book`;
TRUNCATE `cdp_book_part`;
TRUNCATE `cdp_chapter`;
TRUNCATE `cdp_chapter_part`;
TRUNCATE `cdp_content`;
TRUNCATE `cdp_equation`;
TRUNCATE `cdp_figure`;
TRUNCATE `cdp_figure_footnotes`;
TRUNCATE `cdp_footnote`;
TRUNCATE `cdp_list_items`;
TRUNCATE `cdp_list_items_content`;
TRUNCATE `cdp_lists`;
TRUNCATE `cdp_lists_list_items`;
TRUNCATE `cdp_section`;
TRUNCATE `cdp_section_content`;
TRUNCATE `cdp_table`;
TRUNCATE `cdp_table_footnotes`;
TRUNCATE `cdp_table_group`;
set foreign_key_checks = 1;
```
