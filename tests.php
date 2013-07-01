<?php

require_once ('config.php');
//fetch players from API
$players_mapper_web = new players_mapper_web();
$players = $players_mapper_web->add_id('76561198020176880')->add_id('76561197998200662')->load();

//save players into db
$player_mapper_db = new player_mapper_db();
foreach($players as $p) {
	$player_mapper_db->save($p);
}

//fetch players from db
$players_mapper_db = new players_mapper_db();
$players_info = $players_mapper_db->add_id('76561198020176880')->add_id('76561197998200662')->load();
print_r($players_info);
?>