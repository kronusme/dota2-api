<?php
/**
 * Load info about matches from web
 *
 * @author kronus
 * @package mappers
 * @example
 * <code>
 *   $matches_mapper_web = new matches_mapper_web();
 *   $matches_mapper_web->set_account_id(93712171);
 *   $matches_short_info = $matches_mapper_web->load();
 *   foreach ($matches_short_info AS $key=>$match_short_info) {
 *     $match_mapper = new match_mapper_web($key);
 *     $match = $match_mapper->load();
 *     $mm = new match_mapper_db();
 *     $mm->save($match);
 *   }
 * </code>
 */
class matches_mapper_web extends matches_mapper {
    /**
     * Request url
     */
    const steam_matches_url = 'https://api.steampowered.com/IDOTA2Match_570/GetMatchHistory/V001/';

    /**
     * @return array
     */
    private function _get_data_array() {
        $data = get_object_vars($this);
        $ret = array();
        foreach($data as $key => $value) {
            if (!is_array($value) && !is_null($value)) {
                $ret[ltrim($key, '_')] = $value;
            }
        }
        return $ret;
    }

    /**
     * @return array
     */
    public function load() {
        $request = new request(self::steam_matches_url, $this->_get_data_array());
        $response = $request->send();
        $xml = null;
        libxml_use_internal_errors(true);
        try {
            $xml = new SimpleXMLElement($response);
        }
        catch(Exception $e) {
            return null;
        }
        $matches = array();
        if (isset($xml->matches)) {
		    foreach ($xml->matches as $match) {
                foreach ($match as $m) {
                    $m_id = intval($m->match_id);
                    $matches[$m_id]['match_id'] = $m_id;
                    $matches[$m_id]['start_time'] = intval($m->start_time);
                    $matches[$m_id]['lobby_type'] = intval($m->lobby_type);
                    $matches[$m_id]['match_seq_num'] = intval($m->match_seq_num);
                }
            }
        }
        return $matches;
    }
}
