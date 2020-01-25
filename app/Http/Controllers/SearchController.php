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

    /**
     * Create index
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createIndex()
    {
        $result = Search::createIndex();

        return response()->json([
            'success' => $result
        ]);
    }

    /**
     * Indexing records
     */
    public function indexing()
    {
        Search::indexing();
    }

    public function get($id)
    {
        $client = Search::client();
        $params = [
            'index' => 'lot',
            'id' => $id
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
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            //['match' => ['tender_name_ru' => 'кирпич']],
                            //['match' => ['tender_description' => 'кирпич']],
                        ],
                        'must_not' => [
                            //['match' => ['lot_description' => 'работев']],
                            //['match' => ['description' => 'zabor1']],
                        ],
                        'should' => [
                            //['match' => ['lot_description' => 'кирпичу']],
                            //['match' => ['description' => 'zabor']],
                        ],
                        'filter' => [
                            //['term' => ['description' => 'moy']],
                            //['range' => ['publish_date' => [ "gte" => "2015-01-01" ]]]
                        ],
                    ]
                ]
            ]
        ];

        $items = $client->search($params);


        dd($items);
    }
}
