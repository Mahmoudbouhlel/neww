<?php

namespace Database\Factories;

use App\Models\DocumentTemplate;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentTemplateFactory extends Factory
{
    protected $model = DocumentTemplate::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word . ' Template',
            'service_id' => Service::factory(),
            'fields' => json_encode([
                ['label' => 'Nom', 'type' => 'text'],
                ['label' => 'Date de naissance', 'type' => 'date'],
                ['label' => 'Adresse', 'type' => 'text'],
            ]),
        ];
    }
}
