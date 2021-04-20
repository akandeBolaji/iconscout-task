<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{
    User,
    Team,
    TeamAdmin,
    TeamMember,
    Icon,
    Tag,
    Category,
    Color,
    Style
};
use Illuminate\Database\Eloquent\Factories\Sequence;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $team_admin = TeamAdmin::factory()
                                ->for(User::factory()->state([
                                    'name' => 'John Doe',
                                    'email' => 'johndoe@iconscout.com',
                                    'type'  => 'team-admin'
                                ]));

        $team_member = TeamMember::factory()
                                ->for(User::factory()->state([
                                    'name' => 'James Doe',
                                    'email' => 'jamesdoe@iconscout.com',
                                    'type'  => 'team-member'
                                ]));

        Team::factory()
                ->has($team_admin, 'admins')
                ->has($team_member, 'members')
                ->create([
                    'name' => 'Iconscout team'
                ]);

        User::factory()
            ->create([
                'name' => 'Paul Doe',
                'email' => 'pauldoe@iconscout.com',
                'type'  => 'end-user'
            ]);

         Icon::factory()
            ->has(Category::factory()
                ->count(3)
                ->state(new Sequence(
                    ['value' => 'Flight'],
                    ['value' => 'Transport'],
                    ['value' => 'Luxury'],
                ))
            )
            ->has(Tag::factory()
                ->count(3)
                ->state(new Sequence(
                    ['value' => 'Aeroplane'],
                    ['value' => 'Pilot'],
                    ['value' => 'Gas'],
                ))
            )
            ->has(Color::factory()
                ->count(2)
                ->state(new Sequence(
                    [
                        'hex_value' => '85C88D',
                        'hue' => 127,
                        'saturation' => 37,
                        'lightness' => 65
                    ],
                    [
                        'hex_value' => 'FFFFFF',
                        'hue' => 0,
                        'saturation' => 0,
                        'lightness' => 100
                    ]
                ))
            )
            ->create([
                'name' => "Airplane",
                'img_url' => "https://iconscout.com",
                'style_id' => Style::factory()->create(['value' => 'flat']),
                'price' => 5.99,
                'contributor_id' => $team_member->make()->user->id
            ]);
    }
}
