<?php

/**
 * Class Url
 * Gets and handles a URL resource
 */
class Url
{
    /**
     * path
     * @string
     */
    private $path;

    /**
     * the content of the url
     * @string
     */
    private $cached_page;

    /**
     * a dom representation of the url
     * @object DOMDocument
     */
    private $dom_document;

    /**
     * pre-loads the object with a given path
     * @param string $path
     */
    public function __construct($path) {
        $this->validate_path($path);
        $this->path = $path;
    }

    /**
     * Validates a given url against php's internal definition
     *
     * @param string $path
     * @throws InvalidArgumentException if the url is not valid
     */
    private function validate_path($path) {
        if (!filter_var($path, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Supplied URL appears invalid');
        }
    }

    /**
     * performs a header request on the url, and returns the filesize
     *
     * @return int
     */
    public function get_url_content_size() {
        $target_header = get_headers($this->path, TRUE);
        return $target_header['Content-Length'];
    }

    /**
     * gets the domDocument representation of the url
     */
    public function get_dom_document() {
        if ($this->cached_page == null) {
            $this->load_cached_page();
        }
        if ($this->dom_document == null) {
            $this->load_dom_document();
        }

        return $this->dom_document;
    }

    /**
     * loads the object with the contents of the url
     */
    private function load_cached_page() {
        $this->cached_page = file_get_contents($this->path);
    }

    /**
     * creates a dom document object of the url
     */
    private function load_dom_document() {
        $dom = new DOMDocument();
        $dom->loadHTML($this->cached_page);
        $this->dom_document = $dom;
    }
}