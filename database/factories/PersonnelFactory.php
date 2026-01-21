<?php

namespace Database\Factories;

use App\Models\Personnel;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonnelFactory extends Factory
{
    protected $model = Personnel::class;

    public function definition(): array
    {
        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;
        $gender = $this->faker->randomElement(['Male', 'Female']);
        $isMale = $gender === 'Male';

        return [
            'pers_no' => $this->faker->unique()->numberBetween(10000000, 10999999),
            'personnel_number' => $firstName . ' ' . $lastName,
            'personnel_area' => $this->faker->randomElement(['LEONI TN Manzel Hayet', 'LEONI TN Messaadine']),
            'organizational_unit' => $this->faker->randomElement(['TN M2', 'TN FM IT 02']),
            'position_name' => $this->faker->jobTitle,
            'job' => $this->faker->jobTitle,
            'work_schedule_rule' => $this->faker->randomElement(['2-S aftern. 8h', 'Adm_BRMT_TN01']),
            'employment_status' => 'Active',
            'gender_key' => $gender,
            'last_name' => $lastName,
            'first_name' => $firstName,
            'birth_date' => $this->faker->date('Y-m-d', '2000-01-01'),
            'city' => $this->faker->city,
            'marital_status_key' => $this->faker->randomElement(['Single', 'Marr.']),
            'address_record_type' => 'Permanent residence',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
