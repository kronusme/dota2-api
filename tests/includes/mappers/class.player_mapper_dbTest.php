<?php

class player_mapper_dbTest extends PHPUnit_Framework_TestCase {

    public $player_id = 92551671;

    /**
     * @var player
     */
    public $player = null;

    public static function setUpBeforeClass () {
        $db = db::obtain();
        $db->exec('DELETE FROM users');
    }

    public static function tearDownAfterClass () {
        $db = db::obtain();
        $db->exec('DELETE FROM users');
    }

    public function setUp() {
        $mapper_web = new players_mapper_web();
        $mapper_web->add_id(player::convert_id($this->player_id));
        $d = $mapper_web->load();
        $this->player = array_pop($d);
    }

    public function testSave() {
        $mapper_db = new player_mapper_db();
        $mapper_db->save($this->player);
        $db = db::obtain();
        $r = $db->fetch_array_pdo('SELECT * FROM users');
        $this->assertEquals(1, count($r));
    }

    public function testUpdate() {
        $this->player->set('personaname', 'test');
        $mapper_db = new player_mapper_db();
        $mapper_db->save($this->player);
        $db = db::obtain();
        $r = $db->fetch_array_pdo('SELECT * FROM users');
        $player = array_pop($r);
        $this->assertEquals('test', $player['personaname']);
    }

    public function testLoad() {
        $mapper_db = new player_mapper_db();
        $player = $mapper_db->load(player::convert_id($this->player_id));
        $this->assertEquals('test', $player->get('personaname'));
    }

}