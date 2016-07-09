<?php

namespace Dota2Api\Mappers;

use Dota2Api\Models\Team;
use Dota2Api\Utils\Db;

/**
 * Load info about team from web
 *
 * @author kronus
 * @example
 * <code>
 *   $teamsMapperDb = new Dota2Api\Mappers\TeamsMapperDb();
 *   $teams = $teamsMapperDb->load();
 *   foreach($teams as $team) {
 *     echo $team->get('name');
 *   }
 * </code>
 */
class TeamsMapperDb extends TeamsMapper
{
    /**
     *
     */
    const TEAMS_STEAM_URL = 'https://api.steampowered.com/IDOTA2Match_570/GetTeamInfoByTeamID/v001/';

    /**
     * @return null|Team[]
     */
    public function load($tid)
    {
        $addSql = '';
        if (!is_null($tid)) {
            if (is_array($tid)) {
                $addSql = ' WHERE id IN (' . implode(',', $tid) . ')';
            } else {
                $addSql = ' WHERE id = ' . intval($tid);
            }
        }
        $db = Db::obtain();
        $result = $db->fetchArrayPDO('SELECT * FROM ' . Db::realTablename('teams') . ' ' . $addSql);
        if ($result === false) {
            return null;
        }
        $teams = array();
        foreach ($result as $row) {
            $tid = $row['id'];
            $team = new Team();
            $team->setArray($row);
            $team->set('team_id', $tid);
            $teams[$tid] = $team;
        }
        return $teams;
    }

    /**
     * @param Team $team
     * @return bool|integer
     */
    public function save($team)
    {
        $db = Db::obtain();
        return $db->insertPDO(Db::realTablename('teams'), array('id' => $team->get('team_id'), 'name' => $team->get('name')));
    }

    /**
     * @param integer[] $ids
     */
    public function delete($ids)
    {
        $db = Db::obtain();
        $ids = is_array($ids) ? $ids : array($ids);
        if (!count($ids)) {
            return;
        }
        $db->exec('DELETE FROM ' . Db::realTablename('teams') . ' WHERE id IN (' . implode(',', $ids) . ')');
    }
}
