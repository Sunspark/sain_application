<?php

function die_bad_usage($error_string) {
    if ($error_string != null) {
        print $error_string . " \n";
    }
    print "Usage: main.php <path_to_http_resource> \n";
    die();
}

// check for command-line arguments
if (! isset($argv[1])) {
    die_bad_usage('No arguments detected');
}

spl_autoload_register(function ($class) {
    include 'src/' . $class . '.php';
});

//set up results array
$results_array = array();
$sum_total = 0;

try {
    // load the starting url into a parsing object
    $target_url = new Url($argv[1]);

    // get the appropriate element to loop
    $dom = $target_url->get_dom_document();
    $xpath = new DOMXPath($dom);
    $xpath_query = "//div[@id='productLister']/ul[@class='productLister listView']/li";
    $results_elements = $xpath->query($xpath_query);

    // iterate the loop into result objects
    foreach ($results_elements as $results_element) {
        // create a result object
        $result_object = new FruitResultElement($results_element);

        // add the populated object to the result array
        $json_object = json_encode($result_object);
        $results_array[] = $json_object;

        // add the value to the sum value
        $sum_total = $sum_total + $result_object->get_unit_price();
    }

    // create result object for conversion to JSON
    $output_object = new stdClass();
    $output_object->results = $results_array;
    $output_object->total = $sum_total;

    // print JSON
    print json_encode($output_object);

} catch (Exception $e) {
    print $e->getMessage() . "\n";
}
