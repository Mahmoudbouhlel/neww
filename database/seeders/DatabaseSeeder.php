<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Service;
use App\Models\Task;
use App\Models\DocumentTemplate;
use App\Models\DocumentRequest;
use App\Models\Appointment;
use App\Models\Personnel;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Créer des personnels factices
        $this->command->info('👥 Génération de 50 personnels...');
        Personnel::factory(50)->create();

        // 2. Créer le super administrateur
        $this->command->info('🛡️ Création du super administrateur...');
        User::factory()->superAdmin()->create([
            'password' => bcrypt('password'),
            'service_id' => null,
        ]);

        // 3. Créer 5 services avec toutes les données associées
        $this->command->info('🏢 Création des services, agents, tâches, documents, et rendez-vous...');
        Service::factory(5)->create()->each(function ($service) {

            // a. Tâches
            $tasks = Task::factory(3)->create([
                'service_id' => $service->id,
            ]);

            // b. Template
            $template = DocumentTemplate::factory()->create([
                'service_id' => $service->id,
            ]);

            // c. Agents
            $agents = User::factory(2)->create([
                'role' => 'agent',
                'password' => bcrypt('password'),
                'service_id' => $service->id,
            ]);

            // d. Par agent : demandes + rendez-vous
           /*  foreach ($agents as $agent) {

                // Demandes
                DocumentRequest::factory(2)->create([
                    'user_id' => $agent->id,
                    'agent_id' => $agent->id,
                    'service_id' => $service->id,
                    'document_template_id' => $template->id,
                ]);

                // Rendez-vous associés à des personnels existants
                for ($i = 0; $i < 2; $i++) {
                    $personnelId = Personnel::inRandomOrder()->value('pers_no');
                    if ($personnelId) {
                        Appointment::factory()->create([
                            'personnel_id' => $personnelId,
                            'service_id' => $service->id,
                            'task_id' => $tasks->random()->id,
                        ]);
                    }
                }
            } */
        });

        // 4. Générer des rendez-vous aléatoires pour d'autres personnels (en plus des agents)
        $this->command->info('📅 Génération de rendez-vous aléatoires pour 20 personnels...');
        $services = Service::all();
        $tasks = Task::all();

        foreach (Personnel::inRandomOrder()->take(20) as $personnel) {
            Appointment::factory()->create([
                'personnel_id' => $personnel->pers_no,
                'service_id' => $services->random()->id,
                'task_id' => $tasks->random()->id,
            ]);
        }

        $this->command->info('✅ Base de données générée avec succès !');
    }
}
