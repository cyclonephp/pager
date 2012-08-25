<?php
namespace cyclone\pager;

use cyclone\request\Request;
use cyclone\request\Route;
use cyclone\URL;

/**
 * @author Bence Erős <crystal@cyclonephp.org>
 * @package pager
 */
class PagerTest extends \Kohana_Unittest_TestCase {

    public function test_for_request() {
        $req = Request::factory('')->query(array(
            'listsize' => 15
        ))->params(array(
            'pageidx' => 45
        ));

        $pager = Pager::for_request($req, 'params', 'pageidx', 'query', 'listsize');
        $this->assertEquals(15, $pager->get_pagesize());
        $this->assertEquals(45, $pager->get_page());

        $req->params(array());
        $pager = Pager::for_request($req, 'params', 'pageidx', 'query', 'listsize');
        $this->assertEquals(15, $pager->get_pagesize());
        $this->assertEquals(1, $pager->get_page());
    }

    public function test_for_fixed_pagesize() {
        $req = Request::factory('')->params(array(
            'pageidx' => 45
        ));

        $pager = Pager::for_fixed_pagesize($req, 30, 'params', 'pageidx');
        $this->assertEquals(45, $pager->get_page());
        $this->assertEquals(30, $pager->get_pagesize());
    }

    public function test_get_url_no_route() {
        $req = Request::factory('')->query(array(
            'pageidx' => 10
        ));
        $pager = Pager::for_fixed_pagesize($req, 30, 'query', 'pageidx');
        $this->assertEquals(URL::base() . URL::query(array('pageidx' => 4)), $pager->get_url(4));
    }

    public function test_relative_url() {
        $req = Request::factory('')->query(array(
            'pageidx' => 10
        ));
        $pager = Pager::for_fixed_pagesize($req, 30, 'query', 'pageidx')
            ->relative_url(TRUE);
        $this->assertEquals(URL::query(array('pageidx' => 10)), $pager->get_url(10));
    }

}
