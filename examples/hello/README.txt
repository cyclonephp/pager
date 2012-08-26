You have successfully installed the hello-world example of the pager library.

Post-installation steps:
Create a new MySQL database connection to be used or configure an existing one
in app/config/db/default.php

The simplest way to do it is:
 mysql -u root -p
 create database cyclone_test charset utf8;
 grant all on cyclone_test.* to 'cyclone_test'@'localhost' identified by 'cyclone_test';

When your database connection is ready you can load the test schema and testdata to the
database. You can find it in app/schema.sql

Example:
mysql -u cyclone_test --password=cyclone_test cyclone_test < app/schema.sql