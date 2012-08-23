<?php

namespace cyclone\pager;

class MockURLProvider implements URLProvider {

    public function get_url($page_num) {
        return "url/$page_num";
    }

}