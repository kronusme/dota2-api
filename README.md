#DotA2-Api

[![Build Status](https://travis-ci.org/kronusme/dota2-api.png?branch=master)](https://travis-ci.org/kronusme/dota2-api)
[![Coverage Status](https://coveralls.io/repos/kronusme/dota2-api/badge.png?branch=master)](https://coveralls.io/r/kronusme/dota2-api?branch=master)
[![License](https://poser.pugx.org/kronusme/dota2-api/license.svg)](https://packagist.org/packages/kronusme/dota2-api)
[![Latest Stable Version](https://poser.pugx.org/kronusme/dota2-api/v/stable.svg)](https://packagist.org/packages/kronusme/dota2-api)
[![Dependencies](https://www.versioneye.com/user/projects/5469ed86a760ce7bc8000027/badge.svg)](https://www.versioneye.com/user/projects/5469ed86a760ce7bc8000027)
[![Code Climate](https://codeclimate.com/github/kronusme/dota2-api/badges/gpa.svg)](https://codeclimate.com/github/kronusme/dota2-api)

### About

1. **What is it?**
This is PHP code for processing DotA 2 API-requests.

2. **What can it do?**
It can get match-list for some criteria, get match-info for single match, get steam-profile info for users.
AND save all this data in MySQL database. For more information see - "How to use it".

3. **What I need to work with it?**
First of all you need web-server with **PHP 5.3+** ( **PDO** and **cURL** should be enabled) and **MySQL 5**. Then look at install section.

### Install

1. Install via [Composer](http://getcomposer.org/):
````json
{
    "require": {
        "kronusme/dota2-api": "2.2.1"
    }
}
````

2. Connect to your mysql-server with any tool (phpmyadmin, heidisql etc) and execute code from the file **db_latest.sql**.

3. Initialize Dota2-Api like this:
````php
require_once 'vendor/autoload.php';

use Dota2Api\Api;

Api::init('YOUR_API_KEY', array('localhost', 'root', 'password', 'db_name', 'table_prefix_'));
````

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
Before parsing and saving leagues matches to your DB, make sure that you've saved leagues to the DB (using `leaguesMapperWeb`!
If you try to save some "public" matches, you should REMOVE `foreign key` for field `leagueid` in the table `matches`!

#### Load some match-info
```php
<?php
$mm = new Dota2Api\Mappers\MatchMapperWeb(121995119);
$match = $mm->load();
```
$match - it's an object with all match data including slots info, ability-upgrades (if provided) and pick, bans (if cm-mode).

#### Save match-info in the database
```php
<?php
$mm = new Dota2Api\Mappers\MatchMapperWeb(121995119);
$match = $mm->load();
$saver = new Dota2Api\Mappers\MatchMapperDb();
$saver->save($match);
```
matchMapperDb will check if match with $match->get('match_id') exists in the db and select method for save (insert or update).


#### Work with match-object
```php
<?php
$mm = new Dota2Api\Mappers\MatchMapperWeb(121995119);
$match = $mm->load();
echo $match->get('match_id');
echo $match->get('start_time');
echo $match->get('game_mode');
$slots = $match->getAllSlots();
foreach($slots as $slot) {
    echo $slot->get('last_hits');
}
print_r($match->getDataArray());
print_r($match->getSlot(0)->getDataArray());
```

#### I want get last 25 matches with some player
````php
<?php
$matchesMapperWeb = new Dota2Api\Mappers\MatchesMapperWeb();
$matchesMapperWeb->setAccountId(93712171);
$matchesShortInfo = $matchesMapperWeb->load();
foreach ($matchesShortInfo as $key=>$matchShortInfo) {
    $matchMapper = new Dota2Api\Mappers\MatchMapperWeb($key);
    $match = $matchMapper->load();
    if ($match) {
      $mm = new Dota2Api\Mappers\MatchMapperDb();
      $mm->save($match);
    }
}
````

#### Get player info
````php
<?php
$playersMapperWeb = new Dota2Api\Mappers\PlayersMapperWeb();
$playersInfo = $playersMapperWeb->addId('76561198067833250')->addId('76561198058587506')->load();
foreach($playersInfo as $playerInfo) {
    echo $playerInfo->get('realname');
    echo '<img src="'.$playerInfo->get('avatarfull').'" alt="'.$playerInfo->get('personaname').'" />';
    echo '<a href="'.$playerInfo->get('profileurl').'">'.$playerInfo->get('personaname').'\'s steam profile</a>';
}
print_r($playersInfo);
````
Player's id you can get via Player::convertId('xxxxx') method (xxxxx - its DotA ID).

#### Get team info
````php
<?php
$teamsMapperWeb = new Dota2Api\Mappers\TeamsMapperWeb();
$teams = $teamsMapperWeb->setTeamId(2)->setTeamsRequested(2)->load();
foreach($teams as $team) {
    echo $team->get('name');
    echo $team->get('rating');
    echo $team->get('country_code');
    print_r($team->getAllLeaguesIds());
}
````

#### Get current heroes list
````php
<?php
$heroesMapper = new Dota2Api\Mappers\HeroesMapper();
$heroes = $heroesMapper->load();
print_r($heroes);
````
$heroes - array with numeric indexes (heroes ids)

#### Get current items list
````php
<?php
$itemsMapperWeb = new Dota2Api\Mappers\ItemsMapperWeb();
$itemsInfo = $itemsMapperWeb->load();
print_r($itemsInfo);
foreach($itemsInfo as $item) {
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
$itemsMapperWeb = new Dota2Api\Mappers\ItemsMapperWeb();
$items = $itemsMapperWeb->load();
$itemsMapperDb = new itemsMapperDb();
$itemsMapperDb->save($items);
````

#### Get current items list from db
````php
<?php
$itemsMapperDb = new Dota2Api\Mappers\ItemsMapperDb();
$itemsInfo = $itemsMapperDb->load();
print_r($itemsInfo);
foreach($itemsInfo as $item) {
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
$leaguesMapperWeb = new Dota2Api\Mappers\LeaguesMapperWeb();
$leagues = $leaguesMapperWeb->load();
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
$leaguePrizePoolMapperWeb = new Dota2Api\Mappers\LeaguePrizePoolMapperWeb();
$leaguePrizePoolMapperWeb->setLeagueId(600);
$prizePoolInfo = $leaguePrizePoolMapperWeb->load();
print_r($prizePoolInfo);
echo $prizePoolInfo['prize_pool'];
echo $prizePoolInfo['league_id'];
echo $prizePoolInfo['status']; // may be undefined
````

````php
$prizePoolMapperDb = new Dota2Api\Mappers\LeaguePrizePoolMapperDb();
$pp = $prizePoolMapperDb->setLeagueId(600)->load();
foreach($pp as $date=>$prize_pool) {
    echo $date.' - $ '.number_format($prize_pool, 2);
}
````

#### Get live leagues matches
````php
<?php
$leagueMapper = new Dota2Api\Mappers\LeagueMapper(22); // set league id (can be get via leagues_mapper)
$games = $leagueMapper->load();
print_r($games);
````
$games - array of live_match objects

#### Get matches from local db
````php
<?php
$matchesMapperDb = new Dota2Api\Mappers\MatchesMapperDb();
$matchesMapperDb->setLeagueId(29)->setMatchesRequested(1);
$matchesInfo = $matchesMapperDb->load();
print_r($matchesInfo);
````

#### Delete match(es) from local db
````php
<?php
$matchesMapperDb = new Dota2Api\Mappers\MatchesMapperDb();
$matchesMapperDb->delete(array(151341579, 151401247));

$mm = new Dota2Api\Mappers\MatchMapperDb();
$mm->delete(151341579);
````

#### Get info about abilities, heroes, items, games mods, lobby types etc
````php
<?php
$abilities = new Dota2Api\Data\Abilities();
$abilities->parse();
$abilities-getDataById(5172); // return array for ability with id 5172 (BeastMaster Inner Beast)
// same, because there are no thumbs for abilities
$abilities->getImgUrlById(5172, false);
$abilities->getImgUrlById(5172);

$heroes = new Dota2Api\Data\Heroes();
$heroes->parse();
$heroes-getDataById(97); // get info about Magnus
$heroes->getImgUrlById(97, false); // large image
$heroes->getImgUrlById(97); // thumb

$items = new Dota2Api\Data\Items();
$items->parse();
$items-getDataById(149); // get info about Crystalis
$items->getImgUrlById(149, false); // large image
$items->getImgUrlById(149); // thumb

$mods = new Dota2Api\Data\Mods();
$mods->parse();
$mods->getFieldById(1, 'name'); // returns 'All Pick'

$lobbies = new Dota2Api\Data\Lobbies();
$lobbies->parse();
$lobbies->getFieldById(2, 'name'); // returns 'Tournament'

$regions = new Dota2Api\Data\Regions();
$regions->parse();
$regions->getFieldById(132, 'name'); // returns 'Europe West'
````

#### Get map with barracks and towers
````php
<?php
$matchMapperWeb = new Dota2Api\Mappers\MatchMapperWeb(123456789);
$match = $matchMapperWeb->load();
$map = new Dota2Api\Utils\Map($match->get('tower_status_radiant'), $match->get('tower_status_dire'), $match->get('barracks_status_radiant'), $match->get('barracks_status_dire'));
$canvas = $map->getImage();
header('Content-Type: image/jpg');
imagejpeg($canvas);
imagedestroy($canvas);
````

#### Get info about players from db
````php
<?php
$playersMapperDb = new Dota2Api\Mappers\PlayersMapperDb();
$players_info = $playersMapperDb->addId('76561198020176880')->addId('76561197998200662')->load();
print_r($players_info);
````
or for just getting one player, you can also use
````php
<?php
$playerMapperDb = new Dota2Api\Mappers\PlayerMapperDb();
$playerMapperDb->setSteamid('76561198020176880');
print_r($playerMapperDb->load());
````

#### Save info about players into db
````php
<?php
//fetch players from API
$playersMapperWeb = new Dota2Api\Mappers\PlayersMapperWeb();
$players = $playersMapperWeb->addId('76561198020176880')->addId('76561197998200662')->load();

//save players into db
$playerMapperDb = new Dota2Api\Mappers\PlayerMapperDb();
foreach($players as $p) {
	$playerMapperDb->save($p);
}
````

#### Work with UGC Objects
````php
<?php
$matchMapperWeb = new Dota2Api\Mappers\MatchMapperWeb(37633163);
$game = $matchMapperWeb->load();
$ugcMapperWeb = new Dota2Api\Mappers\UgcMapperWeb($game->get('radiant_logo'));
$logoData = $ugcMapperWeb->load();
var_dump($logoData);
echo $logoData->url;
````

### Thanks

1. Valve for DotA 2 and Web API.

2. [MuppetMaster42](http://dev.dota2.com/member.php?u=5137),  for http://dev.dota2.com/showthread.php?t=58317.

3. Players, who don't hide their own statistic.

4. dev.dota2 community.
