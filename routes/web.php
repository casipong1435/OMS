<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MayorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\TreasurerController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ReportController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::controller(GuestController::class)->group(function(){
    Route::get('/', 'welcome')->name('welcome');
    Route::get('/about', 'about')->name('about');
    Route::get('/services', 'services')->name('services');
    Route::get('/ordinance', 'ordinance')->name('ordinance');
    Route::get('/establishment/{id}', 'establishment')->name('guest.establishment');
    Route::get('/establishment-unit/{id}', 'establishment_unit')->name('guest.establishment_unit');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'user-role:user'])->prefix('vendor')->group(function(){
    Route::controller(VendorController::class)->group(function() {

        //GET ROUTES
        Route::get('/dashboard', 'dashboard')->name('user.dashboard');
        Route::get('/business', 'business')->name('user.business');
        Route::get('/payment', 'payment')->name('user.payment');
        Route::get('/profile', 'profile')->name('user.profile');

        //POST ROUTES
        Route::post('/apply-stall', 'applyStall')->name('user.apply');
        Route::post('/send-otp', 'sendOTP')->name('user.sendOTP');
        Route::post('/read-notification/{id}', 'markasReadNotification')->name('user.markasReadNotification');
        Route::post('/read-all-notification', 'markasReadAllNotification')->name('user.markasReadAllNotification');
        

        //PUT ROUTES
        Route::put('/update-profile', 'updateProfile')->name('user.update-profile');
        Route::put('/update-permit', 'updatePermit')->name('user.update-permit');
        Route::put('/verify-OTP', 'verifyOTP')->name('user.verifyOTP');
        
        Route::put('/change-profile-image', 'changeProfile')->name('user.change-profile-image');
        
    });
});

Route::middleware(['auth', 'user-role:admin'])->prefix('admin')->group(function(){
    Route::controller(AdminController::class)->group(function() {
        Route::get('/dashboard', 'dashboard')->name('admin.dashboard');
        Route::get('/officials', 'officials')->name('admin.officials');
        Route::get('/users', 'users')->name('admin.users');

        //Officials Post  Request
        Route::post('/officials', 'addOfficial')->name('admin.addOfficial');
        Route::put('/officials/{id}', 'updateOfficial')->name('admin.updateOfficial');
        Route::delete('/officials/{id}', 'deleteOfficial')->name('admin.deleteOfficial');
        
        //Users Post Request
        Route::put('/users/{id}/{status}', 'restrictUser')->name('admin.restrictUser');
        Route::put('/users/{id}', 'editUser')->name('admin.editUser');

    });
});

Route::middleware(['auth', 'user-role:ceedo'])->prefix('ceedo')->group(function(){
    Route::controller(StaffController::class)->group(function() {
        //GET
        Route::get('/dashboard', 'dashboard')->name('ceedo.dashboard');
        Route::get('/vendors', 'vendors')->name('ceedo.vendors');
        Route::get('/applications', 'applications')->name('ceedo.applications');
        Route::get('/closed_business', 'closed_business')->name('ceedo.closed_business');
        Route::get('/application-info/{id}', 'applicationInfo')->name('ceedo.applicationInfo');
        Route::get('/vendor-profile/{id}', 'vendorProfile')->name('ceedo.vendorProfile');
        Route::get('/compliance', 'compliance')->name('ceedo.compliance');
        Route::get('/payment-due', 'payment_due')->name('ceedo.payment-due');
        Route::get('/renewal', 'renewal')->name('ceedo.renewal');
        Route::get('/establishments', 'establishments')->name('ceedo.establishments');
        Route::get('/establishments/stall/{id}', 'stalls')->name('ceedo.stalls');
        Route::get('/operations-report', 'operations_report')->name('ceedo.operations-report');
        Route::get('/vendors-report', 'vendors_report')->name('ceedo.vendors-report');
        Route::get('/compliance-report', 'compliance_report')->name('ceedo.compliance-report');
        Route::get('/activities', 'activities')->name('ceedo.activities');
        //END GET
        
        //POST ROUTES
        Route::post('/establishments', 'addArea')->name('ceedo.post-establishments');
        Route::post('/establishments/add-establishment', 'addEstablishment')->name('ceedo.post-stall');
        Route::post('/establishments/stall', 'addUnit')->name('ceedo.post-stall-addUnit');
        Route::post('/establishments/unit', 'editEstablishmentUnit')->name('ceedo.put-stall-unit');
        Route::post('/renewal/SendPermitUpdateNotice', 'SendPermitUpdateNotice')->name('ceedo.SendPermitUpdateNotice');
        Route::post('/renewal/sendWarning', 'sendWarning')->name('ceedo.sendWarning');
        Route::post('/renewal/SendPaymentReminder', 'SendPaymentReminder')->name('ceedo.SendPaymentReminder');

        Route::post('/read-notification/{id}', 'markasReadNotification')->name('ceedo.markasReadNotification');
        Route::post('/read-all-notification', 'markasReadAllNotification')->name('ceedo.markasReadAllNotification');
    
        //PUT ROUTES
        Route::put('/establishments-area/{id}', 'editArea')->name('ceedo.put-establishments');
        Route::put('/establishments/coords/{id}', 'updateAreaCoordinates')->name('ceedo.put-establishments-coords');
        Route::put('/establishments-map/{id}', 'editMapVisibility')->name('ceedo.put-establishments-map');
        Route::put('/establishments/stall/{id}', 'editEstablishment')->name('ceedo.put-stall');
        Route::put('/approve-application/{id}', 'approveApplication')->name('ceedo.approveApplication');
        Route::put('/decline-application/{id}', 'declineApplication')->name('ceedo.declineApplication');
        Route::put('/close-business/{id}', 'closeBusiness')->name('ceedo.closeBusiness');
        Route::put('/reopen-business/{id}', 'reopenBusiness')->name('ceedo.reopenBusiness');
        
        //DELETE ROUTES
        Route::delete('/establishments/unit/{id}', 'deleteUnit')->name('ceedo.stall-delete');
        Route::delete('/deleteActivity', 'deleteActivities')->name('ceedo.deleteActivities');
        
        Route::controller(ReportController::class)->prefix('report')->group(function(){
            Route::get('/financial-report/{from}/{to}/{category}', 'financialReport')->name('ceedo.financialReport');
            Route::get('/operational-report/{category}', 'operationalReport')->name('ceedo.operationalReport');
            Route::get('/compliance-report', 'complianceReport')->name('ceedo.complianceReport');
        });
        
    });
});

Route::middleware(['auth', 'user-role:treasurer'])->prefix('treasurer')->group(function(){
    Route::controller(TreasurerController::class)->group(function() {
        Route::get('/dashboard', 'dashboard')->name('treasurer.dashboard');
        Route::get('/payment-due', 'payment_due')->name('treasurer.payment-due');
        Route::get('/payment_portal/{id}', 'payment_portal')->name('treasurer.payment_portal');
        Route::get('/transaction', 'transaction')->name('treasurer.transaction');
        Route::get('/report', 'report')->name('treasurer.report');
        Route::get('/getVendorsReport', 'getVendorsReport')->name('treasurer.getVendorsReport');

        //POST
        Route::post('/pay', 'pay')->name('treasurer.pay');

        Route::controller(ReportController::class)->prefix('report')->group(function(){
            Route::get('/financial-report/{from}/{to}/{category}', 'financialReport')->name('treasurer.financialReport');
        });
    });
});

Route::middleware(['auth', 'user-role:mayor'])->prefix('mayor')->group(function(){
    Route::controller(MayorController::class)->group(function() {
        Route::get('/dashboard', 'dashboard')->name('mayor.dashboard');
        Route::get('/business-operation', 'business_operation')->name('mayor.business-operation');
        Route::get('/business-sections/{id}', 'sections')->name('mayor.business-sections');
        Route::get('/financial-monitoring', 'financial_monitoring')->name('mayor.financial-monitoring');
        Route::get('/compliance', 'compliance')->name('mayor.compliance');
        Route::get('/vendor-profile/{id}', 'vendorProfile')->name('mayor.vendorProfile');
        Route::get('/operations-report', 'operations_report')->name('mayor.operations-report');
        Route::get('/vendors-report', 'vendors_report')->name('mayor.vendors-report');
        Route::get('/compliance-report', 'compliance_report')->name('mayor.compliance-report');
    });

    Route::controller(ReportController::class)->prefix('report')->group(function(){
        Route::get('/financial-report/{from}/{to}/{category}', 'financialReport')->name('mayor.financialReport');
        Route::get('/operational-report/{category}', 'operationalReport')->name('mayor.operationalReport');
        Route::get('/compliance-report', 'complianceReport')->name('mayor.complianceReport');
    });

});

Route::prefix('api')->group(function(){
    Route::controller(AddressController::class)->group(function(){
        Route::get('/provinces/{region}', 'getProvinces')->name('api.provinces');
        Route::get('/cities/{province}', 'getCities')->name('api.cities');
        Route::get('/barangays/{city}', 'getBarangays')->name('api.barangays');
    });

    Route::get('/getVendorsReport', [TreasurerController::class, 'getVendorsReport'])->name('getVendorsReport');
    Route::get('/getOperationsReport', [MayorController::class, 'getOperationsReport'])->name('getOperationsReport');
});

require __DIR__.'/auth.php';
