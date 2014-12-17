<?php

use Dota2Api\Utils\Db;
use Dota2Api\Mappers\PlayerMapperDb;
use Dota2Api\Mappers\PlayersMapperWeb;
use Dota2Api\Models\Player;

class PlayerMapperDbTest extends PHPUnit_Framework_TestCase {

    public $player_id = 92551671;

    /**
     * @var Player
     */
    public $player = null;

    public static function setUpBeforeClass () {
        $db = Db::obtain();
        $db->exec('DELETE FROM users');
    }

    public static function tearDownAfterClass () {
        $db = Db::obtain();
        $db->exec('DELETE FROM users');
    }

    public function setUp() {
        $mapper_web = new PlayersMapperWeb();
        $mapper_web->add_id(player::convert_id($this->player_id));
        $d = $mapper_web->load();
        $this->player = array_pop($d);
    }

    public function testSave() {
        $mapper_db = new PlayerMapperDb();
        $mapper_db->save($this->player);
        $db = Db::obtain();
        $r = $db->fetch_array_pdo('SELECT * FROM users');
        $this->assertEquals(1, count($r));
    }

    public function testUpdate() {
        $this->player->set('personaname', 'test');
        $mapper_db = new PlayerMapperDb();
        $mapper_db->save($this->player);
        $db = Db::obtain();
        $r = $db->fetch_array_pdo('SELECT * FROM users');
        $player = array_pop($r);
        $this->assertEquals('test', $player['personaname']);
    }

    public function testLoad() {
        $mapper_db = new PlayerMapperDb();
        $player = $mapper_db->load(Player::convert_id($this->player_id));
        $this->assertEquals('test', $player->get('personaname'));
    }

}