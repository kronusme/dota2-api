<?php

use Dota2Api\Utils\Db;
use Dota2Api\Mappers\MatchesMapperDb;
use Dota2Api\Mappers\MatchMapperDb;
use Dota2Api\Mappers\MatchMapperWeb;
use Dota2Api\Mappers\LeaguesMapperWeb;
use Dota2Api\Mappers\LeaguesMapperDb;

class MatchesMapperDbTest extends PHPUnit_Framework_TestCase
{
    protected $matchId = 1697818230;

    protected $leagueId = 2733;

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
        while (!$match) {
            $match = $matchMapperWeb->load();
        }
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
        $this->assertEquals($match->get('tower_status_radiant'), 1846);
        $this->assertEquals($match->get('tower_status_dire'), 1572);
        $this->assertEquals($match->get('radiant_win'), 1);
        $this->assertEquals($match->get('duration'), 2348);
        $this->assertEquals($match->get('first_blood_time'), 538);
        $this->assertStringStartsWith('2015-08-09', $match->get('start_time'));
        $this->assertEquals($match->get('barracks_status_radiant'), 63);
        $this->assertEquals($match->get('barracks_status_dire'), 15);
        $this->assertEquals($match->get('lobby_type'), 1);
        $this->assertEquals($match->get('human_players'), 10);
        $this->assertEquals($match->get('leagueid'), $this->leagueId);
        $this->assertEquals($match->get('cluster'), 111);
        $this->assertEquals($match->get('radiant_name'), 'Evil Geniuses');
        $this->assertEquals($match->get('radiant_team_id'), 39);
        $this->assertEquals($match->get('dire_name'), 'CDEC Gaming');
        $this->assertEquals($match->get('dire_team_id'), 1520578);

        $slots = $match->getAllSlots();

        $this->assertEquals(count($slots), 10);
        $slot = $slots[0];
        $this->assertEquals($slot->get('match_id'), $this->matchId);
        $this->assertEquals($slot->get('account_id'), 86727555);
        $this->assertEquals($slot->get('hero_id'), 68);
        $this->assertEquals($slot->get('player_slot'), 0);
        $this->assertEquals($slot->get('item_0'), 214);
        $this->assertEquals($slot->get('item_1'), 254);
        $this->assertEquals($slot->get('item_2'), 92);
        $this->assertEquals($slot->get('item_3'), 23);
        $this->assertEquals($slot->get('item_4'), 0);
        $this->assertEquals($slot->get('item_5'), 36);
        $this->assertEquals($slot->get('kills'), 2);
        $this->assertEquals($slot->get('deaths'), 2);
        $this->assertEquals($slot->get('assists'), 13);
        $this->assertEquals($slot->get('leaver_status'), 0);
        $this->assertEquals($slot->get('gold'), 2428);
        $this->assertEquals($slot->get('last_hits'), 49);
        $this->assertEquals($slot->get('denies'), 3);
        $this->assertEquals($slot->get('gold_per_min'), 264);
        $this->assertEquals($slot->get('xp_per_min'), 337);
        $this->assertEquals($slot->get('gold_spent'), 8185);
        $this->assertEquals($slot->get('hero_damage'), 4527);
        $this->assertEquals($slot->get('tower_damage'), 501);
        $this->assertEquals($slot->get('hero_healing'), 569);
        $this->assertEquals($slot->get('level'), 15);

        $getAllPicksBans = $match->getAllPicksBans();

        $this->assertEquals(count($getAllPicksBans), 20);

    }

    public function testDelete()
    {

        $additionalMatchId = 1697737102;
        $matchMapperWeb = new MatchMapperWeb($additionalMatchId);
        $match = $matchMapperWeb->load();
        while(!$match) {
            $match = $matchMapperWeb->load();
        }
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
