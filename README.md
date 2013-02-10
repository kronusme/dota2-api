#dota2-api

### About

1. **What is it?**

This is PHP code for processing DotA 2 API-requests.

2. **What can it do?**

It can get match-list for some criteria, get match-info for single match, get steam-profile info for users.
AND save all this data in MySQL database.

3. **What I need to work with it?**

First of all you need web-server **with PHP 5.3+** (with **PDO** and **cURL**) and **MySQL 5**. Then look at install section.

### Install

Open file **config.php** and find MySQL connection settings. Write there your 'db-host', 'db-name', 'username', 'password'. 'table-prefix' leave as is.
Connect to your mysql-server with any tool (phpmyadmin, heidisql etc) and execute code in the file **db.sql**.
