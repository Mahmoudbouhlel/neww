<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\Personnel;
use App\Models\Service;
use App\Models\Task;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('📅 Génération de 50 rendez-vous aléatoires...');

        $services = Service::all();
        $tasks = Task::all();

        if (Personnel::count() === 0 || $services->isEmpty() || $tasks->isEmpty()) {
            $this->command->error('❌ Données manquantes : personnels, services ou tâches.');
            return;
        }

        foreach (range(1, 100) as $i) {
            $personnelId = Personnel::inRandomOrder()->value('pers_no');
            if ($personnelId) {
                Appointment::factory()->create([
                    'personnel_id' => $personnelId,
                    'service_id' => $services->random()->id,
                    'task_id' => $tasks->random()->id,
                ]);
            }
        }

        $this->command->info('✅ 100 rendez-vous créés avec succès.');
    }
}
