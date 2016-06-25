<?php

namespace Dota2Api\Mappers;

use Dota2Api\Utils\Db;
use Dota2Api\Models\Match;
use Dota2Api\Models\Slot;

/**
 * Load info about matches from local db (by some criteria)
 *
 * @author kronus
 * @example
 * <code>
 *   $matchesMapperDb = new Dota2Api\Mappers\MatchesMapperDb();
 *   $matchesMapperDb->setLeagueId(29)->setMatchesRequested(1);
 *   $matchesInfo = $matchesMapperDb->load();
 *   $matchesMapperDb->delete(array(12345, 54321));
 *   print_r($matchesInfo);
 * </code>
 */
class MatchesMapperDb extends MatchesMapper
{

    private $_team_id;

    /**
     * @param int $teamId
     * @return MatchesMapperDb
     */
    public function setTeamId($teamId)
    {
        $this->_team_id = (int)$teamId;
        return $this;
    }

    /**
     * @return int | null
     */
    public function getTeamId()
    {
        return $this->_team_id;
    }

    /**
     *
     */
    public function __construct()
    {
    }

    /**
     * Load matches from db
     *
     * Possible filters: league_id, hero_id, account_id, start_at_match_id
     * Also matches_requested used as LIMIT
     * @return Match[]
     */
    public function load()
    {
        $matchesInfo = $this->_getMatchesIdsFromMatches();
        $matchesIds = array();
        foreach ($matchesInfo as $matchInfo) {
            array_push($matchesIds, $matchInfo['match_id']);
        }
        if (count($matchesIds) === 0) {
            return array();
        }
        $slotsInfo = $this->_loadSlotsInfo($matchesIds);
        $picksBansFormattedInfo = $this->_loadPicksBansInfo($matchesIds);

        $slotsIds = array();
        foreach ($slotsInfo as $slotInfo) {
            array_push($slotsIds, $slotInfo['id']);
        }

        $abilitiesUpgradeFormattedInfo = $this->_loadAbilityUpgradesInfo($slotsIds);
        $additionalUnitsFormattedInfo = $this->_loadAdditionalUnitsInfo($slotsIds);

        // we load all matches info and now need to make proper match objects
        $matches = array();
        foreach ($matchesInfo as $matchInfo) {
            $match = new Match();
            $match->setArray($matchInfo);
            $slots_count = 0;
            foreach ($slotsInfo as $slotInfo) {
                if ($slots_count > 9) {
                    // match can't has more than 10 slots
                    break;
                }
                if ($slotInfo['match_id'] == $match->get('match_id')) {
                    $slot = new slot();
                    $slot->setArray($slotInfo);
                    if (array_key_exists($slot->get('id'), $abilitiesUpgradeFormattedInfo)) {
                        $slot->setAbilitiesUpgrade($abilitiesUpgradeFormattedInfo[$slot->get('id')]);
                    }
                    if (array_key_exists($slot->get('id'), $additionalUnitsFormattedInfo)) {
                        $slot->setAdditionalUnitItems($additionalUnitsFormattedInfo[$slot->get('id')]);
                    }
                    $match->addSlot($slot);
                    $slots_count++;
                }
            }
            if (array_key_exists($match->get('match_id'), $picksBansFormattedInfo)) {
                $match->setAllPickBans($picksBansFormattedInfo[$match->get('match_id')]);
            }
            $matches[$match->get('match_id')] = $match;
        }
        return $matches;
    }

    /**
     * Delete matches info from db
     * @param array $ids
     */
    public function delete(array $ids)
    {
        if (!count($ids)) {
            return;
        }
        $ids_str = implode(',', $ids);
        $db = Db::obtain();
        $slots = $db->fetchArrayPDO(
            'SELECT id FROM ' . Db::realTablename('slots') . ' WHERE match_id IN (' . $ids_str . ')',
            array()
        );
        $slotsFormatted = array();
        foreach ($slots as $slot) {
            array_push($slotsFormatted, $slot['id']);
        }
        if (count($slotsFormatted)) {
            $slots_str = implode(',', $slotsFormatted);
            $db->exec('DELETE FROM ' . Db::realTablename('ability_upgrades') . ' WHERE slot_id IN (' . $slots_str . ')');
            $db->exec('DELETE FROM ' . Db::realTablename('additional_units') . ' WHERE slot_id IN (' . $slots_str . ')');
            $db->exec('DELETE FROM ' . Db::realTablename('slots') . ' WHERE id IN (' . $slots_str . ')');
        }
        $db->exec('DELETE FROM ' . Db::realTablename('picks_bans') . ' WHERE match_id IN (' . $ids_str . ')');
        $db->exec('DELETE FROM ' . Db::realTablename('matches') . ' WHERE match_id IN (' . $ids_str . ')');
    }

    /**
     * Load data about slots from "slots" table.
     * Array of the matches ids used as filter
     *
     * @param array $matchesIds
     * @return array
     */
    private function _loadSlotsInfo(array $matchesIds)
    {
        $db = Db::obtain();
        $slots_query = 'SELECT * FROM ' . Db::realTablename('slots') . ' WHERE match_id IN (' . implode(
            ',',
            $matchesIds
        ) . ')';
        return $db->fetchArrayPDO($slots_query, array());
    }

    /**
     * Load data about picks and bans from "picks_bans" table.
     * Array of the matches ids used as filter.
     * Data is formatted like
     * <code>
     * Array (
     *      [match_id] => Array (
     *          [0] => Array(
     *              [id] => int
     *              [match_id] => match_id
     *              [is_pick] => 0/1
     *              [hero_id] => hero_id
     *              [team] => 0/1
     *              [order] => 0/1
     *          )
     *          ...
     *      )
     *      ...
     *  )
     * </code>
     * @param array $matchesIds
     * @return array
     */
    private function _loadPicksBansInfo(array $matchesIds)
    {
        $db = Db::obtain();
        $picksBansQuery = 'SELECT * FROM ' . Db::realTablename('picks_bans') . ' WHERE match_id IN (' . implode(
            ',',
            $matchesIds
        ) . ')';
        $picksBansInfo = $db->fetchArrayPDO($picksBansQuery, array());
        // reformat picks_bans array
        $picksBansFormattedInfo = array();
        foreach ($picksBansInfo as $pickBanInfo) {
            if (!array_key_exists($pickBanInfo['match_id'], $picksBansFormattedInfo)) {
                $picksBansFormattedInfo[$pickBanInfo['match_id']] = array();
            }
            array_push($picksBansFormattedInfo[$pickBanInfo['match_id']], $pickBanInfo);
        }
        return $picksBansFormattedInfo;
    }

    /**
     * Load data about ability upgrades from "ability_upgrades" table.
     * Array of the slots ids used as filter.
     * Data is formatted like
     * <code>
     * Array (
     *      [slot_id] => Array (
     *          [0] => Array (
     *              [slot_id] => slot_id
     *              [ability] => ability identifier
     *              [time] => timestamp from the match start time
     *              [level] => hero level
     *          )
     *          ...
     *      )
     *      ...
     * )
     * </code>
     * @param array $slotsIds
     * @return array
     */
    private function _loadAbilityUpgradesInfo(array $slotsIds)
    {
        $db = Db::obtain();
        $abilitiesUpgradeQuery = 'SELECT * FROM ' . Db::realTablename('ability_upgrades') . ' WHERE slot_id IN (' . implode(
            ',',
            $slotsIds
        ) . ') ORDER BY slot_id, level ASC';
        $abilitiesUpgradeInfo = $db->fetchArrayPDO($abilitiesUpgradeQuery, array());

        // reformat abilities upgrades array
        $abilitiesUpgradeFormattedInfo = array();
        foreach ($abilitiesUpgradeInfo as $abilityUpgradeInfo) {
            if (!array_key_exists($abilityUpgradeInfo['slot_id'], $abilitiesUpgradeFormattedInfo)) {
                $abilitiesUpgradeFormattedInfo[$abilityUpgradeInfo['slot_id']] = array();
            }
            array_push($abilitiesUpgradeFormattedInfo[$abilityUpgradeInfo['slot_id']], $abilityUpgradeInfo);
        }
        return $abilitiesUpgradeFormattedInfo;
    }

    /**
     * Load data about additional units from "additional_units" table.
     * Array of the slots ids used as filter.
     * Data is formatted like
     * <code>
     * Array (
     *      [slot_id] => Array (
     *          [slot_id] => slot_id
     *          [unitname] => spirit_bear (example)
     *          [item_0] => item_id
     *          [item_1] => item_id
     *          [item_2] => item_id
     *          [item_3] => item_id
     *          [item_4] => item_id
     *          [item_5] => item_id
     *      )
     *      ...
     * )
     * </code>
     * @param array $slotsIds
     * @return array
     */
    private function _loadAdditionalUnitsInfo(array $slotsIds)
    {
        $db = Db::obtain();
        $additionalUnitsQuery = 'SELECT * FROM ' . Db::realTablename('additional_units') . ' WHERE slot_id IN (' . implode(
            ',',
            $slotsIds
        ) . ')';
        $additionalUnitsInfo = $db->fetchArrayPDO($additionalUnitsQuery, array());
        $additionalUnitsFormattedInfo = array();
        foreach ($additionalUnitsInfo as $additionalUnitInfo) {
            if (!array_key_exists($additionalUnitInfo['slot_id'], $additionalUnitsFormattedInfo)) {
                $additionalUnitsFormattedInfo[$additionalUnitInfo['slot_id']] = array();
            }
            $additionalUnitsFormattedInfo[$additionalUnitInfo['slot_id']] = $additionalUnitInfo;
        }
        return $additionalUnitsFormattedInfo;
    }

    /**
     * Get info about matches from the "matches" table
     * Use matches ids from slots table as additional filter
     * @return array
     */
    private function _getMatchesIdsFromMatches()
    {
        $_matchesIdsFromSlots = $this->_getMatchesIdsFromSlots();
        $db = Db::obtain();
        // basic matches data
        $matchesQuery = 'SELECT * FROM ' . Db::realTablename('matches') . '';
        $where = '';
        $data = array();

        if (null !== $this->getLeagueId()) {
            $where .= 'leagueid = ? AND ';
            array_push($data, $this->getLeagueId());
        }

        if (null !== $this->getTeamId()) {
            $where .= '(radiant_team_id = ? OR dire_team_id = ?) AND ';
            array_push($data, $this->getTeamId());
            array_push($data, $this->getTeamId());
        }

        if (count($_matchesIdsFromSlots)) {
            $where .= 'match_id IN (' . implode(',', $_matchesIdsFromSlots) . ') AND ';
        }

        if (null !== $this->getStartAtMatchId()) {
            $where .= 'match_id > ? AND ';
            array_push($data, $this->getStartAtMatchId());
        }

        if (trim($where) !== '') {
            $matchesQuery .= ' WHERE ' . substr($where, 0, strlen($where) - 4);
        }

        $matchesQuery .= ' ORDER BY start_time DESC';

        if (null !== $this->getMatchesRequested()) {
            $matchesQuery .= ' LIMIT ?';
            array_push($data, $this->getMatchesRequested());
        }

        $matchesInfo = $db->fetchArrayPDO($matchesQuery, $data);
        // no one match found
        if (count($matchesInfo) === 0) {
            return array();
        }
        return $matchesInfo;
    }

    /**
     * Get matches ids with provided hero_id or account_id (query for "slots" table)
     * @return array
     */
    private function _getMatchesIdsFromSlots()
    {
        $db = Db::obtain();
        $forSlots = 'SELECT match_id ' . Db::realTablename('FROM slots') . '';
        $whereForSlots = '';
        $dataForSlots = array();

        if (null !== $this->getHeroId()) {
            $whereForSlots .= 'hero_id = ? AND ';
            array_push($dataForSlots, $this->getHeroId());
        }
        if (null !== $this->getAccountId()) {
            $whereForSlots .= 'account_id = ? AND ';
            array_push($dataForSlots, $this->getAccountId());
        }

        if (trim($whereForSlots) !== '') {
            $forSlots .= ' WHERE ' . substr($whereForSlots, 0, strlen($whereForSlots) - 4);
        }
        $matchesIds = $db->fetchArrayPDO($forSlots, $dataForSlots);
        $ret = array();
        foreach ($matchesIds as $val) {
            array_push($ret, $val['match_id']);
        }
        return $ret;
    }
}
