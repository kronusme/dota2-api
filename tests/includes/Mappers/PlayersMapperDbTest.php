<?php

use Dota2Api\Mappers\PlayersMapperDb;
use Dota2Api\Mappers\LeaguesMapperWeb;
use Dota2Api\Mappers\LeaguesMapperDb;
use Dota2Api\Mappers\MatchMapperWeb;
use Dota2Api\Mappers\MatchMapperDb;
use Dota2Api\Models\Player;
use Dota2Api\Utils\Db;

class PlayersMapperDbTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PlayersMapperDb
     */
    public $mapper;

    public static function setUpBeforeClass()
    {
        $leaguesMapperWeb = new LeaguesMapperWeb();
        $leagues = $leaguesMapperWeb->load();
        $leaguesMapperDb = new LeaguesMapperDb();
        $leaguesMapperDb->save($leagues[2733]);

        $matchMapperWeb = new MatchMapperWeb(1697818230);
        $match = $matchMapperWeb->load();
        while (!$match) {
            $match = $matchMapperWeb->load();
        }
        $matchMapperDb = new MatchMapperDb();
        $matchMapperDb->save($match);
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

    public function testAddId()
    {
        $this->assertEquals(array(), $this->mapper->getIds());
        $this->mapper->addId(1);
        $this->assertEquals(array(1), $this->mapper->getIds());
    }

    public function testRemoveId()
    {
        $this->mapper->addId(1)->addId(2);
        $this->mapper->removeId(2);
        $this->assertEquals(array(1), $this->mapper->getIds());
    }

    public function testRemoveIds()
    {
        $this->mapper->addId(1);
        $this->mapper->removeIds();
        $this->assertEquals(array(), $this->mapper->getIds());
    }

    public function testGetIdsString()
    {
        $this->mapper->addId(1)->addId(2);
        $this->assertEquals('1,2', $this->mapper->getIdsString());
    }

    public function testLoad()
    {
        $players = $this->mapper->load();
        $this->assertEquals(array(), $players);

        $this->mapper->addId(Player::convertId(86727555))->addId(Player::convertId(111620041));
        $players = $this->mapper->load();
        $this->assertTrue($players[Player::convertId(86727555)] instanceof Player);
        $this->assertTrue($players[Player::convertId(111620041)] instanceof Player);
    }

}