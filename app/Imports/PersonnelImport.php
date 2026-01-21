<?php
namespace App\Imports;

use App\Models\Personnel;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PersonnelImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        Log::info('Processing row:', $row); // Log each row for debugging

        return new Personnel([
            'pers_no' => $row['persno'] ?? $row['pers.no.'] ?? null,
            'personnel_number' => $row['personnel_number'] ?? null,
            'phone' => $row['phone'] ?? null,
            'cost_ctr' => $row['cost_ctr'] ?? $row['cost center'] ?? null,
            'organizational_unit' => $row['organizational_unit'] ?? null,
            'object_abbr' => $row['object_abbr'] ?? null,
            'grp' => $row['grp'] ?? null,
            'tr_id_no' => $row['tr_id_no'] ?? null,
            'org_unit' => $row['orgunit'] ?? null,
            'employment_status' => $row['employment_status'] ?? null,
            'position_code' => $row['position_code'] ?? null,
            'position_name' => $row['position'] ?? null,
            'job' => $row['job'] ?? null,
            'ws_rule' => $row['ws_rule'] ?? $row['work schedule rule'] ?? null,
            'work_schedule_rule' => $row['work_schedule_rule'] ?? null,
            'personnel_area' => $row['personnel_area'] ?? null,
            'p_area' => $row['parea'] ?? null,
            'payroll_area' => $row['payroll_area'] ?? null,
            'subsy' => $row['subsy'] ?? null,
            'grouping_for_connection_to_sub' => $row['grouping_for_connection_to_sub'] ?? null,
            'pyadm' => $row['pyadm'] ?? null,
            'payroll_administrator' => $row['payroll_administrator'] ?? null,
            'peradm' => $row['peradm'] ?? null,
            'administrator_for_hr_master_da' => $row['administrator_for_hr_master_da'] ?? null,
            'tadmin' => $row['tadmin'] ?? null,
            'administrator_for_time_recording' => $row['administrator_for_time_recordi'] ?? null, // Check for truncated names
            'time_management_status' => $row['employee_time_management_statu'] ?? null,
            'prev_pers_no' => $row['prevpersno'] ?? $row['previous personnel number'] ?? null,
            'gender_key' => $row['gender_key'] ?? null,
            'employee_subgroup' => $row['employee_subgroup'] ?? null,
            'p_subarea' => $row['p_subarea'] ?? null,
            'personnel_subarea' => $row['personnel_subarea'] ?? null,
            'last_name' => $row['last_name'] ?? null,
            'first_name' => $row['first_name'] ?? null,
            'headcount_sap_sem' => isset($row['headcount_sap_sem']) && $row['headcount_sap_sem'] === '1',
            'date' => isset($row['date']) ? date('Y-m-d', strtotime($row['date'])) : null,
            'birth_date' => isset($row['birth_date']) ? date('Y-m-d', strtotime($row['birth_date'])) : null,
            'global_id' => $row['global_id'] ?? null,
            'manager_id' => $row['manager_id'] ?? null,
            'second_address_line' => $row['second_address_line'] ?? null,
            'address_record_type' => $row['address_record_type'] ?? null,
            'marital_status_key' => $row['marital_status_key'] ?? null,
            'city' => $row['city'] ?? null,
        ]);
    }
}
