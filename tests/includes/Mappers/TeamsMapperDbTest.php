<?php

use Dota2Api\Mappers\TeamsMapperWeb;
use Dota2Api\Mappers\TeamsMapperDb;

class TeamsMapperDbTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var TeamsMapperWeb
     */
    protected $mapper;

    protected function setUp()
    {
        $this->mapper = new TeamsMapperDb();
        $mapperWeb = new TeamsMapperWeb();
        $mapperWeb->setTeamId(36)->setTeamsRequested(1);
        $teams = $mapperWeb->load();

        $this->assertEquals(count($teams), 1);
        $team = array_pop($teams);
        $team->set('team_id', 36);
        $this->mapper->save($team);
    }

    public function testLoad()
    {
        $teams = $this->mapper->load(36);
        $this->assertEquals(count($teams), 1);
        $team = $teams[36];
        $this->assertEquals($team->get('name'), 'Natus Vincere');
    }

}
