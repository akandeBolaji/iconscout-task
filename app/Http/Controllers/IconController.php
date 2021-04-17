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
use App\Services\ColorConversionService;
use Auth;

class IconController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()->build();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->type != 'team-member') {
            return Redirect::to('admin');
        }
        return view('admin.icon', ['icons' => $user->icons]);
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
            'tags' => 'required|array',
            'categories' => 'required|array',
            'colors' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            $contributor = Auth::user();

            $icon = Icon::create([
                "name" => $validatedData["name"],
                "img_url" => $validatedData["img_url"],
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
                $c =  substr($c, 1);
                $color = Color::where('hex_value', $c)
                        ->firstOrCreate(
                            ['hex_value' => $c],
                            ['hsl_value' => implode(",", ColorConversionService::hexToHsl($c))]
                        );
                $icon->colors()->attach($color);
            }
            DB::commit();

            // Trigger an event to index new icon in Elasticsearch
            NewIconEvent::dispatch($icon);

            return response()->json(["success" => true], 200);
        } catch(\Exception $e) {
            DB::rollBack();
            return response()->json(["success" => false], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'img_url' => 'required',
            'price' => 'required',
            'style' => 'required',
            'tags' => 'required|array',
            'categories' => 'required|array',
            'colors' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            $contributor = Auth::user();
            $icon = Icon::find($id);

            $icon->update([
                "name" => $validatedData["name"],
                "img_url" => $validatedData["img_url"],
                "price" => $validatedData["price"],
                "style" => $validatedData["style"],
                "contributor_id" => $contributor->id,
            ]);

            //temporary implementation
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
                $c =  substr($c, 1);
                $color = Color::where('hex_value', $c)
                        ->firstOrCreate(
                            ['hex_value' => $c],
                            ['hsl_value' => implode(",", ColorConversionService::hexToHsl($c))]
                        );
                $icon->colors()->attach($color);
            }

            DB::commit();

            // Trigger an event to update icon in Elasticsearch
            UpdateIconEvent::dispatch($icon);

            return response()->json(["success" => true], 200);
        } catch(\Exception $e) {
            \Log::debug($e);
            DB::rollBack();
            return response()->json(["success" => false], 500);
        }
    }

    public function destroy(Request $request, $id)
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
                "aggs" => [
                    "style-agg" => [
                      "terms" => [
                        "field" => "style"
                      ]
                    ]
                ]
            ]
        ];

        \Log::debug($params);

        $data = $this->client->search($params);
        return $data;
    }

    private function generate_color_filter($color)
    {
        if (!$color) {
            return;
        }
        $value = explode(",", $color);

        return [
            'path'  => "colors",
            'query' => [
                'bool' => [
                    'filter' =>  [
                        [
                            'range' =>
                            [
                                'colors.h' => [
                                    'gte' => ceil($value[0] - $value[0] * 0.1),
                                    'lte' => ceil($value[0] + $value[0] * 0.1)
                                ]
                            ]
                        ],
                        [
                            'range' =>
                            [
                                'colors.s' => [
                                    'gte' => ceil($value[1] - $value[1] * 0.1),
                                    'lte' => ceil($value[1] + $value[1] * 0.1)
                                ]
                            ]
                        ],
                        [
                            'range' =>
                            [
                                'colors.l' => [
                                    'gte' => ceil($value[2] - $value[2] * 0.1),
                                    'lte' => ceil($value[2] + $value[2] * 0.1)
                                ]
                            ]
                        ]
                    ],
                ],
            ]
        ];
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
                    $value = (new ColorConversionService)->get_hex_color($value, $input['color_type']);
                    \Log::debug($value);
                    $query_array[] = [
                        'nested' => [
                            'path' => 'colors',
                            'query' => [
                                'bool' => [
                                    'must' =>  [
                                        [
                                            'range' =>
                                            [
                                                'colors.h' => [
                                                    'gte' => ceil($value[0] - $value[0] * 0.1),
                                                    'lte' => ceil($value[0] + $value[0] * 0.1)
                                                ]
                                            ]
                                        ],
                                        [
                                            'range' =>
                                            [
                                                'colors.s' => [
                                                    'gte' => ceil($value[1] - $value[1] * 0.1),
                                                    'lte' => ceil($value[1] + $value[1] * 0.1)
                                                ]
                                            ]
                                        ],
                                        [
                                            'range' =>
                                            [
                                                'colors.l' => [
                                                    'gte' => ceil($value[2] - $value[2] * 0.1),
                                                    'lte' => ceil($value[2] + $value[2] * 0.1)
                                                ]
                                            ]
                                        ]
                                    ],
                                ],
                            ],
                            'ignore_unmapped' => true
                        ],

                    ];
                    break;
                default:
                    $query_array[] = ['term' => [
                        "$key" => $value
                    ]];
            }



        }
        return $query_array;
    }

    private function get_hex_color($color, $color_code = 'hsl')
    {
        switch ($key)
        {

        }
    }
}
