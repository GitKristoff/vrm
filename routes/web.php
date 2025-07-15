<?php
use Illuminate\Support\Facades\Auth;
use App\Models\Veterinarian;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\VetController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\Admin\VeterinarianController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\AppointmentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

//Dashboard Redirector
Route::get('/dashboard', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    $user = auth()->user();

    // Handle veterinarian admins
    if ($user->role === 'veterinarian') {
        $vet = Veterinarian::where('user_id', $user->id)->first();
        if ($vet && $vet->is_admin) {
            return redirect()->route('admin.dashboard');
        }
    }

    return match($user->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'veterinarian' => redirect()->route('vet.dashboard'),
        default => redirect()->route('owner.dashboard'),
    };
})->middleware(['auth'])->name('dashboard');


// Admin Routes:
Route::middleware(['auth', 'admin.access'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::get('/users/{user}', [AdminController::class, 'show'])
    ->name('admin.users.show');
    Route::delete('/users/{user}', [AdminController::class, 'destroy'])->name('admin.users.destroy');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users.index');

    // Veterinarian Management
    Route::get('/veterinarians', [VeterinarianController::class, 'index'])
        ->name('admin.veterinarians.index');

    Route::get('/veterinarians/create', [VeterinarianController::class, 'create'])
        ->name('admin.veterinarians.create');

    Route::get('/veterinarians/{veterinarian}/edit', [VeterinarianController::class, 'edit'])
    ->name('admin.veterinarians.edit');

    Route::put('/veterinarians/{veterinarian}', [VeterinarianController::class, 'update'])
        ->name('admin.veterinarians.update');

    Route::get('/veterinarians/{veterinarian}', [VeterinarianController::class, 'show'])
        ->name('admin.veterinarians.show');

    Route::delete('/veterinarians/{veterinarian}', [VeterinarianController::class, 'destroy'])
        ->name('admin.veterinarians.destroy');

    Route::post('/veterinarians', [VeterinarianController::class, 'store'])
        ->name('admin.veterinarians.store');
});

// Owner routes:
Route::middleware(['auth', 'role:owner'])->group(function () {
    // Add this dashboard route
    Route::get('/owner/dashboard', [OwnerController::class, 'dashboard'])->name('owner.dashboard');

    Route::get('/pets', [OwnerController::class, 'pets'])->name('owner.pets.index');

        // Pet management routes
    Route::get('/pets/create', [PetController::class, 'create'])->name('pets.create');
    Route::post('/pets', [PetController::class, 'store'])->name('pets.store');
    Route::get('/pets/{pet}', [PetController::class, 'show'])->name('pets.show');
    Route::get('/pets/{pet}/edit', [PetController::class, 'edit'])->name('pets.edit');
    Route::put('/pets/{pet}', [PetController::class, 'update'])->name('pets.update');
    Route::delete('/pets/{pet}', [PetController::class, 'destroy'])->name('pets.destroy');

});

// Veterinarian routes:
Route::middleware(['auth', 'role:veterinarian,admin'])->group(function () {
    Route::get('/dashboard', [VetController::class, 'dashboard'])->name('vet.dashboard');

    Route::get('/vet/medical-records', [VetController::class, 'medicalRecords'])
        ->name('vet.medrecords.index');
    Route::get('vet/medrecords/{pet}', [MedicalRecordController::class, 'show'])->name('vet.records.show');

    Route::get('/settings', [VetController::class, 'settings'])->name('vet.settings');
});

// Common routes for all users
Route::middleware(['auth'])->group(function () {
   // Unified appointment routes
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');
    Route::get('/appointments/{appointment}/edit', [AppointmentController::class, 'edit'])->name('appointments.edit');
    Route::put('/appointments/{appointment}', [AppointmentController::class, 'update'])->name('appointments.update');
    Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');
    // Route::post('/appointments/{appointment}/checkin', [AppointmentController::class, 'checkin'])
    // ->name('appointments.checkin');
    Route::get('/appointments/{appointment}/checkin', [AppointmentController::class, 'checkinForm'])
    ->name('appointments.checkin.create');
    Route::post('/appointments/{appointment}/checkin', [AppointmentController::class, 'checkinStore'])
    ->name('appointments.checkin.store');

    Route::get('/medical-records', [MedicalRecordController::class, 'index'])
        ->name('medical-records.index');
    Route::get('/medical-records/{pet}', [MedicalRecordController::class, 'show'])
        ->name('medical-records.show');
});

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
