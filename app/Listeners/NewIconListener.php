<?php

namespace App\Listeners;

use App\Events\NewIconEvent;
use App\Models\Icon;
use Elasticsearch\ClientBuilder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
        $this->client = ClientBuilder::create()->build();
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
                'style'        => $icon->style,
                'price'        => $icon->price,
                'tags'         => implode(',', $icon->tags->pluck('value')->toArray()),
                'colors'       => implode(',', $icon->colors->pluck('value')->toArray()),
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
