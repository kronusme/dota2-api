<?php

use Dota2Api\Utils\Db;
use Dota2Api\Mappers\PlayerMapperDb;
use Dota2Api\Mappers\PlayersMapperWeb;
use Dota2Api\Models\Player;

class PlayerMapperDbTest extends PHPUnit_Framework_TestCase
{

    public $playerId = 92551671;

    /**
     * @var Player
     */
    public $player;

    public static function setUpBeforeClass()
    {
        $db = Db::obtain();
        $db->exec('DELETE FROM users');
    }

    public static function tearDownAfterClass()
    {
        $db = Db::obtain();
        $db->exec('DELETE FROM users');
    }

    public function setUp()
    {
        $mapperWeb = new PlayersMapperWeb();
        $mapperWeb->addId(player::convertId($this->playerId));
        $d = $mapperWeb->load();
        $this->player = array_pop($d);
    }

    public function testSave()
    {
        $mapperDb = new PlayerMapperDb();
        $mapperDb->save($this->player);
        $db = Db::obtain();
        $r = $db->fetchArrayPDO('SELECT * FROM users');
        $this->assertEquals(1, count($r));
    }

    public function testUpdate()
    {
        $this->player->set('personaname', 'test');
        $mapperDb = new PlayerMapperDb();
        $mapperDb->save($this->player);
        $db = Db::obtain();
        $r = $db->fetchArrayPDO('SELECT * FROM users');
        $player = array_pop($r);
        $this->assertEquals('test', $player['personaname']);
    }

    public function testLoad()
    {
        $mapperDb = new PlayerMapperDb();
        $player = $mapperDb->load(Player::convertId((string)$this->playerId));
        $this->assertEquals('test', $player->get('personaname'));
    }

}