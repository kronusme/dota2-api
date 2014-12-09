<?php

namespace Dota2Api\Mappers;

use Dota2Api\Utils\Db;
use Dota2Api\Models\League;


class LeaguesMapperDb extends LeaguesMapper {
    public function load() {
        $db = Db::obtain();
        $data = $db->fetch_array_pdo('SELECT * FROM '.Db::real_tablename('leagues'));
        $leagues = array();
        foreach($data as $row) {
            $league = new League();
            $league->set_array($row);
            $leagues[$row['leagueid']] = $league;
        }
        return $leagues;
    }

    public function delete($ids) {
        if (!is_array($ids)) {
            return;
        }
        $ids_str = implode(',', $ids);
        $db = Db::obtain();
        $db->exec('DELETE FROM '.Db::real_tablename('leagues').' WHERE leagueid IN ('.$ids_str.')');
    }

    /**
     * @param League $league
     * @param bool $auto_update if true - update league info if league exists in the DB
     */
    public function save(league $league, $auto_update = true) {
        if (self::league_exists($league->get('leagueid'))) {
            if ($auto_update) {
                $this->update($league);
            }
        }
        else {
            $this->insert($league);
        }
    }
    /**
     * @param League $league
     */
    public function insert(league $league) {
        $db = Db::obtain();
        $db->insert_pdo(Db::real_tablename('leagues'), $league->get_data_array());
    }
    /**
     * @param League $league
     */
    public function update(league $league) {
        $db = Db::obtain();
        $db->update_pdo(Db::real_tablename('leagues'), $league->get_data_array(), array('leagueid' => $league->get('leagueid')));
    }

    public static function league_exists($leagueid) {
        $leagueid = intval($leagueid);
        $db = Db::obtain();
        $r = $db->query_first_pdo('SELECT leagueid FROM '.Db::real_tablename('leagues').' WHERE leagueid = ?', array($leagueid));
        return ((bool)$r);
    }
}