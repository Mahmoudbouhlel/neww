<?php

namespace Database\Factories;

use App\Models\DocumentRequest;
use App\Models\DocumentTemplate;
use App\Models\Personnel;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentRequestFactory extends Factory
{
    protected $model = DocumentRequest::class;

    public function definition(): array
    {
        $template = DocumentTemplate::inRandomOrder()->first() ?? DocumentTemplate::factory()->create();
        $personnel = Personnel::inRandomOrder()->first() ?? Personnel::factory()->create();

        return [
        'user_id' => Personnel::inRandomOrder()->value('pers_no'),
            'document_template_id' => $template->id,
            'service_id' => $template->service_id,
            'data' => [
                'Nom' => $this->faker->name,
                'Date de naissance' => $this->faker->date(),
                'Adresse' => $this->faker->address,
                'comment' => $this->faker->sentence,
            ],
            'status' => 'pending',
            'agent_id' => null,
            'agent_comment' => null,
        ];
    }
}
