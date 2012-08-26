<?php

namespace app\controller;

use cyclone\request\BaseController;
use cyclone\pager\Pager;
use cyclone\DB;

/**
 * Hello-world pager example.
 *
 * @package app
 */
class PagerController extends BaseController {

    /**
     * This controller displays the rows of the "items" database table in a paginated manner.
     */
    public function action_index() {
        // adding some CSS to the output - this example CSS file is contained by the pager library
        $this->add_css('pager/examples/skeleton');
        // creating a pager instance for pages containing 10 items
        // the actual page number will be extracted from the 'page' key of the query string
        // ( for example http://localhost/cyclonephp/pager/?page=3 )
        $pager = Pager::for_fixed_pagesize($this->_request, 10, 'query', 'page');
        // fetching the total count of rows in the database table
        $cnt_row = DB::select(DB::expr('count(*) as cnt'))->from('items')
            ->exec()->get_single_row();
        // setting the total count for the pager object
        $pager->total_count($cnt_row['cnt']);

        // creating the query
        $query = DB::select()->from('items')
            // setting the offset - limit values based on the page and pagesize values
            // which can be read from the $pager object
            ->offset(($pager->get_page() - 1) * $pager->get_pagesize())
            ->limit($pager->get_pagesize());

        // setting the template variables
        // $items will be an iterable collection in the template
        $this->_content->items = $query->exec();
        // $pager will be the HTML code of the pager
        $this->_content->pager = $pager->render();
    }

}