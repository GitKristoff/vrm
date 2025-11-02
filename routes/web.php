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
use App\Http\Controllers\AIController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AIChatController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

//Dashboard Redirector
Route::get('/dashboard', function () {
    // You can redirect based on user role here, or show a general dashboard
    $user = Auth::user();
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->role === 'veterinarian') {
        return redirect()->route('vet.dashboard');
    }
    return redirect()->route('owner.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// Admin Routes:
Route::middleware(['auth', 'admin.access', 'verified'])->prefix('admin')->group(function () {
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
Route::middleware(['auth', 'role:owner', 'verified'])->group(function () {
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
Route::middleware(['auth', 'role:veterinarian,admin', 'verified'])->group(function () {
    Route::get('/dashboard', [VetController::class, 'dashboard'])->name('vet.dashboard');

    Route::get('/vet/medical-records', [VetController::class, 'medicalRecords'])
        ->name('vet.medrecords.index');
    Route::get('vet/medrecords/{pet}', [MedicalRecordController::class, 'show'])->name('vet.records.show');

    // Medical Record Archive Routes
    Route::post('/medical-records/{record}/archive', [MedicalRecordController::class, 'archive'])
    ->name('medical-records.archive');
    Route::post('/medical-records/{id}/restore', [MedicalRecordController::class, 'restore'])
        ->name('medical-records.restore');
    Route::get('/medical-records/archived', [MedicalRecordController::class, 'archived'])
        ->name('medical-records.archived');

    Route::get('/vet/settings', [VetController::class, 'settings'])->name('vet.settings');
    Route::put('/vet/settings', [VetController::class, 'updateSettings'])->name('vet.settings.update');
});

// Common routes for all users
Route::middleware(['auth', 'verified'])->group(function () {
   // Unified appointment routes
    Route::get('/appointments/calendar', [AppointmentController::class, 'calendar'])
        ->name('appointments.calendar');
    Route::get('/appointments/reminders', [AppointmentController::class, 'sendReminders'])
        ->name('appointments.reminders');
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');
    Route::get('/appointments/{appointment}/edit', [AppointmentController::class, 'edit'])->name('appointments.edit');
    Route::put('/appointments/{appointment}', [AppointmentController::class, 'update'])->name('appointments.update');
    Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');
    Route::get('/appointments/{appointment}/checkin', [AppointmentController::class, 'checkinForm'])
    ->name('appointments.checkin.create');
    Route::post('/appointments/{appointment}/checkin', [AppointmentController::class, 'checkinStore'])
    ->name('appointments.checkin.store');
    Route::get('/appointments/remaining', [AppointmentController::class, 'remainingSlots'])->name('appointments.remaining');

    // Medical Records Routes
    Route::get('/medical-records', [MedicalRecordController::class, 'index'])
        ->name('medical-records.index');
    Route::get('/medical-records/{pet}', [MedicalRecordController::class, 'show'])
        ->name('medical-records.show');
    Route::get('/pets/{pet}/medical-records/create', [MedicalRecordController::class, 'create'])
        ->name('medical-records.create');
    Route::post('/pets/{pet}/medical-records', [MedicalRecordController::class, 'store'])
        ->name('medical-records.store');
    Route::get('/medical-records/{record}/report', [MedicalRecordController::class, 'report'])
        ->name('medical-records.report');
    Route::get('/pets/{pet}/medical-records/download', [MedicalRecordController::class, 'downloadPdf'])
        ->name('medical-records.download');

        // Route::post('/ai/predict', [AIController::class, 'predict'])
        //     ->name('ai.predict');
});

// AI Diagnosis Routes
// Route::middleware(['auth', 'verified'])->post('/ai/predict', [AIController::class, 'predict'])
//     ->name('ai.predict');

// Profile routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/profile/remove-picture', [ProfileController::class, 'removePicture'])->name('profile.remove-picture');

    Route::prefix('chat')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('chat.index');
        Route::get('/create', [ChatController::class, 'create'])->name('chat.create');
        Route::post('/start', [ChatController::class, 'storeConversation'])->name('chat.storeConversation');
        Route::post('/{conversation}', [ChatController::class, 'store'])->name('chat.store');
        Route::get('/{conversation}', [ChatController::class, 'show'])->name('chat.show');
        Route::get('/{conversation}/poll', [ChatController::class, 'poll'])->name('chat.poll');
        Route::delete('/conversation/{conversation}', [ChatController::class, 'destroy'])->name('chat.conversation.destroy');

    });

    // AI Chat Routes
    Route::post('/ai-chat', [AIChatController::class, 'store'])
        ->name('ai.chat.store')
        ->middleware('throttle:10,1'); // 10 requests per minute, adjust as needed
});

require __DIR__.'/auth.php';
