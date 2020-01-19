<?php

namespace App\Http\Controllers;

use App\Search;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public $client;

    /**
     * SearchController constructor.
     */
    public function __construct()
    {
        $this->client = Search::client();
    }

    public function createIndex()
    {
        $params = [
          'index' => 'lot',
          'body' => [
            'settings' => [
              'number_of_shards' => 1,
              'number_of_replicas' => 0,
              'analysis' => [
                'analyzer' => [
                  'my_custom_analyzer' => [
                    'type' => 'custom',
                    'tokenizer' => 'whitespace',
                    'filter' => ['lowercase', 'stop', 'kstem']
                  ]
                ]
              ]
            ],
            'mappings' => [
              'properties' => [
                'name_ru' => [
                  'type' => 'text',
                  'analyzer' => 'my_custom_analyzer',
                ],
                'description' => [
                  'type' => 'text',
                  'analyzer' => 'my_custom_analyzer',
                ]
              ]
            ]
          ]
        ];
        $this->client->indices()->create($params);

        return response()->json([
          'success' => true
        ]);
    }



    public function indexing()
    {
        Search::indexing();
    }

    public function get($id)
    {
        $client = Search::client();
        $params = [
          'index' => 'lot',
          'id'    => $id
        ];

        $response = $client->get($params);
        dd($response);
    }

    public function search()
    {
        $client = Search::client();
        /*
        $items = $client->search([
          'index' => 'lot',
          'body'  => [
            'query' => [
              'match' => [
                'name_ru' => 'Первый лот'
              ]
            ]
          ]
        ]);
        */

        $params = [
          'index' => 'lot',
          'body'  => [
            'query' => [
              'bool' => [
                'filter' => [
                  'term' => [ 'description' => 'moy' ]
                ],
              ]
            ]
          ]
        ];

        $items = $client->search($params);


        dd($items);
    }
}
