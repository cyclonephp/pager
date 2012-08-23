<?php

namespace cyclone\pager;
/**
 * @author Bence Erős <crystal@cyclonephp.org>
 * @package pager
 */
class LinkIterator implements \Iterator {

    protected $_from;

    protected $_to;

    /**
     * @var URLProvider
     */
    protected $_url_provider;

    protected $_ctr;

    public function  __construct($from, $to, URLProvider $url_provider) {
        $this->_from = $from;
        $this->_to = $to;
        $this->_url_provider = $url_provider;
    }

    public function current() {
        return $this->_url_provider->get_url($this->_ctr);
    }

    public function key() {
        return $this->_ctr;
    }

    public function next() {
        ++$this->_ctr;
    }

    public function rewind() {
        $this->_ctr = $this->_from;
    }

    public function valid() {
        return $this->_ctr <= $this->_to;
    }

}
