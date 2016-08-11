Codes Database Information
==========================

Codes Data and cdpAccess (CDP) Coupling
---------------------------------------
The book codes (BookBundle) and CDP proposal codes (ProposalBundle) are stored in separate mySQL databases and do not have
SQL-managed relationships, but the CDP codes need to reference the original book code when calculating changes to content.

In order to store the reference from a CDP code to a book code, we store the section number and title
on the CDP code row in the original_section and original_title columns, respectively.

PLEASE NOTE: If the codes database is updated and changes are made to section titles or section numbers, a migration must be created
to update the CDP proposal codes that refer to the changed sections through original_section and original_title columns.