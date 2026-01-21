<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->jobTitle,
            'processing_time' => $this->faker->numberBetween(5, 60),
            'service_id' => Service::factory(),
        ];
    }
}
