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

class MovieController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()->build();
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
}
