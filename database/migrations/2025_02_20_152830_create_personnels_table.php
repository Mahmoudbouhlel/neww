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
        Schema::create('personnels', function (Blueprint $table) {
            $table->id();
            $table->string('pers_no')->nullable()->comment('Personnel Number'); // Changed to nullable
            $table->string('personnel_number')->nullable();
            $table->string('phone')->nullable();
            $table->string('cost_ctr')->nullable()->comment('Cost Center');
            $table->string('organizational_unit')->nullable();
            $table->string('object_abbr')->nullable();
            $table->string('grp')->nullable();
            $table->string('tr_id_no')->nullable();
            $table->string('org_unit')->nullable();
            $table->string('employment_status')->nullable();
            $table->string('position_code')->nullable();
            $table->string('position_name')->nullable(); // Fixing duplicate "Position"
            $table->string('job')->nullable();
            $table->string('ws_rule')->nullable()->comment('Work Schedule Rule');
            $table->string('work_schedule_rule')->nullable();
            $table->string('personnel_area')->nullable();
            $table->string('p_area')->nullable();
            $table->string('payroll_area')->nullable();
            $table->string('subsy')->nullable();
            $table->string('grouping_for_connection_to_sub')->nullable();
            $table->string('pyadm')->nullable()->comment('Payroll Administrator');
            $table->string('peradm')->nullable()->comment('Administrator for HR Master Data');
            $table->string('tadmin')->nullable()->comment('Administrator for Time Recording');
            $table->string('time_management_status')->nullable();
            $table->string('prev_pers_no')->nullable()->comment('Previous Personnel Number');
            $table->string('gender_key')->nullable();
            $table->string('employee_subgroup')->nullable();
            $table->string('p_subarea')->nullable()->comment('Personnel Subarea');
            $table->string('personnel_subarea')->nullable();
            $table->string('payroll_administrator')->nullable();
            $table->string('administrator_for_hr_master_da')->nullable();
            $table->string('administrator_for_time_recording')->nullable();
            $table->string('last_name')->nullable();
            $table->string('first_name')->nullable();
            $table->boolean('headcount_sap_sem')->nullable()->default(false);
            $table->date('date')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('global_id')->nullable();
            $table->string('manager_id')->nullable();
            $table->string('manager_id_2')->nullable(); // In case of duplicate manager_id
            $table->string('manager_id_3')->nullable();
            $table->string('second_address_line')->nullable();
            $table->string('address_record_type')->nullable();
            $table->string('marital_status_key')->nullable();
            $table->string('city')->nullable();
            $table->string('otp_code')->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personnels');
    }
};
