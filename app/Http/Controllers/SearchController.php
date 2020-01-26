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
                "from" => 0,
                "size" => 10000,
                //"stored_fields" => [],
                'query' => [
                    'bool' => [
                        'must' => [
                            ['match' => ['tender_name_ru' => 'кирпич']],
                            ['match' => ['tender_name_ru' => 'пенодиатомитовый']],
                            //['match' => ['tender_description' => 'кирпич']],
                        ],
                        'must_not' => [
                            ['match' => ['lot_name_ru' => 'кп']],
                            //['match' => ['description' => 'zabor1']],
                        ],
                        'should' => [
                            //['match' => ['lot_description' => 'кирпичу']],
                            //['match' => ['tender_name_ru' => 'Болашак']],
                        ],
                        'filter' => [
                            //['term' => ['description' => 'moy']],
                            ['range' => ['open_date' => [ "gte" => "2018-12-03 12:37:00" ]]],
                            ['range' => ['open_date' => [ "lte" => "2018-12-05 14:10:00" ]]]
                        ],
                    ]
                ]
            ]
        ];

        $items = $client->search($params);


        dd($items);
    }
}
