<?php

namespace App;

use Elasticsearch\ClientBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Search
{
    public static $client;

    /**
     * Search constructor.
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

    public static function indexing()
    {
        $client = self::client();

        Lot::chunk(100, function ($lots) use ($client) {
            foreach ($lots as $lot) {
                $client->index([
                  'index' => 'lot',
                  'id' => $lot->id,
                  'body' => $lot->only(['name_ru', 'description'])
                ]);
            }
        });
    }
}
