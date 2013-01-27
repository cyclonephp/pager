<?php 

namespace cyclone\pager;

use cyclone\request\Request;
use cyclone\request\Route;
use cyclone\Config;
use cyclone\URL;

/**
 * @author Bence Eros <crystal@cyclonephp.org>
 * @package pager
 */
class Pager implements ParamSource, URLProvider{

    /**
     * @var PagerCore
     */
    protected $_pager_core;

    /**
     * @var \cyclone\request\Request
     */
    protected $_request;

    protected $_page_src;

    protected $_page_key;

    protected $_page_value;

    protected $_pagesize_src;

    protected $_pagesize_key;

    protected $_pagesize_value;

    protected $_relative_url;

    protected function __construct($page_value, $pagesize_value, Request $request = NULL) {
        $this->_request = $request;
        $this->_page_value = $page_value;
        $this->_pagesize_value = $pagesize_value;
        $this->_pager_core = new PagerCore($this, $this);
    }

    public static function for_request(Request $request
        , $page_src = NULL, $page_key = NULL
        , $pagesize_src = NULL, $pagesize_key = NULL) {
        $cfg = Config::inst()->get('pager');
        $page_value = self::get_page_value($request, $cfg, $page_src, $page_key);
        $pagesize_value = self::get_pagesize_value($request, $cfg, $pagesize_src, $pagesize_key);
        $pager = new Pager($page_value, $pagesize_value, $request);
        $pager->_page_src = $page_src;
        $pager->_page_key = $page_key;
        $pager->_pagesize_src = $pagesize_src;
        $pager->_pagesize_key = $pagesize_key;
        return $pager;
    }

    private static function get_pagesize_value($request, $cfg, $pagesize_src, $pagesize_key) {
        if ($pagesize_src === NULL) {
            if ( ! isset($cfg['request']['pagesize_src']))
                throw new Exception('failed to determine pagesize parameter source');

            $pagesize_src = $cfg['request']['pagesize_src'];
        }

        if ($pagesize_key === NULL) {
            if ( ! isset($cfg['request']['pagesize_key']))
                throw new Exception('failed to determine pagesize parameter key');

            $pagesize_key = $cfg['request']['pagesize_key'];
        }

        if ($pagesize_src === 'param') {
            $pagesize_value = isset($request->params[$pagesize_key])
                ? $request->params[$pagesize_key]
                : 1;
        } elseif ($pagesize_src === 'query') {
            $pagesize_value = isset($request->query[$pagesize_key])
                ? $request->query[$pagesize_key]
                : 1;
        } else
            throw new Exception("invalid pagesize source '$pagesize_src'. Expected: 'param' or 'query'");

        return $pagesize_value;
    }

    private static function get_page_value($request, $cfg, $page_src, $page_key) {
        if ($page_src === NULL) {
            if ( ! isset($cfg['request']['page_src']))
                throw new Exception('failed to determine page parameter source');

            $page_src = $cfg['request']['page_src'];
        }

        if ($page_key === NULL) {
            if ( ! isset($cfg['request']['page_key']))
                throw new Exception('failed to determine page parameter key');

            $page_key = $cfg['request']['page_key'];
        }

        if ($page_src === 'params') {
            $page_value = isset($request->params[$page_key])
                ? $request->params[$page_key]
                : 1;
        } elseif ($page_src === 'query') {
            $page_value = isset($request->query[$page_key])
                ? $request->query[$page_key]
                : 1;
        } else
            throw new Exception("invalid page source '$page_src'. Expected: 'params' or 'query'");

        return $page_value;
    }

    public static function for_fixed_pagesize(Request $request
        , $pagesize
        , $page_src = NULL, $page_key = NULL) {
        $cfg = Config::inst()->get('pager');
        $page_value = static::get_page_value($request, $cfg, $page_src, $page_key);
        $pager = new Pager($page_value, $pagesize, $request);
        $pager->_page_src = $page_src;
        $pager->_page_key = $page_key;
        return $pager;
    }

    /**
     * @param $page_value
     * @return Pager
     */
    public function page($page_value) {
        $this->_page_value = $page_value;
        return $this;
    }

    /**
     * @param $pagesize_value
     * @return Pager
     */
    public function pagesize($pagesize_value) {
        $this->_pagesize_value = $pagesize_value;
        return $this;
    }

    /**
     * @param $total_count
     * @return Pager
     */
    public function total_count($total_count) {
        $this->_pager_core->total_count($total_count);
        return $this;
    }

    /**
     * @param bool $relative_url
     * @return Pager
     */
    public function relative_url($relative_url = TRUE) {
        $this->_relative_url = $relative_url;
        return $this;
    }

    public function get_page() {
        return $this->_page_value;
    }

    /**
     * Returns the maximum page size (the maximum number of entries on a page).
     *
     * @return int
     */
    public function get_pagesize() {
        return $this->_pagesize_value;
    }

    /**
     * Returns the number of items on the current page. If the current page is the last page then this value
     * will probably not the same as @c get_pagesize() .
     *
     * @return int
     */
    public function get_current_pagesize() {
        return $this->_pager_core->get_current_pagesize();
    }

    /**
     * Sets the template file name to be used for rendering the pager.
     *
     * @param $template string
     * @return Pager
     */
    public function template($template) {
        $this->_pager_core->template($template);
        return $this;
    }

    public function get_url($page_num) {
        $query = $this->_request->query;

        $params = $this->_request->params === NULL ? array() : $this->_request->params;

        if ($this->_page_src === 'params') {
            $params[$this->_page_key] = $page_num;
        } elseif ($this->_page_src === 'query') {
            $query[$this->_page_key] = $page_num;
        }

        if ($this->_pagesize_src === 'params') {
            $params[$this->_pagesize_key] = $this->_pagesize_value;
        } elseif ($this->_pagesize_src === 'query') {
            $query[$this->_pagesize_key] = $this->_pagesize_value;
        }

        $query_string = URL::query($query->getArrayCopy());
        $base_url = $this->_relative_url ? '' : URL::base();
        if ($this->_request->route === NULL) {
            return $base_url . $query_string;
        }
        return $base_url . $this->_request->route->uri($params) . $query_string;
    }

    public function get_view() {
        return $this->_pager_core->render();
    }

    public function render() {
        return $this->_pager_core->render();
    }

}
