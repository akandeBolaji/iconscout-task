<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\{
    User,
    Icon,
    Tag,
    Color,
    Category
};

class SeedIconsToDB extends Command
{
    const SEED_URL = 'https://s3.wasabisys.com/iconscout-dev/dist/icons.json';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:icons_to_db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to seed icons from url';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $response = Http::get(self::SEED_URL);
        $data = $response->json();
        $count = count($data);

        echo "========= Seeding Start ============" . PHP_EOL;
        echo "Total $count icons to be imported" . PHP_EOL;
        $start_time = microtime(true);
        $total = $this->seed_icons($data);
        $end_time = microtime(true);
        echo "========= Seeding End ==============" . PHP_EOL;
        echo "Time elapsed: " . round($end_time-$start_time, 2) . ' seconds' . PHP_EOL;
        echo "Total " . $total . " icons were imported to DB" . PHP_EOL;
    }

    public function seed_icons($data)
    {
        $i = 0;
        foreach ($data as $arr) {
            $contributor = User::where('name', $arr["contributor"])
                            ->firstOrCreate(["name" => $arr["contributor"]]);

            $icon = Icon::create([
                "name" => $arr["name"],
                "img_url" => $arr["image"],
                "price" => $arr["price"],
                "style" => $arr["style"],
                "contributor_id" => $contributor->id,
            ]);

            $tags = array_keys($arr["tags"]);
            foreach ($tags as $t) {
                $tag = Tag::where('value', $t)
                        ->firstOrCreate(["value" => $t]);
                $icon->tags()->attach($tag);
            }

            $categories = array_values($arr["categories"]);
            foreach ($categories as $c) {
                $category = Category::where('value', $c)
                        ->firstOrCreate(["value" => $c]);
                $icon->categories()->attach($category);
            }

            $colors = array_keys(array_filter($arr["colors"], function($color) {
                return $color > 0;
            }));
            foreach ($colors as $c) {
                $color = Color::where('value', $c)
                        ->firstOrCreate(["value" => $c]);
                $icon->colors()->attach($color);
            }
            $i++;
            echo "$i icons imported to DB" . PHP_EOL;
        }
        return $i;
    }
}
