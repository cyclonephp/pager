<?php

namespace cyclone\pager;

use cyclone\Config;
use cyclone\view\AbstractView;

/**
 * <p>Core class of the pager library.</p>
 *
 * <p>The class depends on a @c ParamSource instance which provides the current page and page size
 * parameters and on a @c URLProvider instance which is able to generate the link for a given
 * page number. The @c PagerCore class is responsible to perform the pagination-related
 * calculations and creating the view instance for rendering the pager.</p>
 *
 * <p>In most cases applications don't have to directly interact with the @c PagerCore class,
 * the @c Pager class is a convenient wrapper for coupling a @c cyclone\request\Request instance
 * and a @c PagerCore object.</p>
 *
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
     * <p>Sets the total number of items to be paginated.</p>
     *
     * <p>This method MUST be set before rendering otherwise @c get_view() will throw an
     * @c Exception .</p>
     *
     * @param $total_count int
     * @return PagerCore this
     */
    public function total_count($total_count) {
        $this->_total_count = $total_count;
        return $this;
    }

    /**
     * <p>Sets the <code>auto_hide</code> property, overrides the <code>pager.auto_hide</code>
     * configuration setting.</p>
     *
     * @param $auto_hide boolean
     * @return PagerCore
     */
    public function auto_hide($auto_hide) {
        $this->_auto_hide = $auto_hide;
        return $this;
    }

    /**
     * <p>Sets the template file name to be used for rendering the pager. Overrides the
     * <code>pager.template</code> configuration setting.</p>
     *
     *
     * @param $template
     * @return PagerCore
     */
    public function template($template) {
        $this->_template = $template;
        return $this;
    }

    /**
     * <p>Sets the number of links to be rendered including the dummy link of the current page
     * but not including the link to the first, previous, next and last pages.</p>
     *
     * <p>For example if <code>link_count</code> is 11 then 5 links will be rendered before
     * the current page and 5 links will be rendered after the current page assuming that the
     * current page is somewhere in the middle of the page link list.</p>
     *
     * <p>In other cases - when the current page is at the beginning or the end of the pagelist
     * the number of links before and after the current page will be aligned but the total
     * number of list will be still 10. For example if <code>link_count</code> is 11 and the
     * current page is the 3rd page then 2 links will be rendered before the current page and
     * 8 links will be rendered after the current page.</p>
     *
     * @param $link_count
     * @return PagerCore
     */
    public function link_count($link_count) {
        $this->_link_count = $link_count;
        return $this;
    }

    protected function add_static_pagenums(AbstractView $view, $current_page, $page_count) {
        $url_provider = $this->_url_provider;
        if ($current_page == 1) {
            $view->first_page_url = FALSE;
            $view->prev_page_url = FALSE;
        } else {
            $view->first_page_url = $url_provider->get_url(1);
            $view->prev_page_url = $url_provider->get_url($current_page - 1);
        }

        if ($current_page == $page_count) {
            $view->next_page_url = FALSE;
            $view->last_page_url = FALSE;
        } else {
            $view->next_page_url = $url_provider->get_url($current_page + 1);
            $view->last_page_url = $url_provider->get_url($page_count);
        }
    }

    protected function add_iterators($view, $current_page, $page_count) {
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

    public function get_current_pagesize() {
        $current_page = $this->_param_source->get_page();
        $page_count = $this->get_page_count();
        $pagesize = $this->_param_source->get_pagesize();
        if ($current_page < $page_count) {
            return $pagesize;
        } else {
            return $this->_total_count % $pagesize;
        }
    }

    private function get_page_count() {
        return (int) ceil($this->_total_count / $this->_param_source->get_pagesize());
    }

    /**
     * <p>Creates and returns the view object for the pager.</p>
     *
     * <p>If the number of pages to be rendered is 1 and the <code>auto_hide</code> property is
     * <code>TRUE</code> then it will return <code>NULL</code>.</p>
     *
     * <p>Otherwise it will return a view object, its template file will be the property set by
     * @c template() or defined by the <code>pager.template</code> configuration setting.</p>
     *
     * <p>If you plan to implement your own template, the following variables will be available
     * in the template:
     * <dl>
     * <dt>$page_count</dt>
     *      <dd>the number of available pages</dd>
     * <dt>$first_item_offset</dt>
     *      <dd>the position of the first item currently rendered in the entire dataset to be paginated</dd>
     * <dt>$last_item_offset</dt>
     *      <dd>the position of the last item currently rendered in the entire dataset to be paginated</dd>
     * <dt>$total_count</dt>
     *      <dd>the total number of items in the dataset, set by @c total_count()</dd>
     * <dt>$current_page</dt>
     *      <dd>the number of the current page, provided by @c ParamSource::get_page() .</dd>
     * <dt>$before_links</dt>
     *      <dd>a collection as page number =&gt; link URL pairs, which should be rendered before
     *          the current page. It will be a @c LinkIterator instance.</dd>
     * <dt>$after_links</dt>
     *      <dd>a collection as page number =&gt; link URL pairs, which should be rendered after
     *          the current page. It will be a @c LinkIterator instance.</dd>
     * <dt>$first_page_url</dt>
     *      <dd>The URL of the first page. It is <code>FALSE</code> if the current page is the
     *          first page and this link should be hidden.</dd>
     * <dt>$prev_page_url</dt>
     *      <dd>The URL of the previous page. It is <code>FALSE</code> if the current page is the
     *          previous page and this link should be hidden.</dd>
     * <dt>$next_page_url</dt>
     *      <dd>The URL of the next page. It is <code>FALSE</code> if the current page is the
     *          last page and this link should be hidden.</dd>
     * <dt>$last_page_url</dt>
     *      <dd>The URL of the last page. It is <code>FALSE</code> if the current page is the
     *          last page and this link should be hidden.</dd>
     * </dl>
     *
     * @return \cyclone\view\AbstractView
     * @throws Exception in the following cases:
     *  <ol>
     *      <li>the total number of items has not been set using @c total_count() </li>
     *      <li>the current page returned by @c ParamSource::get_page() is lower than 1 or
     *          higher than the total number of pages</li>
     *  </ol>
     */
    public function get_view() {
        if (NULL === $this->_total_count)
            throw new Exception("total_count must be set before rendering");

        $current_page = $this->_param_source->get_page();
        if ($current_page < 1)
            throw new Exception("invalid value returned by ParamSource::get_page(): '$current_page'");

        $page_size = $this->_param_source->get_pagesize();
        $page_count = $this->get_page_count();

        if ($this->_auto_hide && $page_count === 1) {
            return NULL;
        }

        if ($current_page > $page_count)
            throw new Exception("invalid state: current_page = '$current_page' > page_count = '$page_count'");

        $first_item_offset = ($current_page - 1) * $page_size + 1;
        $last_item_offset = min($current_page * $page_size, $this->_total_count);

        $view = AbstractView::factory($this->_template, array(
            'page_count' => $page_count,
            'first_item_offset' => $first_item_offset,
            'last_item_offset' => $last_item_offset,
            'total_count' => $this->_total_count,
            'current_page' => $current_page
        ));
        $this->add_static_pagenums($view, $current_page, $page_count);
        $this->add_iterators($view, $current_page, $page_count);
        return $view;
    }

    /**
     * <p>Renders the pager and returns its HTML markup as a string.</p>
     *
     * <p>Calls @c get_view() then
     * <ul>
     *  <li>returns an empty string if <code>get_view()</code> returned <code>NULL</code></li>
     *  <li>otherwise it generates the HTML markup of the pager using
     *      @c \cyclone\view\AbstractView::render() and returns it as a string</li>
     * </ul>
     * @return string
     */
    public function render() {
        $view = $this->get_view();
        if ($view === NULL)
            return '';

        return $view->render();
    }

}
