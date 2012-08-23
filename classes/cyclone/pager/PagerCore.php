<?php

namespace cyclone\pager;

use cyclone\Config;
use cyclone\view\AbstractView;

/**
 * @author Bence Eros <crystal@cyclonephp.org>
 * @package pager
 */
class PagerCore {

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

    /**
     * @var int
     */
    protected $_total_count;

    /**
     * @var boolean
     */
    protected $_auto_hide;

    /**
     * @var string
     */
    protected $_template;

    protected $_link_count;

    public function __construct(ParamSource $param_source, URLProvider $url_provider) {
        $this->_param_source = $param_source;
        $this->_url_provider = $url_provider;
        $cfg = Config::inst()->get('pager');
        $this->_auto_hide = $cfg['auto_hide'];
        $this->_template = $cfg['template'];
        $this->_link_count = $cfg['link_count'];
    }

    /**
     * @param $total_count int
     * @return PagerCore this
     */
    public function total_count($total_count) {
        $this->_total_count = $total_count;
        return $this;
    }

    /**
     * @param $auto_hide boolean
     * @return PagerCore
     */
    public function auto_hide($auto_hide) {
        $this->_auto_hide = $auto_hide;
        return $this;
    }

    /**
     * @param $template
     * @return PagerCore
     */
    public function template($template) {
        $this->_template = $template;
        return $this;
    }

    /**
     * @param $link_count
     * @return PagerCore
     */
    public function link_count($link_count) {
        $this->_link_count = $link_count;
        return $this;
    }

    private function add_static_pagenums(AbstractView $view, $current_page, $page_count) {
        $url_provider = $this->_url_provider;
        if ($current_page === 1) {
            $view->first_page_num = FALSE;
            $view->prev_page_num = FALSE;
        } else {
            $view->first_page_num = $url_provider->get_url(1);
            $view->prev_page_num = $url_provider->get_url($current_page - 1);
        }

        if ($current_page === $page_count) {
            $view->next_page_num = FALSE;
            $view->last_page_num = FALSE;
        } else {
            $view->next_page_num = $url_provider->get_url($current_page + 1);
            $view->last_page_num = $url_provider->get_url($page_count);
        }
    }

    private function add_iterators($view, $current_page, $page_count) {
        $min = $current_page - ($this->_link_count - 1) / 2;
        $max = $current_page + ($this->_link_count - 1) / 2;

        $before_from = $min;
        $before_to = $current_page - 1;

        if ($before_from < 1) {
            $diff = 1 - $before_from;
            $max += $diff;
            $before_from = 1;
        }

        $after_from = $current_page + 1;
        $after_to = $max;

        if ($after_to > $page_count) {
            $diff = $after_to - $page_count;
            $before_from -= $diff;
            $before_from = max($before_from, 1);
            $after_to = $page_count;
        }

        $view->before_links = new LinkIterator($before_from, $before_to, $this->_url_provider);

        $view->after_links = new LinkIterator($after_from, $after_to, $this->_url_provider);
    }

    /**
     * @return \cyclone\view\AbstractView
     */
    public function get_view() {
        if (NULL === $this->_total_count)
            throw new PagerException("total_count must be set before rendering");

        $current_page = $this->_param_source->get_page();
        $page_size = $this->_param_source->get_pagesize();
        $page_count = (int) ceil($this->_total_count / $page_size);

        $first_item_offset = ($current_page - 1) * $page_size + 1;
        $last_item_offset = min($current_page * $page_size, $this->_total_count);

        $view = AbstractView::factory($this->_template, array(
            'page_count' => $page_count,
            'first_item_offset' => $first_item_offset,
            'last_item_offset' => $last_item_offset
        ));
        $this->add_static_pagenums($view, $current_page, $page_count);
        $this->add_iterators($view, $current_page, $page_count);
        return $view;
    }

    /**
     * @return string
     */
    public function render() {
        return $this->get_view()->render();
    }

}
