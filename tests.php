<?php

require_once ('config.php');
/*
$matches_mapper_web = new matches_mapper_web();
$matches_mapper_web->set_account_id(107567522);
$matches_short_info = $matches_mapper_web->load();
foreach ($matches_short_info AS $key=>$match_short_info) {
    $match_mapper = new match_mapper_web($key);
    $match = $match_mapper->load();
    $mm = new match_mapper_db();
    $mm->save($match);
}*/
$mm = new match_mapper_web(121853748);
$match = $mm->load();
$saver = new match_mapper_db();
$saver->save($match);

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
$info = $players_mapper_web->add_id('76561198067833250')->add_id('76561198058587506')->get_info();
print_r($info);*/