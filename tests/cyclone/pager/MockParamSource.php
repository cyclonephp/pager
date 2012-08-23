<?php

namespace cyclone\pager;

class MockParamSource implements ParamSource {

    private $_page;

    private $_pagesize;

    public function __construct($page, $pagesize) {
        $this->_page = $page;
        $this->_pagesize = $pagesize;
    }

    function get_page() {
        return $this->_page;
    }

    function get_pagesize() {
        return $this->_pagesize;
    }
}