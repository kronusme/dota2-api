<?php

class players_mapper_dbTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var players_mapper_db
     */
    public $mapper = null;

    public static function setUpBeforeClass() {
        $leagues_mapper_web = new leagues_mapper_web();
        $leagues = $leagues_mapper_web->load();
        $leagues_mapper_db = new leagues_mapper_db();
        $leagues_mapper_db->save($leagues[600]);

        $match_mapper_web = new match_mapper_web(683300315);
        $match = $match_mapper_web->load();
        $match_mapper_db = new match_mapper_db();
        $match_mapper_db->save($match);
    }

    public static function tearDownBeforeClass() {
        $db = db::obtain();
        $db->exec('DELETE FROM picks_bans');
        $db->exec('DELETE FROM ability_upgrades');
        $db->exec('DELETE FROM additional_units');
        $db->exec('DELETE FROM slots');
        $db->exec('DELETE FROM matches');
        $db->exec('DELETE FROM leagues');
    }

    public function setUp () {
        $this->mapper = new players_mapper_db();
    }

    public function testAdd_id () {
        $this->assertEquals(array(), $this->mapper->get_ids());
        $this->mapper->add_id(1);
        $this->assertEquals(array(1), $this->mapper->get_ids());
    }

    public function testRemove_id () {
        $this->mapper->add_id(1)->add_id(2);
        $this->mapper->remove_id(2);
        $this->assertEquals(array(1), $this->mapper->get_ids());
    }

    public function testRemove_ids () {
        $this->mapper->add_id(1);
        $this->mapper->remove_ids();
        $this->assertEquals(array(), $this->mapper->get_ids());
    }

    public function testGet_ids_string () {
        $this->mapper->add_id(1)->add_id(2);
        $this->assertEquals('1,2', $this->mapper->get_ids_string());
    }

    public function testLoad () {
        $players = $this->mapper->load();
        $this->assertEquals(array(), $players);

        $this->mapper->add_id(player::convert_id(36547811))->add_id(player::convert_id(89137399));
        $players = $this->mapper->load();
        $this->assertTrue($players[player::convert_id(36547811)] instanceof player);
        $this->assertTrue($players[player::convert_id(89137399)] instanceof player);
    }

}