<?php
/**
 * Load info about match from web
 *
 * @author kronus
 * @package mappers
 * @example
 * <code>
 *   $mm = new match_mapper_web(121995119);
 *   $match = $mm->load();
 *   echo $match->get('match_id');
 *   echo $match->get('start_time');
 *   echo $match->get('game_mode');
 *   $slots = $match->get_all_slots();
 *   foreach($slots as $slot) {
 *     echo $slot->get('last_hits');
 *   }
 *   print_r($match->get_data_array());
 *   print_r($match->get_slot(0)->get_data_array());
 * </code>
 */
class match_mapper_web extends match_mapper {
    const steam_match_url = 'https://api.steampowered.com/IDOTA2Match_570/GetMatchDetails/V001/';

    public function __construct($match_id) {
        parent::__construct($match_id);
    }
    /**
     * Load match info by match_id
     */
    public function load() {
        $request = new request(self::steam_match_url, array('match_id' => $this->get_match_id()));
        $match_info = $request->send();
        if (is_null($match_info)) {
            return null;
        }
        $match = new match();
        $players = array();
        foreach($match_info->players->player as $key=>$player) {
            $players[] = $player;
        }
        $data = (array)$match_info;
        unset($data['players']);
        $data['start_time'] = date('Y-m-d H:i:s', $data['start_time']);
        $data['radiant_win'] = ($data['radiant_win'] == 'true')? '1' : '0';
        $match->set_array($data);
        // slots info
        foreach ($players as $player) {
            $data = (array)$player;
            $data['match_id'] = $this->get_match_id();
            $slot = new slot();
            $slot->set_array($data);
            // additional_units
            if (isset($data['additional_units'])) {
                $slot->set_additional_unit_items((array)($data['additional_units']->unit));
            }
            // abilities
            if (isset($data['ability_upgrades'])) {
                $d = (array)$data['ability_upgrades'];
                $abilities_upgrade = $d['ability'];
                foreach($abilities_upgrade as $k=>$v) {
					$abilities_upgrade[$k] = (array)$abilities_upgrade[$k];
                }
                $slot->set_abilities_upgrade($abilities_upgrade);
            }
            $match->add_slot($slot);
        }
        if (isset($match_info->picks_bans)) {
            $picks_bans = (array)$match_info->picks_bans;
            foreach($picks_bans['pick_ban'] as $k=>$v) {
                $picks_bans['pick_ban'][$k] = (array)$v;
                if($picks_bans['pick_ban'][$k]['is_pick'] == 'false') {
                    $picks_bans['pick_ban'][$k]['is_pick'] = '0';
                }
                else {
                    $picks_bans['pick_ban'][$k]['is_pick'] = '1';
                }
            }
            $match->set_all_pick_bans($picks_bans['pick_ban']);
        }
        return $match;
    }
}