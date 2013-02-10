#dota2-api

### About

1. **What is it?**
This is PHP code for processing DotA 2 API-requests.

2. **What can it do?**
It can get match-list for some criteria, get match-info for single match, get steam-profile info for users.
AND save all this data in MySQL database.

3. **What I need to work with it?**
First of all you need web-server with **PHP 5.3+** ( **PDO** and **cURL** should be enabled) and **MySQL 5**. Then look at install section.

### Install

1. Open file **config.php** and find MySQL connection settings. Write there your 'db-host', 'db-name', 'username', 'password'. 'table-prefix' leave as is.

2. In this file also replace API_KEY with your own (you can get it on the http://steamcommunity.com/dev/apikey).

3. Connect to your mysql-server with any tool (phpmyadmin, heidisql etc) and execute code from the file **db.sql**.

### How to use it

#### Load some match-info
```php
<?php
require_once ('config.php');
$mm = new match_mapper_web(121995119);
$match = $mm->load();
```
$match - it's an object with all match data including slots info, ability-upgrades (if provided) and pick, bans (if cm-mode).

#### Save match-info in the database
```php
<?php
require_once ('config.php');
$mm = new match_mapper_web(121995119);
$match = $mm->load();
$saver = new match_mapper_db();
$saver->save($match);
```
match_mapper_db will check if match with $match->get('match_id') exists in the db and select method for save (insert or update).


#### Work with match-object
```php
require_once ('config.php');
$mm = new match_mapper_web(121995119);
$match = $mm->load();
echo $match->get('match_id');
echo $match->get('start_time');
echo $match->get('game_mode');
$slots = $match->get_all_slots();
foreach($slots as $slot) {
    echo $slot->get('last_hits');
}
```