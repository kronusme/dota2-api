<?php

namespace Dota2Api\Mappers;

use Dota2Api\Utils\Db;
use Dota2Api\Models\Match;
use Dota2Api\Models\Slot;
use Dota2Api\Models\Player;

/**
 * Load info about match from db
 *
 * @author kronus
 * @example
 * <code>
 *   $mm = new Dota2Api\Mappers\MatchMapperDb(111093969);
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
 *   $mm->save($match);
 *   $mm->delete(111093969);
 * </code>
 */
class MatchMapperDb extends MatchMapper
{

    /**
     * @var string
     */
    protected $matchesTable = 'matches';
    /**
     * @var string
     */
    protected $slotsTable = 'slots';

    /**
     * @param int $matchId
     * @return Match
     */
    public function load($matchId = null)
    {
        if (null !== $matchId) {
            $this->setMatchId($matchId);
        }
        $db = Db::obtain();
        $queryForMatch = 'SELECT * FROM ' . Db::realTablename($this->matchesTable) . ' WHERE match_id=?';
        $queryForSlots = 'SELECT * FROM ' . Db::realTablename($this->slotsTable) . ' WHERE match_id=?';
        $matchInfo = $db->queryFirstPDO($queryForMatch, array($this->getMatchId()));
        $match = new Match();
        if (!$matchInfo) {
            return $match;
        }
        $match->setArray($matchInfo);
        $slots = $db->fetchArrayPDO($queryForSlots, array($this->getMatchId()));
        $slotIds = '';
        foreach ($slots as $slot) {
            $slotIds .= $slot['id'] . ',';
        }
        $queryForAbilityUpgrades = 'SELECT * FROM ' . Db::realTablename('ability_upgrades') . ' WHERE slot_id IN (' . rtrim(
            $slotIds,
            ','
        ) . ')';
        $abilityUpgrade = $db->fetchArrayPDO($queryForAbilityUpgrades);
        $abilityUpgradeFormatted = array();
        foreach ($abilityUpgrade as $a) {
            if (!array_key_exists($a['slot_id'], $abilityUpgradeFormatted)) {
                $abilityUpgradeFormatted[$a['slot_id']] = array();
            }
            array_push($abilityUpgradeFormatted[$a['slot_id']], $a);
        }
        $queryForAdditionalUnits = 'SELECT * FROM ' . Db::realTablename('additional_units') . ' WHERE slot_id IN  (' . rtrim(
            $slotIds,
            ','
        ) . ')';
        $additionalUnits = $db->fetchArrayPDO($queryForAdditionalUnits);
        $additionalUnitsFormatted = array();
        foreach ($additionalUnits as $additionalUnit) {
            if (!array_key_exists($additionalUnit['slot_id'], $additionalUnitsFormatted)) {
                $additionalUnitsFormatted[$additionalUnit['slot_id']] = array();
            }
            array_push($additionalUnitsFormatted[$additionalUnit['slot_id']], $additionalUnit);
        }
        foreach ($slots as $s) {
            $slot = new Slot();
            $slot->setArray($s);
            if (array_key_exists($slot->get('id'), $abilityUpgradeFormatted)) {
                $slot->setAbilitiesUpgrade($abilityUpgradeFormatted[$slot->get('id')]);
            }
            if (array_key_exists($slot->get('id'), $additionalUnitsFormatted)) {
                $slot->setAdditionalUnitItems($additionalUnitsFormatted[$slot->get('id')]);
            }
            $match->addSlot($slot);
        }
        if ($match->get('game_mode') == match::CAPTAINS_MODE) {
            $queryForPicksBans = 'SELECT `is_pick`, `hero_id`, `team`, `order` FROM ' . Db::realTablename('picks_bans') . ' WHERE match_id = ? ORDER BY `order`';
            $picks_bans = $db->fetchArrayPDO($queryForPicksBans, array($match->get('match_id')));
            $match->setAllPickBans($picks_bans);
        }
        return $match;
    }

    /**
     * @param match $match
     * @param bool $autoUpdate if true - update match info if match exists in the DB
     */
    public function save(Match $match, $autoUpdate = true)
    {
        if (self::matchExists($match->get('match_id'))) {
            if ($autoUpdate) {
                $this->update($match);
            }
        } else {
            $this->insert($match);
        }
    }

    /**
     * @param $match
     */
    public function insert(Match $match)
    {
        $db = Db::obtain();
        $slots = $match->getAllSlots();
        if ($match->get('radiant_team_id')) {
            $db->insertPDO(Db::realTablename('teams'), array(
                'id' => $match->get('radiant_team_id'),
                'name' => $match->get('radiant_name')
            ));
        }

        if ($match->get('dire_team_id')) {
            $db->insertPDO(Db::realTablename('teams'), array(
                'id' => $match->get('dire_team_id'),
                'name' => $match->get('dire_name')
            ));
        }

        // save common match info
        $db->insertPDO(Db::realTablename($this->matchesTable), $match->getDataArray());
        // save accounts
        foreach ($slots as $slot) {
            if ((int)$slot->get('account_id') !== Player::ANONYMOUS) {
                $db->insertPDO(Db::realTablename('users'), array(
                    'account_id' => $slot->get('account_id'),
                    'steamid' => Player::convertId($slot->get('account_id'))
                ));
            }
        }
        // save slots
        foreach ($slots as $slot) {
            $slotId = $db->insertPDO(Db::realTablename($this->slotsTable), $slot->getDataArray());
            // save abilities upgrade
            $aU = $slot->getAbilitiesUpgrade();
            if (count($aU) > 0) {
                $keys = array();
                $data = array();
                foreach ($aU as $ability) {
                    $keys = array_keys($ability); // yes, it will be reassigned many times
                    $data1 = array_values($ability);
                    array_unshift($data1, $slotId);
                    array_push($data, $data1);
                }
                reset($aU);
                array_unshift($keys, 'slot_id');
                $db->insertManyPDO(Db::realTablename('ability_upgrades'), $keys, $data);
            }
            $additionalUnit = $slot->getAdditionalUnitItems();
            if (count($additionalUnit) > 0) {
                $additionalUnit['slot_id'] = $slotId;
                $db->insertPDO(Db::realTablename('additional_units'), $additionalUnit);
            }
        }
        if ((int)$match->get('game_mode') === match::CAPTAINS_MODE) {
            $picksBans = $match->getAllPicksBans();
            $data = array();
            foreach ($picksBans as $pickBan) {
                $data1 = array();
                array_push($data1, $match->get('match_id'));
                array_push($data1, $pickBan['is_pick']);
                array_push($data1, $pickBan['hero_id']);
                array_push($data1, $pickBan['team']);
                array_push($data1, $pickBan['order']);
                array_push($data, $data1);
            }
            $db->insertManyPDO(
                Db::realTablename('picks_bans'),
                array('match_id', 'is_pick', 'hero_id', 'team', 'order'),
                $data
            );
        }
    }

    /**
     * @param Match $match
     * @param bool $lazy if false - update all data, if true - only possible updated data
     */
    public function update(Match $match, $lazy = true)
    {
        $db = Db::obtain();
        $slots = $match->getAllSlots();
        // update common match info
        $db->updatePDO(
            Db::realTablename($this->matchesTable),
            $match->getDataArray(),
            array('match_id' => $match->get('match_id'))
        );
        foreach ($slots as $slot) {
            // update accounts
            $db->updatePDO(Db::realTablename('users'), array(
                'account_id' => $slot->get('account_id'),
                'steamid' => Player::convertId($slot->get('account_id'))
            ), array('account_id' => $slot->get('account_id')));
            // update slots
            if (!$lazy) {
                $db->updatePDO(
                    Db::realTablename($this->slotsTable),
                    $slot->getDataArray(),
                    array('match_id' => $slot->get('match_id'), 'player_slot' => $slot->get('player_slot'))
                );
            }
        }
    }

    /**
     * @param int $matchId
     */
    public function delete($matchId)
    {
        if (!self::matchExists($matchId)) {
            return;
        }
        $db = Db::obtain();
        $slots = $db->fetchArrayPDO(
            'SELECT id FROM ' . Db::realTablename($this->slotsTable) . ' WHERE match_id = ?',
            array($matchId)
        );
        $slotsFormatted = array();
        foreach ($slots as $slot) {
            array_push($slotsFormatted, $slot['id']);
        }
        if (count($slotsFormatted)) {
            $slots_str = implode(',', $slotsFormatted);
            $db->exec('DELETE FROM ' . Db::realTablename('ability_upgrades') . ' WHERE slot_id IN (' . $slots_str . ')');
            $db->exec('DELETE FROM ' . Db::realTablename('additional_units') . ' WHERE slot_id IN (' . $slots_str . ')');
            $db->exec('DELETE FROM ' . Db::realTablename($this->slotsTable) . ' WHERE id IN (' . $slots_str . ')');
        }
        $db->deletePDO(Db::realTablename('picks_bans'), array('match_id' => $matchId), 0);
        $db->deletePDO(Db::realTablename($this->matchesTable), array('match_id' => $matchId));
    }

    /**
     * Delete match data from db
     *
     * @param int $matchId
     * @return bool
     */
    public static function matchExists($matchId)
    {
        $db = Db::obtain();
        $r = $db->queryFirstPDO(
            'SELECT match_id FROM ' . Db::realTablename('matches') . ' WHERE match_id = ?',
            array($matchId)
        );
        return ((bool)$r);
    }
}
