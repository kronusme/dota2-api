<?php
/**
 * Information about items
 *
 * @author KronuS
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
class items extends data {
    public function __construct() {
        $this->set_filename('items.json');
        $this->set_field('items');
    }
}
