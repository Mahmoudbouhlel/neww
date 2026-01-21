<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Personnel;
use App\Models\Service;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        $requestedAt = $this->faker->dateTimeBetween('-5 days', 'now');

        return [
            'personnel_id' => Personnel::inRandomOrder()->value('pers_no'),
            'service_id' => Service::inRandomOrder()->value('id'),
            'task_id' => Task::inRandomOrder()->value('id'),
            'status' => $this->faker->randomElement(['pending', 'in_progress', 'completed', 'canceled']),
            'requested_at' => $requestedAt,
            'processed_at' => $this->faker->boolean(70) ? $this->faker->dateTimeBetween($requestedAt, 'now') : null,
            'completed_at' => $this->faker->boolean(40) ? $this->faker->dateTimeBetween($requestedAt, 'now') : null,
        ];
    }
}
