<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Personnel;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Arr;

use PhpOffice\PhpSpreadsheet\IOFactory;

use Illuminate\Validation\ValidationException;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PersonnelImport;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class PersonnelController extends Controller
{
    public function index(Request $request)
    {
        // Load all personnel records (or a large batch) without filters
        $personnels = Personnel::query()->get();

        return Inertia::render('Admin/Personnel/Index', [
            'personnels' => [
                'data' => $personnels, // Simplified structure for client-side use
            ],
        ]);
    }



public function uploadXLS(Request $request)
{
    try {
        if (!$request->hasFile('file')) {
            return back()->with('error', 'No file uploaded.');
        }

        $file = $request->file('file');
        $request->validate(['file' => 'required|mimes:xlsx,xls']);

        // Clear existing data before import
        DB::table('personnels')->truncate();

        // Import new data
        Excel::import(new PersonnelImport, $file);

        return back()->with('success', 'Personnel data uploaded successfully!');
    } catch (ValidationException $e) {
        return back()->with('error', 'Validation Error: ' . implode(' | ', Arr::flatten($e->errors())));
    } catch (\Exception $e) {
        return back()->with('error', 'Error uploading file: ' . $e->getMessage());
    }
}

public function updatePhone(Request $request, $pers_no)
    {
        $request->validate([
            'phone' => 'nullable|string|max:15|regex:/^\+?[\d\s-]{7,15}$/',
        ]);

        $personnel = Personnel::findOrFail($pers_no);
        $personnel->update([
            'phone' => $request->input('phone'),
        ]);


    }

}
