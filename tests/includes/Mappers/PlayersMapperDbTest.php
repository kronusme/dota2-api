<?php

use Dota2Api\Mappers\PlayersMapperDb;
use Dota2Api\Mappers\LeaguesMapperWeb;
use Dota2Api\Mappers\LeaguesMapperDb;
use Dota2Api\Mappers\MatchMapperWeb;
use Dota2Api\Mappers\MatchMapperDb;
use Dota2Api\Models\Player;

class PlayersMapperDbTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PlayersMapperDb
     */
    public $mapper = null;

    public static function setUpBeforeClass() {
        $leagues_mapper_web = new LeaguesMapperWeb();
        $leagues = $leagues_mapper_web->load();
        $leagues_mapper_db = new LeaguesMapperDb();
        $leagues_mapper_db->save($leagues[600]);

        $match_mapper_web = new MatchMapperWeb(683300315);
        $match = $match_mapper_web->load();
        $match_mapper_db = new MatchMapperDb();
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
        $this->mapper = new PlayersMapperDb();
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

        $this->mapper->add_id(Player::convert_id(36547811))->add_id(Player::convert_id(89137399));
        $players = $this->mapper->load();
        $this->assertTrue($players[Player::convert_id(36547811)] instanceof Player);
        $this->assertTrue($players[Player::convert_id(89137399)] instanceof Player);
    }

}