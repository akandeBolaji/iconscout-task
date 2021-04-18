<?php

namespace Database\Factories;

use App\Models\TeamAdmin;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamAdminFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TeamAdmin::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => 1,
            'team_id' => 1
        ];
    }
}
