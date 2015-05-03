<?php

require_once __DIR__ . '/../bootstrap.php';

$search = service('search');

$elastica = $search->client();

$index = $elastica->getIndex('bm');

$index->delete();

$index->create();

$type = $index->getType('marks');

$mapping = new \Elastica\Type\Mapping();
$mapping->setType($type);

$es = json_decode(file_get_contents(root_dir . '/config/elasticsearch-marks.json'), true);

$mapping->setProperties($es['marks']['properties']);

$mapping->send();
