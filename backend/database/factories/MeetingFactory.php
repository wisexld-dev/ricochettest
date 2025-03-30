<?php

namespace Database\Factories;

use App\Models\Meeting;
use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MeetingFactory extends Factory
{
    protected $model = Meeting::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'scheduled_at' => $this->faker->dateTimeBetween('now', '+1 month'),
            'notes' => $this->faker->text(200),
            'user_id' => User::factory(),
        ];
    }
}
