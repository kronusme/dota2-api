<?php

namespace Dota2Api\Mappers;

use Dota2Api\Utils\Request;
use Dota2Api\Models\Match;
use Dota2Api\Models\Slot;

/**
 * Load info about match from web
 *
 * @author kronus
 * @example
 * <code>
 *   $mm = new Dota2Api\Mappers\MatchMapperWeb(121995119);
 *   $match = $mm->load();
 *   echo $match->get('match_id');
 *   echo $match->get('start_time');
 *   echo $match->get('game_mode');
 *   $slots = $match->getAllSlots();
 *   foreach($slots as $slot) {
 *     echo $slot->get('last_hits');
 *   }
 *   print_r($match->getDataArray());
 *   print_r($match->getSlot(0)->getDataArray());
 * </code>
 */
class MatchMapperWeb extends MatchMapper
{
    const STEAM_MATCH_URL = 'https://api.steampowered.com/IDOTA2Match_570/GetMatchDetails/V001/';

    public function __construct($matchId)
    {
        parent::__construct($matchId);
    }

    /**
     * Load match info by match_id
     */
    public function load()
    {
        $request = new request(self::STEAM_MATCH_URL, array('match_id' => $this->getMatchId()));
        $matchInfo = $request->send();
        if (null === $matchInfo) {
            return null;
        }
        $match = new Match();
        $players = array();
        foreach ($matchInfo->players->player as $key => $player) {
            $players[] = $player;
        }
        $data = (array)$matchInfo;
        unset($data['players']);
        $data['start_time'] = date('Y-m-d H:i:s', $data['start_time']);
        $data['radiant_win'] = ($data['radiant_win'] === 'true') ? '1' : '0';
        $match->setArray($data);
        // slots info
        foreach ($players as $player) {
            $data = (array)$player;
            $data['match_id'] = $this->getMatchId();
            $slot = new Slot();
            $slot->setArray($data);
            // additional_units
            if (array_key_exists('additional_units', $data)) {
                $slot->setAdditionalUnitItems((array)($data['additional_units']->unit));
            }
            // abilities
            if (array_key_exists('ability_upgrades', $data)) {
                $d = (array)$data['ability_upgrades'];
                $abilitiesUpgrade = $d['ability'];
                if (!is_array($abilitiesUpgrade)) {
                    $abilitiesUpgrade = array($abilitiesUpgrade);
                }
                foreach ($abilitiesUpgrade as $k => $v) {
                    $abilitiesUpgrade[$k] = (array)$abilitiesUpgrade[$k];
                }
                $slot->setAbilitiesUpgrade($abilitiesUpgrade);
            }
            $match->addSlot($slot);
        }
        if (isset($matchInfo->picks_bans)) {
            $picksBans = (array)$matchInfo->picks_bans;
            foreach ($picksBans['pick_ban'] as $k => $v) {
                $picksBans['pick_ban'][$k] = (array)$v;
                $picksBans['pick_ban'][$k]['is_pick'] = filter_var($picksBans['pick_ban'][$k]['is_pick'], FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
            }
            $match->setAllPickBans($picksBans['pick_ban']);
        }
        return $match;
    }
}
