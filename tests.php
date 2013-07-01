<?php

require_once ('config.php');

/*$matches_mapper_web = new matches_mapper_web();
$matches_mapper_web->set_account_id(107567522)->set_tournament_games_only(true)->set_matches_requested(2);
$matches_short_info = $matches_mapper_web->load();
foreach ($matches_short_info AS $key=>$match_short_info) {
    print_r($match_short_info);
    $match_mapper = new match_mapper_web($key);
    $match = $match_mapper->load();
    $mm = new match_mapper_db();
    $mm->save($match);
}
*/
/*$teams_mapper_web = new teams_mapper_web();
$teams = $teams_mapper_web->set_team_id(2)->set_teams_requested(2)->load();
foreach($teams as $team) {
    echo $team->get('name');
    echo $team->get('rating');
    echo $team->get('country_code');
    print_r($team->get_all_leagues_ids());
}*/

/*$mm = new match_mapper_web(123034878); //123002160 122961276
$match = $mm->load();
$saver = new match_mapper_db();
$saver->save($match);*/

/*$loader = new match_mapper_db(116998221);
$loader->load();*/

/*$match_mapper = new match_mapper_web(110272081);
$match = $match_mapper->load();

print_r($match->get_data_array());
print_r($match->get_slot(0)->get_data_array());*/
/*$matches_mapper_web = new matches_mapper_web();
$matches = $matches_mapper_web->set_account_id(ACCOUNT_ID)->set_matches_requested(2)->load();
print_r($matches);*/
/*$mm = new match_mapper_db(111093969);
$match = $mm->load();

print_r($match->get_data_array());
print_r($match->get_slot(0)->get_data_array());*/

/*$mm = new match_mapper_db();
$mm->save($match);*/

/*$mm->set_match_id(110237984);
$match_from_db = $mm->load();*/
/*print_r($match_from_db->get_data_array());
print_r($match_from_db->get_slot(0)->get_data_array());*/

/*$players_mapper_web = new players_mapper_web();
$players_info = $players_mapper_web->add_id('76561198067833250')->add_id('76561198058587506')->load();
foreach($players_info as $player_info) {
    echo $player_info->get('realname');
    echo '<img src="'.$player_info->get('avatarfull').'" alt="'.$player_info->get('personaname').'" />';
    echo '<a href="http://steamcommunity.com/profiles/'.$player_info->get('steamid').'">'.$player_info->get('personaname').'\'s steam profile</a>';
}
print_r($players_info);*/

/*$heroes_mapper = new heroes_mapper();
$heroes = $heroes_mapper->load();
print_r($heroes);*/

/*$leagues_mapper = new leagues_mapper();
$leagues = $leagues_mapper->load();
print_r($leagues);*/

/*$league_mapper = new league_mapper(22);
$games = $league_mapper->load();
print_r($games);*/

