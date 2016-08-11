SnappyPDF (wkhtmltopdf)
===

The snappy PDF bundle requres the installation of the package for your system from http://wkhtmltopdf.org/downloads.html.

**_Note_**: You will need to use a package from the wkhtmltopdf site as not all distrubution's packages are current and have patched QT libaries as are needed for proper generation.

After installation you will need to update the parameters.yml `wkhtmltopdf_bin_path` to the proper path if its not `/usr/local/bin/wkhtmltopdf` (the default location in packeages for common systems)