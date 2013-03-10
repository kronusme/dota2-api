<?php
/**
 * Information about items
 *
 * @author kronus
 * @package data
 * @example
 * <code>
 *   $items = new items();
 *   $items->parse();
 *   $items-get_data_by_id(149); // get info about Crystalis
 *   $items->get_img_url_by_id(149, false); // large image
 *   $items->get_img_url_by_id(149); // thumb
 * </code>
 */
class items extends heroes_data {
    /**
     * Empty slot has id = 0
     */
    const empty_id = 0;
    public function __construct() {
        $this->set_filename('items.json');
        $this->set_field('items');
    }
}
