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

### Requests

|        ######Supported           |                                    URL                                           |
|----------------------------------|----------------------------------------------------------------------------------|
|**(GetMatchHistory)**             | https://api.steampowered.com/IDOTA2Match_570/GetMatchHistory/v001/               |
|**(GetMatchDetails)**             | https://api.steampowered.com/IDOTA2Match_570/GetMatchDetails/v001/               |
|**(GetPlayerSummaries)**          | https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/                |
|**(GetLeagueListing)**            | https://api.steampowered.com/IDOTA2Match_570/GetLeagueListing/v0001/             |
|**(GetTeamInfoByTeamID)**         | https://api.steampowered.com/IDOTA2Match_570/GetTeamInfoByTeamID/v001/           |
|**(GetHeroes)**                   | https://api.steampowered.com/IEconDOTA2_570/GetHeroes/v0001/                     |
|       #######Unsupported         |                                    URL                                           |
|----------------------------------|----------------------------------------------------------------------------------|
|**(EconomySchema)**               | https://api.steampowered.com/IEconItems_570/GetSchema/v0001/                     |
|**(GetLiveLeagueGames)**          | https://api.steampowered.com/IDOTA2Match_570/GetLiveLeagueGames/v0001/           |
|**(GetMatchHistoryBySequenceNum)**| https://api.steampowered.com/IDOTA2Match_570/GetMatchHistoryBySequenceNum/v0001/ |

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
<?php
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
print_r($match->get_data_array());
print_r($match->get_slot(0)->get_data_array());
```

#### I want get all matches with some player
````php
<?php
require_once ('config.php');
$matches_mapper_web = new matches_mapper_web();
$matches_mapper_web->set_account_id(93712171);
$matches_short_info = $matches_mapper_web->load();
foreach ($matches_short_info AS $key=>$match_short_info) {
    $match_mapper = new match_mapper_web($key);
    $match = $match_mapper->load();
    $mm = new match_mapper_db();
    $mm->save($match);
}
````

#### Get player info
````php
<?php
require_once ('config.php');
$players_mapper_web = new players_mapper_web();
$info = $players_mapper_web->add_id('76561198067833250')->load();
print_r($info);
````
Player's id you can get via player::convert_id('xxxxx') method (xxxxx - its DotA ID).

#### Get team info
````php
<?php
require_once ('config.php');
$teams_mapper_web = new teams_mapper_web();
$teams = $teams_mapper_web->set_team_id(2)->set_teams_requested(2)->load();
print_r($teams);
````

#### Get current heroes list
````php
<?php
require_once ('config.php');
$heroes_mapper = new heroes_mapper();
$heroes = $heroes_mapper->load();
print_r($heroes);
````
$heroes - array with numeric indexes (heroes ids)

#### Get leagues list
````php
<?php
require_once ('config.php');
$leagues_mapper = new leagues_mapper();
$leagues = $leagues_mapper->load();
print_r($leagues);
````
$leagues - array with numeric indexes (leagues ids)