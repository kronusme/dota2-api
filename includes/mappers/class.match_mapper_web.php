<?php
/**
 *
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
        $response = $request->send();
        $match_info = new SimpleXMLElement($response);
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
            if (isset($data['ability_upgrades'])) {
                $d = (array)$data['ability_upgrades'];
                $abilities_upgrade = $d['ability'];
                $slot->set_abilities_upgrade($abilities_upgrade);
            }
            $match->add_slot($slot);
        }
        return $match;
    }
}