<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveApplicationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\HalfDayController;
use App\Http\Controllers\Admin\AdminLeaveController;
use App\Http\Controllers\Admin\AdminHalfDayController;
use App\Http\Controllers\ManagementSettingsController;
use App\Http\Controllers\DetailsOfLeaveController;
use App\Http\Controllers\LeaveCardController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PayrollDeductionController;
use App\Http\Controllers\PayrollRemittanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PayslipController;

// ── Guest ─────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/',       [AuthController::class, 'showLogin'])->name('login');
    Route::get('/login',  [AuthController::class, 'showLogin']);
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// ── Authenticated ─────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::post('/logout',      [AuthController::class, 'logout'])->name('logout');
    Route::post('/switch-view', [AuthController::class, 'switchView'])->name('auth.switch-view');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Profile ───────────────────────────────────────────────────────────────
    Route::get('/profile',          [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile',          [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // ── Notifications ─────────────────────────────────────────────────────────
    Route::get('/notifications',              [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all',    [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::post('/notifications/{id}/read',   [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.count');

    // ── Employee-facing leave ─────────────────────────────────────────────────
    Route::get('/application/leave',              [LeaveApplicationController::class, 'index'])->name('application.leave');
    Route::post('/application/leave',             [LeaveApplicationController::class, 'store'])->name('leave.store');
    Route::post('/application/leave/monetize',    [LeaveApplicationController::class, 'storeMonetization'])->name('leave.monetize');
    Route::post('/application/leave/{id}/cancel', [LeaveApplicationController::class, 'cancel'])->name('leave.cancel');
    Route::get('/application/leave/{id}/pdf',     [LeaveApplicationController::class, 'pdf'])->name('leave.pdf');

    // ── Employee-facing half day ──────────────────────────────────────────────
    Route::get('/application/halfday',              [HalfDayController::class, 'index'])->name('application.halfday');
    Route::post('/application/halfday',             [HalfDayController::class, 'store'])->name('halfday.store');
    Route::get('/application/halfday/{id}/pdf',     [HalfDayController::class, 'pdf'])->name('halfday.pdf');
    Route::post('/application/halfday/{id}/cancel', [HalfDayController::class, 'cancel'])->name('halfday.cancel');

    // ── Employee-facing payroll (payslip self-service) ────────────────────────
    Route::get('/payroll/payslip',              [PayrollController::class, 'payslip'])->name('payroll.payslip');
    Route::get('/payroll/{period}/payslip-pdf', [PayslipController::class, 'printOne'])->name('payroll.payslip.pdf');

    // Placeholder
    Route::get('/leave-card', fn() => view('placeholder'))->name('leave.card');

    // ── Admin-only ────────────────────────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {

        // ── Employees ─────────────────────────────────────────────────────────
        Route::get('/employees',                     [EmployeeController::class, 'index'])->name('employees.index');
        Route::post('/employees',                    [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/employees/{id}/show',           [EmployeeController::class, 'show'])->name('employees.show');
        Route::put('/employees/{id}',                [EmployeeController::class, 'update'])->name('employees.update');
        Route::post('/employees/{id}/toggle-status', [EmployeeController::class, 'toggleStatus'])->name('employees.toggle');

        // ── Admin leave management ────────────────────────────────────────────
        Route::get('/admin/leave',               [AdminLeaveController::class, 'index'])->name('admin.leave.index');
        Route::post('/admin/leave/{id}/approve', [AdminLeaveController::class, 'approve'])->name('admin.leave.approve');
        Route::post('/admin/leave/{id}/reject',  [AdminLeaveController::class, 'reject'])->name('admin.leave.reject');
        Route::post('/admin/leave/{id}/status',  [AdminLeaveController::class, 'updateStatus'])->name('admin.leave.status');
        Route::get('/admin/leave/{id}/pdf',      [AdminLeaveController::class, 'pdf'])->name('admin.leave.pdf');

        // ── Admin half day management ─────────────────────────────────────────
        Route::get('/admin/halfday',              [AdminHalfDayController::class, 'index'])->name('admin.halfday.index');
        Route::post('/admin/halfday/{id}/status', [AdminHalfDayController::class, 'updateStatus'])->name('admin.halfday.status');
        Route::get('/admin/halfday/{id}/cert',    [AdminHalfDayController::class, 'cert'])->name('admin.halfday.cert');

        // ══════════════════════════════════════════════════════════════════════
        // PAYROLL ROUTES
        // IMPORTANT: All static/fixed-segment routes MUST be declared BEFORE
        // any wildcard {period} routes to prevent route shadowing.
        // ══════════════════════════════════════════════════════════════════════

        // ── 1. Static payroll pages ───────────────────────────────────────────
        Route::get('/payroll',        [PayrollController::class, 'index'])->name('payroll.index');
        Route::get('/payroll/create', [PayrollController::class, 'create'])->name('payroll.create');
        Route::post('/payroll',       [PayrollController::class, 'store'])->name('payroll.store');

        // ── 2. Payslip Management (admin editable grid) ───────────────────────
        Route::get('/payroll/manage', [PayslipController::class, 'manage'])->name('payroll.manage');

        // ── 3. Legacy payslip management view ────────────────────────────────
        Route::get('/payroll/payslip-management', [PayrollController::class, 'payslipManagement'])
            ->name('payroll.payslip.management');

        // ── 4. Remittances (static page + AJAX record actions) ───────────────
        Route::get('/payroll/remittances', [PayrollRemittanceController::class, 'index'])
            ->name('payroll.remittances');

        // AJAX: save a single field value for one payroll_record row
        // Used by saveToDB() in remittances.blade.php via SAVE_URL_BASE
        Route::patch('/payroll/remittance/record/{id}', [PayrollRemittanceController::class, 'saveField'])
            ->name('payroll.remittance.record.save');

        // AJAX: hide/remove a payroll_record row from this period
        // Used by executeDelete() in remittances.blade.php via HIDE_URL_BASE
        Route::patch('/payroll/remittance/record/{id}/hide', [PayrollRemittanceController::class, 'hideRecord'])
            ->name('payroll.remittance.record.hide');

        // ── 5. Payroll record inline edit (modal save) ────────────────────────
        // PATCH /payroll/record/{id}
        // Called by saveModal() in payslip-manage.blade.php
        Route::patch('/payroll/record/{id}', [PayslipController::class, 'updateRecord'])
            ->name('payroll.record.update');

        // Update signatory (clerk name/title) for a period
        // Called by saveSignatory() in payslip-manage.blade.php
        Route::patch('/payroll/period/{period}/signatory', [PayslipController::class, 'updateSignatory'])
            ->name('payroll.period.signatory');

        // ── 6. Payroll Deductions management ─────────────────────────────────
        Route::prefix('payroll/deductions')->name('payroll.deductions.')->group(function () {
            Route::get('/',              [PayrollDeductionController::class, 'index'])->name('index');
            Route::post('/',             [PayrollDeductionController::class, 'store'])->name('store');
            Route::put('/{id}',          [PayrollDeductionController::class, 'update'])->name('update');
            Route::patch('/{id}/toggle', [PayrollDeductionController::class, 'toggle'])->name('toggle');
            Route::delete('/{id}',       [PayrollDeductionController::class, 'destroy'])->name('destroy');
            Route::post('/reorder',      [PayrollDeductionController::class, 'reorder'])->name('reorder');
        });

        // ── 7. Period-level actions (wildcard — AFTER all static routes) ──────

        // Finalize a payroll period
        Route::post('/payroll/{period}/finalize', [PayrollController::class, 'finalize'])
            ->name('payroll.finalize');



        // ── 8. PDF exports (wildcard — AFTER all static routes) ──────────────

        // Full payroll PDF (landscape, all employees)
        Route::get('/payroll/{period}/pdf', [PayrollController::class, 'pdf'])
            ->name('payroll.pdf');

        // All payslips PDF for a period (optionally filtered by ?emp_id=)
        Route::get('/payroll/{period}/payslip-all-pdf', [PayslipController::class, 'printAll'])
            ->name('payroll.payslip-all-pdf');

        // Remittance PDF (GSIS / Pag-Ibig / PhilHealth etc.)
        Route::get('/payroll/{period}/remittance/{type}/pdf', [PayrollRemittanceController::class, 'remittancePdf'])
            ->name('payroll.remittance.pdf');

        // ── Logs ──────────────────────────────────────────────────────────────
        Route::get('/logs', fn() => view('placeholder'))->name('logs.index');

        // ── Leave Card (Admin) ────────────────────────────────────────────────
        Route::prefix('admin/leave-card')->name('leave-card.')->group(function () {
            Route::get('/',                          [LeaveCardController::class, 'index'])->name('index');
            Route::get('/print-all',                 [LeaveCardController::class, 'printAll'])->name('print-all');
            Route::get('/{employeeId}/{year}',       [LeaveCardController::class, 'show'])->name('show');
            Route::get('/{employeeId}/{year}/print', [LeaveCardController::class, 'print'])->name('print');
            Route::post('/save',                     [LeaveCardController::class, 'save'])->name('save');
        });

        // ── Management Settings ───────────────────────────────────────────────
        Route::prefix('settings')->name('settings.')->group(function () {

            Route::get('/', [ManagementSettingsController::class, 'index'])->name('index');

            // Leave Type
            Route::get('/leave-type',               [ManagementSettingsController::class, 'leaveType'])->name('leaveType');
            Route::post('/leave-type',              [ManagementSettingsController::class, 'storeLeaveType']);
            Route::put('/leave-type/{id}',          [ManagementSettingsController::class, 'updateLeaveType']);
            Route::patch('/leave-type/{id}/toggle', [ManagementSettingsController::class, 'toggleLeaveType']);
            Route::delete('/leave-type/{id}',       [ManagementSettingsController::class, 'destroyLeaveType']);

            // Department
            Route::get('/department',               [ManagementSettingsController::class, 'department'])->name('department');
            Route::post('/department',              [ManagementSettingsController::class, 'storeDepartment']);
            Route::put('/department/{id}',          [ManagementSettingsController::class, 'updateDepartment']);
            Route::patch('/department/{id}/toggle', [ManagementSettingsController::class, 'toggleDepartment']);
            Route::delete('/department/{id}',       [ManagementSettingsController::class, 'destroyDepartment']);

            // Position
            Route::get('/position',               [ManagementSettingsController::class, 'position'])->name('position');
            Route::post('/position',              [ManagementSettingsController::class, 'storePosition']);
            Route::put('/position/{id}',          [ManagementSettingsController::class, 'updatePosition']);
            Route::patch('/position/{id}/toggle', [ManagementSettingsController::class, 'togglePosition']);
            Route::delete('/position/{id}',       [ManagementSettingsController::class, 'destroyPosition']);

            // Details of Leave
            Route::get('/details-of-leave',                                 [DetailsOfLeaveController::class, 'index'])->name('detailsOfLeave');
            Route::post('/details-of-leave/groups',                         [DetailsOfLeaveController::class, 'storeGroup']);
            Route::put('/details-of-leave/groups/{id}',                     [DetailsOfLeaveController::class, 'updateGroup']);
            Route::delete('/details-of-leave/groups/{id}',                  [DetailsOfLeaveController::class, 'destroyGroup']);
            Route::post('/details-of-leave/groups/{groupId}/items',         [DetailsOfLeaveController::class, 'storeItem']);
            Route::post('/details-of-leave/groups/{groupId}/items/reorder', [DetailsOfLeaveController::class, 'reorderItems']);
            Route::put('/details-of-leave/items/{id}',                      [DetailsOfLeaveController::class, 'updateItem']);
            Route::delete('/details-of-leave/items/{id}',                   [DetailsOfLeaveController::class, 'destroyItem']);

            // Commutation
            Route::get('/commutation',            [ManagementSettingsController::class, 'commutation'])->name('commutation');
            Route::post('/commutation',           [ManagementSettingsController::class, 'storeCommutation']);
            Route::put('/commutation/{id}',       [ManagementSettingsController::class, 'updateCommutation']);
            Route::delete('/commutation/{id}',    [ManagementSettingsController::class, 'destroyCommutation']);

            // Recommendation
            Route::get('/recommendation',         [ManagementSettingsController::class, 'recommendation'])->name('recommendation');
            Route::post('/recommendation',        [ManagementSettingsController::class, 'storeRecommendation']);
            Route::put('/recommendation/{id}',    [ManagementSettingsController::class, 'updateRecommendation']);
            Route::delete('/recommendation/{id}', [ManagementSettingsController::class, 'destroyRecommendation']);

            // Signatory (management settings — NOT the payroll signatory)
            Route::get('/signatory',              [ManagementSettingsController::class, 'signatory'])->name('signatory');
            Route::post('/signatory',             [ManagementSettingsController::class, 'storeSignatory']);
            Route::put('/signatory/{id}',         [ManagementSettingsController::class, 'updateSignatory']);
            Route::delete('/signatory/{id}',      [ManagementSettingsController::class, 'destroySignatory']);

            // Role
            Route::get('/role',              [ManagementSettingsController::class, 'role'])->name('role');
            Route::post('/role',             [ManagementSettingsController::class, 'storeRole']);
            Route::put('/role/{id}',         [ManagementSettingsController::class, 'updateRole']);
            Route::delete('/role/{id}',      [ManagementSettingsController::class, 'destroyRole']);

        }); // ← settings prefix ends

    }); // ← Admin middleware ends

}); // ← Auth middleware ends