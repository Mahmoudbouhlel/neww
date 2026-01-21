<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class Personnel extends Model implements AuthenticatableContract
{
    use HasFactory;
    use Authenticatable;

    protected $fillable = [
        'pers_no',
        'personnel_number',
        'phone',
        'cost_ctr',
        'organizational_unit',
        'object_abbr',
        'grp',
        'tr_id_no',
        'org_unit',
        'employment_status',
        'position_code',
        'position_name',
        'job',
        'ws_rule',
        'work_schedule_rule',
        'personnel_area',
        'p_area',
        'payroll_area',
        'subsy',
        'grouping_for_connection_to_sub',
        'pyadm',
        'payroll_administrator',
        'peradm',
        'administrator_for_hr_master_da',
        'tadmin',
        'administrator_for_time_recording',
        'time_management_status',
        'prev_pers_no',
        'gender_key',
        'employee_subgroup',
        'p_subarea',
        'personnel_subarea',
        'last_name',
        'first_name',
        'headcount_sap_sem',
        'date',
        'birth_date',
        'global_id',
        'manager_id',
        'second_address_line',
        'address_record_type',
        'marital_status_key',
        'city',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'personnel_id');
    }

    // Define primary key
    protected $primaryKey = 'pers_no';

    // Disable auto-incrementing since `pers_no` is a string
    public $incrementing = false;

    // Define key type
    protected $keyType = 'string';

    // Enable timestamps to match migration
    public $timestamps = true;

    // Cast date fields to date type
    protected $casts = [
        'headcount_sap_sem' => 'boolean',
        'date' => 'date',
        'birth_date' => 'date',
    ];
}