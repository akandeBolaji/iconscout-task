<?php

namespace App\Listeners;

use App\Events\DeleteIconEvent;
use App\Models\Icon;
use Elasticsearch\ClientBuilder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DeleteIconListener
{
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
     * @param  DeleteIconEvent  $event
     * @return void
     */
    public function handle(DeleteIconEvent $event)
    {
        $this->delete_icon_from_elastic_search($event->icon);
    }

    private function delete_icon_from_elastic_search(Icon $icon)
    {
        // Fill array with icon data
        $data = [
            'index' => Icon::ELASTIC_INDEX,
            'id'    => $icon->id,
        ];

        // Send request to update icon
        $response = $this->client->delete($data);

        return $response;
    }
}
