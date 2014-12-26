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

    public static function setUpBeforeClass()
    {
        $leagues_mapper_web = new LeaguesMapperWeb();
        $leagues = $leagues_mapper_web->load();
        $leagues_mapper_db = new LeaguesMapperDb();
        $leagues_mapper_db->save($leagues[600]);

        $match_mapper_web = new MatchMapperWeb(683300315);
        $match = $match_mapper_web->load();
        $match_mapper_db = new MatchMapperDb();
        $match_mapper_db->save($match);
    }

    public static function tearDownBeforeClass()
    {
        $db = Db::obtain();
        $db->exec('DELETE FROM picks_bans');
        $db->exec('DELETE FROM ability_upgrades');
        $db->exec('DELETE FROM additional_units');
        $db->exec('DELETE FROM slots');
        $db->exec('DELETE FROM matches');
        $db->exec('DELETE FROM leagues');
    }

    public function setUp()
    {
        $this->mapper = new PlayersMapperDb();
    }

    public function testAdd_id()
    {
        $this->assertEquals(array(), $this->mapper->getIds());
        $this->mapper->addId(1);
        $this->assertEquals(array(1), $this->mapper->getIds());
    }

    public function testRemove_id()
    {
        $this->mapper->addId(1)->addId(2);
        $this->mapper->removeId(2);
        $this->assertEquals(array(1), $this->mapper->getIds());
    }

    public function testRemove_ids()
    {
        $this->mapper->addId(1);
        $this->mapper->removeIds();
        $this->assertEquals(array(), $this->mapper->getIds());
    }

    public function testGet_ids_string()
    {
        $this->mapper->addId(1)->addId(2);
        $this->assertEquals('1,2', $this->mapper->getIdsString());
    }

    public function testLoad()
    {
        $players = $this->mapper->load();
        $this->assertEquals(array(), $players);

        $this->mapper->addId(Player::convertId(36547811))->addId(Player::convertId(89137399));
        $players = $this->mapper->load();
        $this->assertTrue($players[Player::convertId(36547811)] instanceof Player);
        $this->assertTrue($players[Player::convertId(89137399)] instanceof Player);
    }

}