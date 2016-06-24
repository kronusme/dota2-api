<?php

use Dota2Api\Mappers\TeamsMapperWeb;

class TeamsMapperWebTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var TeamsMapperWeb
     */
    protected $mapper;

    protected function setUp()
    {
        $this->markTestSkipped('Teams API end-point doesn\'t return team_id for teams');
        $this->mapper = new TeamsMapperWeb();
    }

    public function testLoad()
    {

        $this->mapper->setTeamId(36)->setTeamsRequested(1);
        $teams = $this->mapper->load();

        $this->assertEquals(count($teams), 1);
        $team = array_pop($teams);
        $this->assertEquals((int)$team->get('team_id'), 36);
        $this->assertEquals($team->get('name'), 'Natus Vincere');
        $this->assertEquals($team->get('tag'), 'Na`Vi');
        $this->assertEquals($team->get('country_code'), 'ua');
        $this->assertGreaterThan(0, count($team->getAllPlayersIds()));
        $this->assertGreaterThan(0, count($team->getAllLeaguesIds()));
    }

    public function testLoadMultiple()
    {

        $this->mapper->setTeamId(36)->setTeamsRequested(2);
        $teams = $this->mapper->load();
        $this->assertEquals(count($teams), 2);

        $team = array_pop($teams);
        $this->assertEquals((int)$team->get('team_id'), 39);
        $this->assertEquals($team->get('name'), 'Evil Geniuses');
        $this->assertEquals($team->get('tag'), 'EG');
        $this->assertEquals($team->get('country_code'), 'us');
        $this->assertGreaterThan(0, count($team->getAllPlayersIds()));
        $this->assertGreaterThan(0, count($team->getAllLeaguesIds()));

        $team = array_pop($teams);
        $this->assertEquals((int)$team->get('team_id'), 36);
        $this->assertEquals($team->get('name'), 'Natus Vincere');
        $this->assertEquals($team->get('tag'), 'Na`Vi');
        $this->assertEquals($team->get('country_code'), 'ua');
        $this->assertGreaterThan(0, count($team->getAllPlayersIds()));
        $this->assertGreaterThan(0, count($team->getAllLeaguesIds()));
    }
}
