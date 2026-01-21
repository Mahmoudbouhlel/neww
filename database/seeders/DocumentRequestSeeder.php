<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentRequest;
use App\Models\DocumentTemplate;
use App\Models\Personnel;

class DocumentRequestSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('📄 Génération de 50 demandes de documents (via personnels)...');

        if (
            Personnel::count() === 0 ||
            DocumentTemplate::count() === 0
        ) {
            $this->command->error('❌ Données manquantes : personnels ou templates.');
            return;
        }

        DocumentRequest::factory(50)->create();

        $this->command->info('✅ 50 demandes créées avec succès pour des personnels existants.');
    }
}
