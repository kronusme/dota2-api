<?php

namespace Dota2Api\Mappers;

use Dota2Api\Utils\Db;
use Dota2Api\Models\League;

class LeaguesMapperDb extends LeaguesMapper
{
    public function load()
    {
        $db = Db::obtain();
        $data = $db->fetchArrayPDO('SELECT * FROM ' . Db::realTablename('leagues'));
        $leagues = array();
        foreach ($data as $row) {
            $league = new League();
            $league->setArray($row);
            $leagues[$row['leagueid']] = $league;
        }
        return $leagues;
    }

    public function delete($ids)
    {
        if (!is_array($ids)) {
            return;
        }
        $ids_str = implode(',', $ids);
        $db = Db::obtain();
        $db->exec('DELETE FROM ' . Db::realTablename('leagues') . ' WHERE leagueid IN (' . $ids_str . ')');
    }

    /**
     * @param League $league
     * @param bool $autoUpdate if true - update league info if league exists in the DB
     */
    public function save(League $league, $autoUpdate = true)
    {
        if (self::leagueExists($league->get('leagueid'))) {
            if ($autoUpdate) {
                $this->update($league);
            }
        } else {
            $this->insert($league);
        }
    }

    /**
     * @param League $league
     */
    public function insert(League $league)
    {
        $db = Db::obtain();
        $db->insertPDO(Db::realTablename('leagues'), $league->getDataArray());
    }

    /**
     * @param League $league
     */
    public function update(league $league)
    {
        $db = Db::obtain();
        $db->updatePDO(
            Db::realTablename('leagues'),
            $league->getDataArray(),
            array('leagueid' => $league->get('leagueid'))
        );
    }

    public static function leagueExists($leagueid)
    {
        $leagueid = (int)$leagueid;
        $db = Db::obtain();
        $r = $db->queryFirstPDO(
            'SELECT leagueid FROM ' . Db::realTablename('leagues') . ' WHERE leagueid = ?',
            array($leagueid)
        );
        return ((bool)$r);
    }
}
