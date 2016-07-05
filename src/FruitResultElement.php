<?php

class FruitResultElement implements JsonSerializable
{
    /**
     * The product title
     * @string
     */
    private $title;

    /**
     * the product 'more information' page url
     * @string
     */
    private $info_page_url;

    /**
     * the product 'more information' page size
     * @int
     */
    private $info_page_size;

    /**
     * product price, in GBP
     * @float
     */
    private $unit_price;

    /**
     * product description
     * @string
     */
    private $description;

    /**
     * a cache of the object dom node
     * DOMNode
     */
    private $dom_node;

    /**
     * a cache of the object's xpath rendering
     * DOMXPath
     */
    private $dom_xpath;

    /**
     * FruitResultElement constructor.
     * @param DOMNode $dom_node
     */
    public function __construct($dom_node) {
        $this->dom_node = $dom_node;
        $dom = new DOMDocument();
        $imported_node = $dom->importNode($dom_node, true); // attempting to import the cached version fails.
        $dom->appendChild($imported_node);

        $this->dom_xpath = new DOMXPath($dom);

        $this->populate_object();
    }

    /**
     * This is quite brittle, and depends on the exact html being passed in
     */
    private function populate_object() {
        $this->populate_by_xpath('title', "//div[@class='productInfo']/h3/a", 'textContent');

        $this->populate_by_xpath('unit_price', "//div[@class='pricing']/p[@class='pricePerUnit']", 'textContent');
        $this->unit_price_to_float();

        $this->populate_by_xpath('info_page_url', "//div[@class='productInfo']/h3/a", 'href');
        $this->fetch_info_page_values();
    }


    private function populate_by_xpath($param_name, $xpath_query, $attribute) {
        $this->$param_name = $this->get_by_xpath($xpath_query, $attribute);
    }

    private function get_by_xpath($xpath_query, $attribute) {
        $xpath_results = $this->dom_xpath->query($xpath_query);
        if ($attribute == null || $attribute == 'textContent') {
            return trim($xpath_results[0]->textContent);
        } else {
            return trim($xpath_results[0]->getAttribute($attribute));
        }
    }

    /**
     * Strips the extraneous text from the unit price in the page
     */
    private function unit_price_to_float() {
        $pattern = '/\&pound(\d+\.\d\d)\/unit/';
        preg_match($pattern, $this->unit_price, $matches);
        $this->unit_price = $matches[1];
    }

    private function fetch_info_page_values() {
        $target_url = new Url($this->info_page_url);
        $dom = $target_url->get_dom_document();
        $xpath = new DOMXPath($dom);
        $xpath_query = "//h3[@class='productDataItemHeader' and text()='Description']/following-sibling::div[@class='productText'][1]";
        $xpath_results = $xpath->query($xpath_query);
        $this->description = trim($xpath_results[0]->textContent);

        $this->info_page_size = round(($target_url->get_url_content_size()/1024) , 1) . 'kb';
    }

    public function jsonSerialize() {
        return array(
            'title' => $this->title,
            'unit_price' => $this->unit_price,
            'description' => $this->description,
            'size' => $this->info_page_size,
        );
    }

    public function get_unit_price() {
        return $this->unit_price;
    }
}