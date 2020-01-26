<?php

namespace App;

use Elasticsearch\ClientBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Search
{
    public static $client;

    /**
     * ElasticSearch client
     */
    public static function client()
    {
        if (is_null(self::$client)) {
            $logger = new Logger('myElastic');
            $logger->pushHandler(new StreamHandler(storage_path('logs/elastic.log'), Logger::WARNING));
            self::$client = ClientBuilder::create()
                ->setLogger($logger)
                ->build();
        }
        return self::$client;
    }

    public static function createIndex()
    {
        $client = self::client();

        $params = [
            "index" => "lot",
            "body" => [
                "settings" => [
                    "number_of_shards" => 1,
                    "number_of_replicas" => 0,
                    "analysis" => [
                        "filter" => [
                            "russian_stop" => [
                                "type" => "stop",
                                "stopwords" => "_russian_"
                            ],
                            "russian_keywords" => [
                                "type" => "keyword_marker",
                                "keywords" => ["пример"]
                            ],
                            "russian_stemmer" => [
                                "type" => "stemmer",
                                "language" => "russian"
                            ]
                        ],
                        "char_filter" => [
                            "my_ru" => [
                                "type" => "mapping",
                                "mappings" => ["Ё=>Е", "ё=>е"]
                            ],
                        ],
                        "analyzer" => [
                            "my_custom_analyzer" => [
                                "type" => "custom",
                                "tokenizer" => "standard",
                                "filter" => ["lowercase", "russian_stop", "russian_stemmer"],
                                "char_filter" => ["html_strip", "my_ru"],
                            ]
                        ]
                    ]
                ],
                "mappings" => [
                    "properties" => [
                        "tender_name_ru" => [
                            "type" => "text",
                            "analyzer" => "my_custom_analyzer",
                        ],
                        "tender_description" => [
                            "type" => "text",
                            "analyzer" => "my_custom_analyzer",
                        ],
                        "lot_name_ru" => [
                            "type" => "text",
                            "analyzer" => "my_custom_analyzer",
                        ],
                        "lot_description" => [
                            "type" => "text",
                            "analyzer" => "my_custom_analyzer",
                        ],
                        "open_date" => [
                            'type' => 'date',
                            'format' => 'yyyy-MM-dd HH:mm:ss',
                        ],
                        "close_date" => [
                            'type' => 'date',
                            'format' => 'yyyy-MM-dd HH:mm:ss',
                        ],
                    ]
                ]
            ]
        ];

        $client->indices()->create($params);

        return true;
    }

    public static function indexing()
    {
        $client = self::client();

        Lot::with('tender')->chunk(500, function ($lots) use ($client) {
            $params = ['body' => []];
            foreach ($lots as $lot) {
                $params['body'][] = [
                    'index' => [
                        '_index' => 'lot',
                        '_id'    => $lot->id,
                    ]
                ];

                $params['body'][] = [
                    'tender_name_ru' => optional($lot->tender)->name_ru,
                    'tender_description' => optional($lot->tender)->description,
                    'lot_name_ru' => $lot->name_ru,
                    'lot_description' => $lot->description,
                 ];

            }
            $responses = $client->bulk($params);
            unset($responses);
        });
    }
}
