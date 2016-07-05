<?php
require_once '../src/Url.php';


class urlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnInvalidUrl() {
        $target_url = new Url('i am error');
    }

    // the get_headers function only works on http served pages, so can't do it on a local flat file
    public function testGetUrlContentSize() {
        $target_url = new Url('http://hiring-tests.s3-website-eu-west-1.amazonaws.com/2015_Developer_Scrape/5_products.html');
        $this->assertEquals($target_url->get_url_content_size(), 84542);
    }

    public function testGetDomDocument() {
        $target_url = new Url('file:///C:/Users/Will/IdeaProjects/sainsburys/tests/testPage.html');
        $this->assertTrue(is_a($target_url->get_dom_document(), 'DOMDocument'));
    }
}
