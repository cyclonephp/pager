<?php
namespace cyclone\pager;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'MockParamSource.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'MockURLProvider.php';

class PagerCoreTest extends \Kohana_Unittest_TestCase {

    public static function create_pager($page, $pagesize, $total_count) {
        $pager = new PagerCore(new MockParamSource($page, $pagesize), new MockURLProvider());
        return $pager->total_count($total_count);
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
        $this->assertFalse($view->first_page_num);
        $this->assertFalse($view->prev_page_num);
        $this->assertFalse($view->next_page_num);
        $this->assertFalse($view->last_page_num);

        $pager = self::create_pager(1, 10, 10);
        $view = $pager->get_view();
        $this->assertFalse($view->first_page_num);
        $this->assertFalse($view->prev_page_num);
        $this->assertFalse($view->next_page_num);
        $this->assertFalse($view->last_page_num);

        $pager = self::create_pager(1, 10, 11);
        $view = $pager->get_view();
        $this->assertFalse($view->first_page_num);
        $this->assertFalse($view->prev_page_num);
        $this->assertEquals('url/2', $view->next_page_num);
        $this->assertEquals('url/2', $view->last_page_num);

        $pager = self::create_pager(2, 10, 11);
        $view = $pager->get_view();
        $this->assertEquals('url/1', $view->first_page_num);
        $this->assertEquals('url/1', $view->prev_page_num);
        $this->assertFalse($view->next_page_num);
        $this->assertFalse($view->last_page_num);

        $pager = self::create_pager(3, 10, 41);
        $view = $pager->get_view();
        $this->assertEquals('url/1', $view->first_page_num);
        $this->assertEquals('url/2', $view->prev_page_num);
        $this->assertEquals('url/4', $view->next_page_num);
        $this->assertEquals('url/5', $view->last_page_num);
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
}