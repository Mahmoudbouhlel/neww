<?php

use App\Http\Controllers\Admin\AdminappointmentController;
use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\DocumentRequestController;
use App\Http\Controllers\Admin\PersonnelController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Agent\AgentDocumentRequestController;
use App\Http\Controllers\Agent\AppointmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// routes/web.php

Route::middleware('auth')->get('/auth/user', function () {
    return auth()->user();
})->name('auth.user');




Route::post('/user/otp/request', [AuthController::class, 'requestOtp'])->name('user.otp.request');
Route::post('/user/otp/verify', [AuthController::class, 'verifyOtp'])->name('user.otp.verify');

Route::get('/admin/updates', [DashboardController::class, 'getDashboardUpdates'])->name('admin.updates');
Route::get('/appointments/available-times', [AppointmentController::class, 'getAvailableTimes'])->name('user.appointments.available_times');

Route::post('/user/login', [AuthenticatedSessionController::class, 'userLogin'])->name('user.login');

// User Dashboard Route with Personnel Authentication
Route::middleware(['auth:personnel'])->group(function () {
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::post('/user/appointments/store', [UserDashboardController::class, 'storeAppointment'])->name('user.appointments.store');
    Route::post('/user/document-requests/store', [UserDashboardController::class, 'storeDocumentRequest'])->name('user.document_requests.store');

});


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Common dashboard route for authenticated users
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Grouped routes with authentication middleware
Route::middleware('auth')->group(function () {

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');










    // Super Admin Routes

   Route::middleware(['auth', 'role:super_admin'])->group(function () {
       Route::get('/admin', [DashboardController::class, 'admin'])->name('admin.dashboard');
       Route::resource('users', UserController::class);
       Route::resource('services', ServiceController::class);
       Route::resource('tasks', TaskController::class);
       Route::resource('agents', AgentController::class);
       Route::resource('personnels', PersonnelController::class); // Added Personnel Management Route
       Route::post('/admin/personnels/upload', [PersonnelController::class, 'uploadXLS'])->name('personnels.upload');
       Route::resource('appointments', AdminappointmentController::class);
       Route::put('/admin/personnels/{pers_no}/phone', [PersonnelController::class, 'updatePhone'])->name('personnels.updatePhone');
       Route::resource('documents', \App\Http\Controllers\Admin\DocumentTemplateController::class);

       Route::get('/admin/document-requests', [DocumentRequestController::class, 'index'])->name('document_requests.index');
       Route::put('/admin/document-requests/{id}', [DocumentRequestController::class, 'updateStatus'])->name('document_requests.update');




   });

   /**
    * Agent Routes
    */
   Route::middleware(['auth', 'role:agent'])->group(function () {
       Route::get('/agent', [DashboardController::class, 'agent'])->name('agent.dashboard');
       Route::get('/Agent/appointments', [AppointmentController::class, 'index'])->name('Agent.appointments.index');
/*      Route::put('/Agent//appointments/{id}', [AppointmentController::class, 'update'])->name('Agent.appointments.update');
 */     Route::resource('Agentappointments', AppointmentController::class);

        Route::resource('agent/document-requests', AgentDocumentRequestController::class)
        ->names([
          'index' => 'agent.document-requests.index',
             'update' => 'agent.document-requests.update',
     // other resource methods
 ]);


   });

   /**
    * User Routes
    */
   /* Route::middleware(['auth', 'role:user'])->group(function () {
       Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');

   }); */


});

require __DIR__.'/auth.php';
