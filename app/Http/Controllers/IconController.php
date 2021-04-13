<?php

namespace App\Http\Controllers;

use Elasticsearch\ClientBuilder;
use Illuminate\Http\Request;
use App\Models\{
    User,
    Icon,
    Tag,
    Color,
    Category
};
use App\Events\{
    NewIconEvent,
    UpdateIconEvent,
    DeleteIconEvent
};
use Illuminate\Support\Facades\DB;

class IconController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()->build();
    }

    public function search(Request $request)
    {

        // Search for given text and return data
        $data = $this->search_icons($request->all());
        $iconArrayIds = [];

        // If there are any icons that match given search text "hits" fill their id's in array
        if($data['hits']['total'] > 0) {

            foreach ($data['hits']['hits'] as $hit) {
                $iconArrayIds[] = $hit['_source']['id'];
            }
        }

        // Retrieve found icons from database
        $icons = Icon::with('tags', 'categories', 'colors')
                        ->whereIn('id', $iconArrayIds)
                        ->get();

        \Log::debug($data);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'img_url' => 'required',
            'price' => 'required',
            'style' => 'required',
            'contributor' => 'required',
            'tags' => 'required|array',
            'categories' => 'required|array',
            'colors' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            $contributor = User::where('name', $validatedData["contributor"])
                            ->orWhere('id', $validatedData["contributor"] )
                            ->firstOrCreate(["name" => $validatedData["contributor"]]);

            $icon = Icon::create([
                "name" => $validatedData["name"],
                "img_url" => $validatedData["image"],
                "price" => $validatedData["price"],
                "style" => $validatedData["style"],
                "contributor_id" => $contributor->id,
            ]);

            $tags = array_values($validatedData["tags"]);
            foreach ($tags as $t) {
                $tag = Tag::where('value', $t)
                        ->firstOrCreate(["value" => $t]);
                $icon->tags()->attach($tag);
            }

            $categories = array_values($validatedData["categories"]);
            foreach ($categories as $c) {
                $category = Category::where('value', $c)
                        ->firstOrCreate(["value" => $c]);
                $icon->categories()->attach($category);
            }

            $colors = array_values($validatedData["colors"]);
            foreach ($colors as $c) {
                $color = Color::where('value', $c)
                        ->firstOrCreate(["value" => $c]);
                $icon->colors()->attach($color);
            }
            DB::commit();

            // Trigger an event to index new icon in Elasticsearch
            NewIconEvent::dispatch($icon);

            return  response()->json(["message" => "success"], 200);
        } catch(\Exception $e) {
            DB::rollBack();
            return response()->json(["message" => "error"], 500);
        }


    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'img_url' => 'required',
            'price' => 'required',
            'style' => 'required',
            'contributor' => 'required',
            'tags' => 'required|array',
            'categories' => 'required|array',
            'colors' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            $contributor = User::where('name', $validatedData["contributor"])
                            ->orWhere('id', $validatedData["contributor"] )
                            ->firstOrCreate(["name" => $validatedData["contributor"]]);

            $icon = Icon::find($id)
                    ->update([
                        "name" => $validatedData["name"],
                        "img_url" => $validatedData["image"],
                        "price" => $validatedData["price"],
                        "style" => $validatedData["style"],
                        "contributor_id" => $contributor->id,
                    ]);

            //temp implementation
            $icon->tags()->detach();
            $icon->colors()->detach();
            $icon->categories()->detach();

            $tags = array_values($validatedData["tags"]);
            foreach ($tags as $t) {
                $tag = Tag::where('value', $t)
                        ->firstOrCreate(["value" => $t]);
                $icon->tags()->attach($tag);
            }

            $categories = array_values($validatedData["categories"]);
            foreach ($categories as $c) {
                $category = Category::where('value', $c)
                        ->firstOrCreate(["value" => $c]);
                $icon->categories()->attach($category);
            }

            $colors = array_values($validatedData["colors"]);
            foreach ($colors as $c) {
                $color = Color::where('value', $c)
                        ->firstOrCreate(["value" => $c]);
                $icon->colors()->attach($color);
            }

            DB::commit();

            // Trigger an event to update icon in Elasticsearch
            UpdateIconEvent::dispatch($icon);

            return response()->json(["message" => "success"], 200);
        } catch(\Exception $e) {
            DB::rollBack();
            return response()->json(["message" => "error"], 500);
        }
    }

    public function delete(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $icon = Icon::find($id);

            $icon->tags()->detach();
            $icon->colors()->detach();
            $icon->categories()->detach();
            $icon->delete();

            DB::commit();

            // Trigger an event to update icon in Elasticsearch
            DeleteIconEvent::dispatch($icon);

            return response()->json(["message" => "success"], 200);
        } catch(\Exception $e) {
            DB::rollBack();
            return response()->json(["message" => "error"], 500);
        }
    }

    private function search_icons($input)
    {
        $params = [
            'index' => Icon::ELASTIC_INDEX,
            'body' => [
                'sort' => [
                    '_score'
                ],
                'query' => [
                    'bool' => [
                        'must' => [
                            ['match' => [
                                "name" => [
                                    'query'  => $input['query'],
                                    // 'fuzziness' => '1'
                                ]
                            ]],
                            ['match' => [
                                "tags" => [
                                    'query'  => $input['query'],
                                    // 'fuzziness' => '1'
                                ]
                            ]]
                        ],
                        'filter' =>  $this->generate_search_filter($input),
                    ],
                ],
            ]
        ];

        \Log::debug($params);

        $data = $this->client->search($params);
        return $data;
    }

    private function generate_search_filter($input)
    {
        $query_array = [];

        foreach ($input as $key => $value) {
            if ($key == 'query') {
                continue;
            }
            switch ($key) {
                case "price":
                    if ($value == 'free') {
                        $query_array[] = ['range' =>
                            [
                                'price' => [
                                    'lte' => '0.00'
                                ]
                            ]
                        ];
                    }
                    elseif ($value == 'premium') {
                        $query_array[] = ['range' =>
                            [
                                'price' => [
                                    'gt' => '0.00'
                                ]
                            ]
                        ];
                    }
                    else {
                        $value = number_format((float)$value, 2, '.', '');
                        $query_array[] = ['term' => [
                            "price" => $value
                        ]];
                    }
                    break;
                case "color":

                    break;
                default:
                    $query_array[] = ['term' => [
                        "$key" => $value
                    ]];
            }



        }
        return $query_array;
    }
}
