<?php

namespace cyclone\pager;

use cyclone\Config;

/**
 * @author Bence Eros <crystal@cyclonephp.org>
 * @package pager
 */
class PagerCore {

    /**
     * @var boolean
     */
    protected $_show_first_link;

    /**
     * @var boolean
     */
    protected $_show_prev_link;

    /**
     * @var boolean
     */
    protected $_show_next_link;

    /**
     * @var boolean
     */
    protected $_show_last_link;

    /**
     * The parameter source which will be used to fetch the current page and the page size.
     *
     * @var ParamSource
     */
    protected $_param_source;

    /**
     * The URL provider which will be used to generate the URL-s in the pager links.
     *
     * @var URLProvider
     */
    protected $_url_provider;

    public function __construct(ParamSource $param_source, URLProvider $url_provider) {
        $this->_param_source = $param_source;
        $this->_url_provider = $url_provider;
        $cfg = Config::inst()->get('pager');
        $this->_show_first_link = $cfg['show_first_link'];
        $this->_show_prev_link = $cfg['show_prev_link'];
        $this->_show_next_link = $cfg['show_next_link'];
        $this->_show_last_link = $cfg['show_last_link'];
    }

}
