<?php

use Dota2Api\Utils\Db;
use Dota2Api\Mappers\MatchesMapperDb;
use Dota2Api\Mappers\MatchMapperDb;
use Dota2Api\Mappers\MatchMapperWeb;
use Dota2Api\Mappers\LeaguesMapperWeb;
use Dota2Api\Mappers\LeaguesMapperDb;

class MatchesMapperDbTest extends PHPUnit_Framework_TestCase
{
    protected $matchId = 985780481;

    protected $leagueId = 1803;

    public static function setUpBeforeClass()
    {
        $db = Db::obtain();
        $db->exec('DELETE FROM picks_bans');
        $db->exec('DELETE FROM ability_upgrades');
        $db->exec('DELETE FROM additional_units');
        $db->exec('DELETE FROM slots');
        $db->exec('DELETE FROM matches');
    }

    public function setUp()
    {

        $leaguesMapperWeb = new LeaguesMapperWeb();
        $leagues = $leaguesMapperWeb->load();
        $leaguesMapperDb = new LeaguesMapperDb();
        $leaguesMapperDb->save($leagues[$this->leagueId]);

        $matchMapperWeb = new MatchMapperWeb($this->matchId);
        $match = $matchMapperWeb->load();
        $matchMapperDb = new MatchMapperDb();
        $matchMapperDb->save($match);
    }

    public function testLoad()
    {

        $matchesMapperDb = new MatchesMapperDb();
        $matchesMapperDb->setLeagueId($this->leagueId)->setMatchesRequested(1);
        $matches = $matchesMapperDb->load();
        /* @var $match \Dota2Api\Models\Match */
        $match = array_pop($matches);

        $this->assertEquals($match->get('match_id'), $this->matchId);
        $this->assertEquals($match->get('game_mode'), 2);
        $this->assertEquals($match->get('tower_status_radiant'), 1796);
        $this->assertEquals($match->get('tower_status_dire'), 256);
        $this->assertEquals($match->get('radiant_win'), 1);
        $this->assertEquals($match->get('duration'), 3526);
        $this->assertEquals($match->get('first_blood_time'), 47);
        $this->assertStringStartsWith('2014-10-26', $match->get('start_time'));
        $this->assertEquals($match->get('barracks_status_radiant'), 55);
        $this->assertEquals($match->get('barracks_status_dire'), 50);
        $this->assertEquals($match->get('lobby_type'), 1);
        $this->assertEquals($match->get('human_players'), 10);
        $this->assertEquals($match->get('leagueid'), 1803);
        $this->assertEquals($match->get('cluster'), 136);
        $this->assertEquals($match->get('radiant_name'), 'Evil Geniuses');
        $this->assertEquals($match->get('radiant_team_id'), 39);
        $this->assertEquals($match->get('dire_name'), 'Team Secret');
        $this->assertEquals($match->get('dire_team_id'), 1838315);

        $slots = $match->getAllSlots();

        $this->assertEquals(count($slots), 10);
        $slot = $slots[0];
        $this->assertEquals($slot->get('match_id'), $this->matchId);
        $this->assertEquals($slot->get('account_id'), 86727555);
        $this->assertEquals($slot->get('hero_id'), 30);
        $this->assertEquals($slot->get('player_slot'), 0);
        $this->assertEquals($slot->get('item_0'), 180);
        $this->assertEquals($slot->get('item_1'), 37);
        $this->assertEquals($slot->get('item_2'), 108);
        $this->assertEquals($slot->get('item_3'), 42);
        $this->assertEquals($slot->get('item_4'), 81);
        $this->assertEquals($slot->get('item_5'), 36);
        $this->assertEquals($slot->get('kills'), 9);
        $this->assertEquals($slot->get('deaths'), 8);
        $this->assertEquals($slot->get('assists'), 14);
        $this->assertEquals($slot->get('leaver_status'), 0);
        $this->assertEquals($slot->get('gold'), 3883);
        $this->assertEquals($slot->get('last_hits'), 97);
        $this->assertEquals($slot->get('denies'), 0);
        $this->assertEquals($slot->get('gold_per_min'), 310);
        $this->assertEquals($slot->get('xp_per_min'), 393);
        $this->assertEquals($slot->get('gold_spent'), 14470);
        $this->assertEquals($slot->get('hero_damage'), 6978);
        $this->assertEquals($slot->get('tower_damage'), 1090);
        $this->assertEquals($slot->get('hero_healing'), 4280);
        $this->assertEquals($slot->get('level'), 21);

        $getAllPicksBans = $match->getAllPicksBans();

        $this->assertEquals(count($getAllPicksBans), 20);

    }

    public function testDelete()
    {

        $additionalMatchId = 886357301;
        $matchMapperWeb = new MatchMapperWeb($additionalMatchId);
        $match = $matchMapperWeb->load();
        $matchMapperDb = new MatchMapperDb();
        $matchMapperDb->save($match);

        $matchesMapperDb = new MatchesMapperDb();
        $matchesMapperDb->delete(array($additionalMatchId, $this->matchId));

        $db = Db::obtain();
        $this->assertEquals(0, count($db->fetchArrayPDO('SELECT * FROM matches')));
        $this->assertEquals(0, count($db->fetchArrayPDO('SELECT * FROM slots')));
        $this->assertEquals(0, count($db->fetchArrayPDO('SELECT * FROM additional_units')));
        $this->assertEquals(0, count($db->fetchArrayPDO('SELECT * FROM ability_upgrades')));
        $this->assertEquals(0, count($db->fetchArrayPDO('SELECT * FROM picks_bans')));

    }

}
