<?php

namespace cyclone\pager;

/**
 * Used to iterate pager links in a from - to interval.
 *
 * The <code>$before_links</code> and <code>$after_links</code> template variables of a pager
 * template are @c LinkIterator instances (see the <code>&lt;pager-root&gt;/view/pager/pager.php
 * </code> for example usage).
 *
 * @author Bence Erős <crystal@cyclonephp.org>
 * @package pager
 */
class LinkIterator implements \Iterator {

    /**
     * The lowest page number, where the iteration will start
     *
     * @var int
     */
    protected $_from;

    /**
     * The highest page number, where the iteration will finish
     *
     * @var int
     */
    protected $_to;

    /**
     * The provider which is responsible for generating the URL of the link of a given page.
     *
     * @var URLProvider
     */
    protected $_url_provider;

    /**
     * Internal counter
     *
     * @var int
     */
    protected $_ctr;

    /**
     * @param $from int the lowest page number, where the iteration will start
     * @param $to int the highest page number, where the iteration will finish
     * @param URLProvider $url_provider the provider which is responsible for
     *  generating the URL of the link of a given page.
     */
    public function  __construct($from, $to, URLProvider $url_provider) {
        $this->_from = $from;
        $this->_to = $to;
        $this->_url_provider = $url_provider;
    }

    /**
     * Returns the URL for the page number returned by @c key() . The URL
     * is generated by a @c URLProvider instance passed in the constructor.
     *
     * @see URLProvider::get_url()
     * @return string
     */
    public function current() {
        return $this->_url_provider->get_url($this->_ctr);
    }

    /**
     * Returns a page number
     *
     * @return int
     */
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
