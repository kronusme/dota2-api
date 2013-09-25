<?php
class leagues_mapper_db extends leagues_mapper {
    public function load() {
        $db = db::obtain();
        $data = $db->fetch_array_pdo('SELECT * FROM '.db::real_tablename('leagues'));
        $leagues = array();
        foreach($data as $row) {
            $league = new league();
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
        $db = db::obtain();
        $db->exec('DELETE FROM '.db::real_tablename('leagues').' WHERE leagueid IN ('.$ids_str.')');
    }

    /**
     * @param league $league
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
     * @param league $league
     */
    public function insert(league $league) {
        $db = db::obtain();
        $db->insert_pdo(db::real_tablename('leagues'), $league->get_data_array());
    }
    /**
     * @param league $league
     */
    public function update(league $league) {
        $db = db::obtain();
        $db->update_pdo(db::real_tablename('leagues'), $league->get_data_array(), array('leagueid' => $league->get('leagueid')));
    }

    public static function league_exists($leagueid) {
        $leagueid = intval($leagueid);
        $db = db::obtain();
        $r = $db->query_first_pdo('SELECT leagueid FROM '.db::real_tablename('leagues').' WHERE leagueid = ?', array($leagueid));
        return ((bool)$r);
    }
}