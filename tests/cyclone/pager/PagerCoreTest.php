<?php
namespace cyclone\pager;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'MockParamSource.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'MockURLProvider.php';

class PagerCoreTest extends \Kohana_Unittest_TestCase {

    public static function create_pager($page, $pagesize, $total_count) {
        $pager = new PagerCore(new MockParamSource($page, $pagesize), new MockURLProvider());
        return $pager->total_count($total_count)->auto_hide(FALSE);
    }

    public function test_page_count() {
        $pager = self::create_pager(1, 10, 20);
        $view = $pager->get_view();
        $this->assertEquals(2, $view->page_count);

        $pager->total_count(21);
        $view = $pager->get_view();
        $this->assertEquals(3, $view->page_count);

        $pager->total_count(29);
        $view = $pager->get_view();
        $this->assertEquals(3, $view->page_count);
    }

    public function test_first_last_prev_next_page_num() {
        $pager = self::create_pager(1, 10, 5);
        $view = $pager->get_view();
        $this->assertFalse($view->first_page_url);
        $this->assertFalse($view->prev_page_url);
        $this->assertFalse($view->next_page_url);
        $this->assertFalse($view->last_page_url);

        $pager = self::create_pager(1, 10, 10);
        $view = $pager->get_view();
        $this->assertFalse($view->first_page_url);
        $this->assertFalse($view->prev_page_url);
        $this->assertFalse($view->next_page_url);
        $this->assertFalse($view->last_page_url);

        $pager = self::create_pager(1, 10, 11);
        $view = $pager->get_view();
        $this->assertFalse($view->first_page_url);
        $this->assertFalse($view->prev_page_url);
        $this->assertEquals('url/2', $view->next_page_url);
        $this->assertEquals('url/2', $view->last_page_url);

        $pager = self::create_pager(2, 10, 11);
        $view = $pager->get_view();
        $this->assertEquals('url/1', $view->first_page_url);
        $this->assertEquals('url/1', $view->prev_page_url);
        $this->assertFalse($view->next_page_url);
        $this->assertFalse($view->last_page_url);

        $pager = self::create_pager(3, 10, 41);
        $view = $pager->get_view();
        $this->assertEquals('url/1', $view->first_page_url);
        $this->assertEquals('url/2', $view->prev_page_url);
        $this->assertEquals('url/4', $view->next_page_url);
        $this->assertEquals('url/5', $view->last_page_url);
    }

    public function test_offset() {
        $pager = self::create_pager(1, 10, 15);
        $view = $pager->get_view();
        $this->assertEquals(1, $view->first_item_offset);
        $this->assertEquals(10, $view->last_item_offset);

        $pager = self::create_pager(2, 10, 30);
        $view = $pager->get_view();
        $this->assertEquals(11, $view->first_item_offset);
        $this->assertEquals(20, $view->last_item_offset);

        $pager = self::create_pager(2, 10, 17);
        $view = $pager->get_view();
        $this->assertEquals(11, $view->first_item_offset);
        $this->assertEquals(17, $view->last_item_offset);
    }

    public function test_before_after_urls() {
        $pager = self::create_pager(4, 15, 200);
        $pager->link_count(5);
        $view = $pager->get_view();

        $expected = array(
            2 => 'url/2',
            3 => 'url/3'
        );
        $actual = array();
        foreach ($view->before_links as $page_num => $url) {
            $actual[$page_num] = $url;
        }
        $this->assertEquals($expected, $actual);

        $expected = array(
            5 => 'url/5',
            6 => 'url/6'
        );
        $actual = array();
        foreach ($view->after_links as $page_num => $url) {
            $actual[$page_num] = $url;
        }
        $this->assertEquals($expected, $actual);
    }

    public function test_before_links_cropped() {
        $pager = self::create_pager(2, 15, 200);
        $pager->link_count(9);
        $view = $pager->get_view();

        $expected = array(
            1 => 'url/1'
        );
        $actual = array();
        foreach ($view->before_links as $page_num => $url) {
            $actual[$page_num] = $url;
        }
        $this->assertEquals($expected, $actual);

        $expected = array(
            3 => 'url/3',
            4 => 'url/4',
            5 => 'url/5',
            6 => 'url/6',
            7 => 'url/7',
            8 => 'url/8',
            9 => 'url/9',
        );
        $actual = array();
        foreach ($view->after_links as $page_num => $url) {
            $actual[$page_num] = $url;
        }
        $this->assertEquals($expected, $actual);
    }

    public function test_after_links_cropped() {
        $pager = self::create_pager(9, 10, 100);
        $pager->link_count(9);
        $view = $pager->get_view();

        $expected = array(
            2 => 'url/2',
            3 => 'url/3',
            4 => 'url/4',
            5 => 'url/5',
            6 => 'url/6',
            7 => 'url/7',
            8 => 'url/8',
        );
        $actual = array();
        foreach ($view->before_links as $page_num => $url) {
            $actual[$page_num] = $url;
        }
        $this->assertEquals($expected, $actual);


        $expected = array(
            10 => 'url/10'
        );
        $actual = array();
        foreach ($view->after_links as $page_num => $url) {
            $actual[$page_num] = $url;
        }
        $this->assertEquals($expected, $actual);
    }

    public function test_before_after_links_cropped() {
        $pager = self::create_pager(3, 10, 50);
        $pager->link_count(9);
        $view = $pager->get_view();

        $expected = array(
            1 => 'url/1',
            2 => 'url/2'
        );
        $actual = array();
        foreach ($view->before_links as $page_num => $url) {
            $actual[$page_num] = $url;
        }
        $this->assertEquals($expected, $actual);

        $expected = array(
            4 => 'url/4',
            5 => 'url/5'
        );
        $actual = array();
        foreach ($view->after_links as $page_num => $url) {
            $actual[$page_num] = $url;
        }
        $this->assertEquals($expected, $actual);
    }

    /**
     * @expectedException \cyclone\pager\Exception
     */
    public function test_exc_on_invalid_page() {
        self::create_pager(0, 10, 20)->get_view();
    }

    /**
     * @expectedException \cyclone\pager\Exception
     */
    public function test_exc_on_high_curr_page() {
        self::create_pager(10, 10, 20)->get_view();
    }

    /**
     * @expectedException \cyclone\pager\Exception
     */
    public function test_exc_on_undefined_total() {
        $pager = new PagerCore(new MockParamSource(10, 20), new MockURLProvider());
        $pager->get_view();
    }

    public function test_auto_hide() {
        $pager = self::create_pager(1, 10, 5)->auto_hide(TRUE);
        $this->assertEquals(NULL, $pager->get_view());
        $this->assertEquals('', $pager->render());
    }

    public function test_get_current_pagesize() {
        $pager = self::create_pager(1, 10, 20);
        $this->assertEquals(10, $pager->get_current_pagesize());
        $pager = self::create_pager(2, 10, 15);
        $this->assertEquals(5, $pager->get_current_pagesize());
        $pager = self::create_pager(1, 10, 5);
        $this->assertEquals(5, $pager->get_current_pagesize());
    }

}