#dota2-api

[![Build Status](https://travis-ci.org/kronusme/dota2-api.png?branch=master)](https://travis-ci.org/kronusme/dota2-api)
[![Coverage Status](https://coveralls.io/repos/kronusme/dota2-api/badge.png?branch=master)](https://coveralls.io/r/kronusme/dota2-api?branch=master)
[![License](https://poser.pugx.org/kronusme/dota2-api/license.svg)](https://packagist.org/packages/kronusme/dota2-api)
[![Latest Stable Version](https://poser.pugx.org/kronusme/dota2-api/v/stable.svg)](https://packagist.org/packages/kronusme/dota2-api)
[![Dependencies](https://www.versioneye.com/user/projects/5469ed86a760ce7bc8000027/badge.svg)](https://www.versioneye.com/user/projects/5469ed86a760ce7bc8000027)


### Trunk brunch is unstable - use [v1.1.0](https://github.com/kronusme/dota2-api/tree/v1.1.0)!


### About

1. **What is it?**
This is PHP code for processing DotA 2 API-requests.

2. **What can it do?**
It can get match-list for some criteria, get match-info for single match, get steam-profile info for users.
AND save all this data in MySQL database. For more information see - "How to use it".

3. **What I need to work with it?**
First of all you need web-server with **PHP 5.3+** ( **PDO** and **cURL** should be enabled) and **MySQL 5**. Then look at install section.

### Install

1. Open file **config.php** and find MySQL connection settings. Write there your 'db-host', 'db-name', 'username', 'password'. 'table-prefix' leave as is.

2. In the file **api-key.php** replace API_KEY with your own (you can get it on the http://steamcommunity.com/dev/apikey).

3. Connect to your mysql-server with any tool (phpmyadmin, heidisql etc) and execute code from the file **db_latest.sql**.

### Requests
|           Type               |                                    URL                                           |
|------------------------------|----------------------------------------------------------------------------------|
|        **Supported**         |                                                                                  |
| GetMatchHistory              | https://api.steampowered.com/IDOTA2Match_570/GetMatchHistory/v001/               |
| GetMatchDetails              | https://api.steampowered.com/IDOTA2Match_570/GetMatchDetails/v001/               |
| GetPlayerSummaries           | https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/                |
| GetLeagueListing             | https://api.steampowered.com/IDOTA2Match_570/GetLeagueListing/v0001/             |
| GetLiveLeagueGames           | https://api.steampowered.com/IDOTA2Match_570/GetLiveLeagueGames/v0001/           |
| GetTeamInfoByTeamID          | https://api.steampowered.com/IDOTA2Match_570/GetTeamInfoByTeamID/v001/           |
| GetHeroes                    | https://api.steampowered.com/IEconDOTA2_570/GetHeroes/v0001/                     |
| GetTournamentPrizePool       | https://api.steampowered.com/IEconDOTA2_570/GetTournamentPrizePool/v1/           |
| GetGameItems                 | https://api.steampowered.com/IEconDOTA2_570/GetGameItems/v0001/                  |
|       **Unsupported**        |                                                                                  |
| EconomySchema                | https://api.steampowered.com/IEconItems_570/GetSchema/v0001/                     |
| GetMatchHistoryBySequenceNum | https://api.steampowered.com/IDOTA2Match_570/GetMatchHistoryBySequenceNum/v0001/ |

### How to use it

#### IMPORTANT!
Before parsing and saving leagues matches to your DB, make sure that you've saved leagues to the DB (using `leagues_mapper_web`!
If you try to save some "public" matches, you should REMOVE `foreign key` for field `leagueid` in the table `matches`!

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

#### I want get last 25 matches with some player
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
$players_info = $players_mapper_web->add_id('76561198067833250')->add_id('76561198058587506')->load();
foreach($players_info as $player_info) {
    echo $player_info->get('realname');
    echo '<img src="'.$player_info->get('avatarfull').'" alt="'.$player_info->get('personaname').'" />';
    echo '<a href="'.$player_info->get('profileurl').'">'.$player_info->get('personaname').'\'s steam profile</a>';
}
print_r($players_info);
````
Player's id you can get via player::convert_id('xxxxx') method (xxxxx - its DotA ID).

#### Get team info
````php
<?php
$teams_mapper_web = new teams_mapper_web();
$teams = $teams_mapper_web->set_team_id(2)->set_teams_requested(2)->load();
foreach($teams as $team) {
    echo $team->get('name');
    echo $team->get('rating');
    echo $team->get('country_code');
    print_r($team->get_all_leagues_ids());
}
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

#### Get current items list
````php
<?php
require_once ('config.php');
$items_mapper_web = new items_mapper_web();
$items_info = $items_mapper_web->load();
print_r($items_info);
foreach($items_info as $item) {
    echo $item->get('id');
    echo $item->get('name');
    echo $item->get('cost');
    echo $item->get('secret_shop');
    echo $item->get('side_shop');
    echo $item->get('recipe');
    echo $item->get('localized_name');
}
````

#### Save received from web items list to db
````php
<?php
require_once ('config.php');
$items_mapper_web = new items_mapper_web();
$items = $items_mapper_web->load();
$items_mapper_db = new items_mapper_db();
$items_mapper_db->save($items);
````

#### Get current items list from db
````php
<?php
require_once ('config.php');
$items_mapper_db = new items_mapper_db();
$items_info = $items_mapper_db->load();
print_r($items_info);
foreach($items_info as $item) {
    echo $item->get('id');
    echo $item->get('name');
    echo $item->get('cost');
    echo $item->get('secret_shop');
    echo $item->get('side_shop');
    echo $item->get('recipe');
    echo $item->get('localized_name');
}
````

#### Get leagues list
````php
<?php
require_once ('config.php');
$leagues_mapper_web = new leagues_mapper_web();
$leagues = $leagues_mapper_web->load();
foreach($leagues as $league) {
    echo $league->get('description');
    if ($league->get('tournament_url')) {
        echo $league->get('tournament_url');
    }
 }
````
$leagues - array with numeric indexes (leagues ids)

#### Get leagues prize pool
````php
$league_prize_pool_mapper_web = new league_prize_pool_mapper_web();
$league_prize_pool_mapper_web->set_league_id(600);
$prize_pool_info = $league_prize_pool_mapper_web->load();
print_r($prize_pool_info);
echo $prize_pool_info['prize_pool'];
echo $prize_pool_info['league_id'];
echo $prize_pool_info['status']; // may be undefined
````

````php
$prize_pool_mapper_db = new league_prize_pool_mapper_db();
$pp = $prize_pool_mapper_db->set_league_id(600)->load();
foreach($pp as $date=>$prize_pool) {
    echo $date.' - $ '.number_format($prize_pool, 2);
}
````

#### Get live leagues matches
````php
<?php
require_once ('config.php');
$league_mapper = new league_mapper(22); // set league id (can be get via leagues_mapper)
$games = $league_mapper->load();
print_r($games);
````
$games - array of live_match objects

#### Get matches from local db
````php
<?php
require_once('config.php');
$matches_mapper_db = new matches_mapper_db();
$matches_mapper_db->set_league_id(29)->set_matches_requested(1);
$matches_info = $matches_mapper_db->load();
print_r($matches_info);
````

#### Delete match(es) from local db
````php
<?php
require_once('config.php');
$matches_mapper_db = new matches_mapper_db();
$matches_mapper_db->delete(array(151341579, 151401247));

$mm = new match_mapper_db();
$mm->delete(151341579);
````

#### Get info about abilities, heroes, items, games mods, lobby types etc
````php
<?php
require_once('config.php');
$abilities = new abilities();
$abilities->parse();
$abilities-get_data_by_id(5172); // return array for ability with id 5172 (BeastMaster Inner Beast)
// same, because there are no thumbs for abilities
$abilities->get_img_url_by_id(5172, false);
$abilities->get_img_url_by_id(5172);

$heroes = new heroes();
$heroes->parse();
$heroes-get_data_by_id(97); // get info about Magnus
$heroes->get_img_url_by_id(97, false); // large image
$heroes->get_img_url_by_id(97); // thumb

$items = new items();
$items->parse();
$items-get_data_by_id(149); // get info about Crystalis
$items->get_img_url_by_id(149, false); // large image
$items->get_img_url_by_id(149); // thumb

$mods = new mods();
$mods->parse();
$mods->get_field_by_id(1, 'name'); // returns 'All Pick'

$lobbies = new lobbies();
$lobbies->parse();
$lobbies->get_field_by_id(2, 'name'); // returns 'Tournament'

$regions = new regions();
$regions->parse();
$regions->get_field_by_id(132, 'name'); // returns 'Europe West'
````

#### Get map with barracks and towers
````php
<?php
require_once('config.php');
$match_mapper_web = new match_mapper_web(123456789);
$match = $match_mapper_web->load();
$map = new map($match->get('tower_status_radiant'), $match->get('tower_status_dire'), $match->get('barracks_status_radiant'), $match->get('barracks_status_dire'));
$canvas = $map->get_image();
header('Content-Type: image/jpg');
imagejpeg($canvas);
imagedestroy($canvas);
````

#### Get info about players from db
````php
<?php
require_once('config.php');
$players_mapper_db = new players_mapper_db();
$players_info = $players_mapper_db->add_id('76561198020176880')->add_id('76561197998200662')->load();
print_r($players_info);
````
or for just getting one player, you can also use
````php
<?php
require_once('config.php');
$player_mapper_db = new player_mapper_db();
$player_mapper_db->set_steamid('76561198020176880');
print_r($player_mapper_db->load());
````

#### Save info about players into db
````php
<?php
require_once('config.php');
//fetch players from API
$players_mapper_web = new players_mapper_web();
$players = $players_mapper_web->add_id('76561198020176880')->add_id('76561197998200662')->load();

//save players into db
$player_mapper_db = new player_mapper_db();
foreach($players as $p) {
	$player_mapper_db->save($p);
}
````

#### Work with UGC Objects
````php
<?php
require_once('config.php');
$match_mapper_web = new match_mapper_web(37633163);
$game = $match_mapper_web->load();
$ugc_mapper_web = new ugc_mapper_web($game->get('radiant_logo'));
$logo_data = $ugc_mapper_web->load();
var_dump($logo_data);
echo $logo_data->url;
````

### Wiki
* [Get all leagues matches and calculate win rate for each hero](https://github.com/kronusme/dota2-api/wiki/Get-all-leagues-matches-and-calculate-win-rate-for-each-hero)
* [Match view page](https://github.com/kronusme/dota2-api/wiki/Match-view-page)

### Thanks

1. Valve for DotA 2 and Web API.

2. [MuppetMaster42](http://dev.dota2.com/member.php?u=5137),  for http://dev.dota2.com/showthread.php?t=58317.

3. Players, who don't hide their own statistic.

4. dev.dota2 community.
