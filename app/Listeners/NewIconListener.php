<?php

namespace App\Listeners;

use App\Events\NewIconEvent;
use App\Models\Icon;
use Elasticsearch\ClientBuilder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\ColorConversionService;

class NewIconListener
{
    protected $client;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->client = ClientBuilder::create()
                        ->setHosts([getenv('ELASTIC_SEARCH_HOST')])
                        ->build();
    }

    /**
     * Handle the event.
     *
     * @param  NewIconEvent  $event
     * @return void
     */
    public function handle(NewIconEvent $event)
    {
        $this->add_icon_to_elastic_search($event->icon);
    }

    private function add_icon_to_elastic_search(Icon $icon)
    {
        // Fill array with icon data
        $data = [
            'body' => [
                'id'           => $icon->id,
                'name'         => $icon->name,
                'contributor'  => $icon->contributor->name,
                'style'        => $icon->style->name,
                'price'        => $icon->price,
                'tags'         => implode(',', $icon->tags->pluck('value')->toArray()),
                'colors'       => (new ColorConversionService)->generate_nested_colors($icon->colors),
                'categories'   => implode(',', $icon->categories->pluck('value')->toArray()),
            ],
            'index' => Icon::ELASTIC_INDEX,
            'id'    => $icon->id,
        ];

        // Send request to index new icon
        $response = $this->client->index($data);

        return $response;
    }
}
