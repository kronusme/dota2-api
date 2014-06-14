<?php

/**
 * Load league's prize pool in current moment
 *
 * @example
 * <code>
 *  $prize_pool_mapper_db = new league_prize_pool_mapper_db();
 *  $pp = $prize_pool_mapper_db->set_league_id(600)->load();
 *  foreach($pp as $date=>$prize_pool) {
 *      echo $date.' - $ '.number_format($prize_pool, 2).'<br />';
 *  }
 * </code>
 */

class league_prize_pool_mapper_db extends league_prize_pool_mapper {

    public function load() {
        $leagueid = $this->get_league_id();
        if (is_null($leagueid)) {
            return array();
        }
        $db = db::obtain();
        $checks = array();
        $result = $db->fetch_array_pdo('SELECT * FROM '.db::real_tablename('league_prize_pools').' WHERE league_id = '.$leagueid, array());
        foreach($result as $r) {
            $checks[$r['date']] = $r['prize_pool'];
        }

        return $checks;
    }

    public function save() {
        $leagueid = $this->get_league_id();
        $prize_pool = $this->get_prize_pool();
        if (is_null($prize_pool) || is_null($leagueid)) {
            return;
        }
        $db = db::obtain();
        $data = array('league_id' => $leagueid, 'prize_pool' => $prize_pool);
        $db->insert_pdo(db::real_tablename('league_prize_pools'), $data);
        echo $db->get_error();
    }

}