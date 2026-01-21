<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('document_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id'); // Demandeur
            $table->foreignId('document_template_id'); // Modèle du document
            $table->foreignId('service_id')->constrained(); // Service concerné
            $table->json('data'); // Données saisies par l'utilisateur
            $table->enum('status', ['pending', 'processing', 'completed', 'rejected'])->default('pending');
            $table->foreignId('agent_id')->nullable(); // Agent qui traite
            $table->text('agent_comment')->nullable(); // Commentaire de l'agent
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_requests');
    }
};
