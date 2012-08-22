<?php 

namespace cyclone\pager;

/**
 * @author Bence Eros <crystal@cyclonephp.org>
 * @package pager
 */
interface ParamSource {

    public function get_page();

    public function get_pagesize();

}
