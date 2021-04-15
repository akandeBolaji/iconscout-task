<?php

namespace App\Console\Commands;

use App\Models\Icon;
use Elasticsearch\ClientBuilder;
use Illuminate\Console\Command;

class ImportToElasticSearch extends Command
{
    const ELASTIC_INDEX = "icons";
    const ELASTIC_TYPE  = "icon";

    protected $client;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:data_to_elasticsearch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports all data from database to Elasticsearch service';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // Initialize Elasticsearch client
        $this->client = ClientBuilder::create()->build();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // When importing, delete old data and insert new from database
        $reset = $this->resetIndex();

        if ($reset) {
            echo "========= Import Start ============" . PHP_EOL;
            $start_time = microtime(true);
            $total = $this->importIcons();
            $end_time = microtime(true);
            echo "========= Import End ==============" . PHP_EOL;
            echo "Time elapsed: " . round($end_time-$start_time, 2) . ' seconds' . PHP_EOL;
            echo "Total " . $total . " icons were imported to ElasticSearch" . PHP_EOL;
        } else {
            echo "Data is not imported";
        }
    }

    public function resetIndex()
    {
        $params = [
            'index' => Icon::ELASTIC_INDEX,
            'body' => [
                'settings' => [
                    'number_of_shards' => 3,
                    'number_of_replicas' => 2
                ],
                'mappings' => [
                    '_source' => [
                        'enabled' => true
                    ],
                    'properties' => [
                        'colors' => [
                            'type' => 'nested'
                        ],
                        'style' => [
                            'type' => 'keyword'
                        ]
                    ]
                ]
            ]
        ];

        // If index exists it will delete it (all data will be deleted) and create new one
        if ($this->client->indices()->exists(['index' => Icon::ELASTIC_INDEX])) {

            // Deleting index
            $response_delete = $this->client->indices()->delete(['index' => Icon::ELASTIC_INDEX]);

            if ($response_delete['acknowledged']) {
                echo "Index '" . Icon::ELASTIC_INDEX . "' successfully deleted" . PHP_EOL;

                // Creating new index
                $response_create = $this->client->indices()->create($params);

                if ($response_create['acknowledged']) {
                    echo "Index '" . Icon::ELASTIC_INDEX . "' successfully created" . PHP_EOL;
                    return true;
                }

                echo "Failed to create index" . PHP_EOL;
                die();
            }

            echo "Failed to delete index" . PHP_EOL;
            die();
        } else {
            // Creating new index
            $response_create = $this->client->indices()->create($params);

            if ($response_create['acknowledged']) {
                return true;
            }

            echo "Failed to create index" . PHP_EOL;
            die();
        }
    }

    private function importIcons()
    {
        $start = microtime(true);

        // Get all icon data from database
        $icons = Icon::with(['contributor', 'tags', 'colors', 'categories'])->get();

        $end = microtime(true);

        $i = 0;
        echo "-- Got data in " . round($end - $start, 2) . " seconds" . PHP_EOL;

        $start = microtime(true);
        foreach ($icons as $icon) {

            // Add index and type data to array
            $data['body'][] = [
                'index' => [
                    '_index'    => Icon::ELASTIC_INDEX,
                    '_id'       => $icon->id,
                ]
            ];

            // Icon data that will be required for later search
            $data['body'][] = [
                'id'                => $icon->id,
                'name'              => $icon->name,
                'contributor'       => $icon->contributor->name,
                'style'             => $icon->style,
                'price'             => $icon->price,
                'tags'              => implode(',', $icon->tags->pluck('value')->toArray()),
                'colors'            => $this->generate_nested_colors($icon->colors),
                'categories'        => implode(',', $icon->categories->pluck('value')->toArray()),
            ];

            $i++;
        }
        $end = microtime(true);
        echo "-- Filled array in " . round($end - $start, 2) . " seconds" . PHP_EOL;

        $start = microtime(true);

        // Execute Elasticsearch bulk command for indexing multiple data
        $response = $this->client->bulk($data);

        \Log::debug($response);

        $end = microtime(true);
        echo "-- Uploaded in " . round($end - $start, 2) . " seconds" . PHP_EOL;
        return $i;
    }

    private function generate_nested_colors($colors)
    {
        $result = [];
        $values = $colors->pluck('hsl_value')->toArray();
        foreach ($values as $value) {
            $value = explode(",", $value);
            $result[] = [
                "h" => $value[0],
                "s" => $value[1],
                "l" => $value[2]
            ];
        }
        return $result;
    }
}
