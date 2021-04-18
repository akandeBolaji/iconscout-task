<?php

namespace App\Listeners;

use App\Events\UpdateIconEvent;
use App\Models\Icon;
use Elasticsearch\ClientBuilder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\ColorConversionService;

class UpdateIconListener
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
     * @param  UpdateIconEvent  $event
     * @return void
     */
    public function handle(UpdateIconEvent $event)
    {
        $this->update_icon_to_elastic_search($event->icon);
    }

    private function update_icon_to_elastic_search(Icon $icon)
    {
        // Fill array with icon data
        $data = [
            'body' => [
                'doc' => [
                    'id'           => $icon->id,
                    'name'         => $icon->name,
                    'contributor'  => $icon->contributor->name,
                    'style'        => $icon->style,
                    'price'        => $icon->price,
                    'tags'         => implode(',', $icon->tags->pluck('value')->toArray()),
                    'colors'       => (new ColorConversionService)->generate_nested_colors($icon->colors),
                    'categories'   => implode(',', $icon->categories->pluck('value')->toArray()),
                ]
            ],
            'index' => Icon::ELASTIC_INDEX,
            'id'    => $icon->id,
        ];

        // Send request to update icon
        $response = $this->client->update($data);

        return $response;
    }
}
