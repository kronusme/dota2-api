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
$mm = new match_mapper_web(116998221);
$match = $mm->load();
$saver = new match_mapper_db();
$saver->save($match);

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

/*$players_mapper = new players_mapper();
$info = $players_mapper->add_id('76561198067833250')->add_id('76561198058587506')->get_info();
print_r($info);*/