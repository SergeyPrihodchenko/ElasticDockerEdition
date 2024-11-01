<?php

use Elastic\Elasticsearch\ClientBuilder;

require_once __DIR__ . '/vendor/autoload.php';

$pdo = new \PDO('mysql:host=172.17.0.1:20002;dbname=DB', 'dev', '123');

$query = <<<SQL
    SELECT pagetitle, longtitle, uri FROM modx_site_content;
SQL;

$stmt = $pdo->prepare($query);

$stmt->execute();

$result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

$client = ClientBuilder::create()
   ->setHosts(['127.0.0.1:9200'])
   ->setApiKey('RlV4OHVKSUJhZWpxWWlxemxBTjM6c2lVclZsLVJTVjYteHVSUVZBMEZ5QQ==')
   ->build();

// $params = [
//     'index' => 'my_index',
//     "mappings" => [
//         "properties" => [
//             "title" => [
//                 "type" => "text"
//             ],
//             "element_id" => [
//                 "type" => "integer"
//             ],
//             "table_name" => [
//                 "type" => "text"
//             ]
//         ]
//     ]
// ];

foreach ($result as $key => $value) {

    $params = [
        'index' => 'contentsite',
        "body" => [
            "pagetitle" => $value['pagetitle'],
            "longtitle" => $value['longtitle'],
            "uri" => $value['uri']
        ]
    ];

    $response = $client->index($params);

}

