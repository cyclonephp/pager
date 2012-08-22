<?php 

namespace cyclone\pager;

/**
 * @author Bence Eros <crystal@cyclonephp.org>
 * @package pager
 */
interface URLProvider {

    public function get_url($page_num);

}
