<?php

use App\Http\Controllers\Backend\Activity\ActivityController;
use App\Http\Controllers\Backend\Agent\AgentController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\Devices\DevicesController;
use App\Http\Controllers\Backend\Reports\ReportsController;
use App\Http\Controllers\Backend\Transaction\TransactionController;
use App\Http\Controllers\Backend\Payment\PaymentController;

/*
 * All route names are prefixed with 'admin.'.
 */
Route::redirect('/', '/admin/dashboard', 301);
Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::post('isGenerateLink', [DashboardController::class, 'isGenerateLink'])->name('isGenerateLink');
Route::post('dashboardData', [DashboardController::class, 'getDashboardData'])->name('dashboard.getData');
Route::post('cardData', [DashboardController::class, 'getChartData'])->name('dashboard.getChartData');

/*
 * Transaction
 */
Route::group([
    'prefix' => 'transaction',
    'namespace' => 'Transaction',
], function () {
    Route::get('firstTransactions', [TransactionController::class, 'firstTransactionView'])->name('transaction.firstTransaction');
    Route::get('allTransactions', [TransactionController::class, 'recuredTransactionView'])->name('transaction.allTransactions');
    Route::get('getTransactions', [TransactionController::class, 'getTransactionByInsure'])->name('transaction.getTransaction');
    Route::post('editSchedule', [TransactionController::class, 'editSchedule'])->name('transaction.editSchedule');
    Route::post('statusSchedule', [TransactionController::class, 'changeStatus'])->name('transaction.statusSchedule');
    Route::post('editApprove', [TransactionController::class, 'editApprove'])->name('transaction.editApprove');
    Route::post('editStatusApprove', [TransactionController::class, 'editStatusApprove'])->name('transaction.editStatusApprove');
    Route::post('delete',[TransactionController::class, 'delete'])->name('transaction.delete');
    Route::get('pendingTransaction', [TransactionController::class, 'viewPending'])->name('transaction.pendingTransaction');
});

/*
 * Agent
 */
Route::group([
    'prefix' => 'agent',
    'namespace' => 'Agent',
], function () {
    Route::post('validatePoNo', [AgentController::class, 'validatePoNo'])->name('agent.validatePoNo');
    Route::get('policePayment', [AgentController::class, 'createLinkView'])->name('agent.policePayment');
    Route::post('policePayment/create', [AgentController::class, 'createLink'])->name('agent.policePayment.create');
    Route::get('paymentHistory', [AgentController::class, 'paymentHistory'])->name('agent.paymentHistory');
});

/*
 * Payment
 */
Route::group([
    'prefix' => 'payment',
    'namespace' => 'Payment',
], function () {
    Route::get('newPayment', [PaymentController::class, 'newPayment'])->name('payment.newPayment');
    Route::post('makeNewPayment', [PaymentController::class, 'makeNewPayment'])->name('payment.makeNewPayment');
});

/*
 * Activity
 */

Route::group([
    'namespace' => 'Activity',
], function () {
    Route::get('activityLog', [ActivityController::class, 'index'])->name('activityLog');
});

/*
 * devices
 */
Route::group([
    'prefix' => 'devices',
    'namespace' => 'Devices',
], function () {
    Route::get('listDevice', [DevicesController::class, 'index'])->name('devices.listDevice');
    Route::post('deleteDevice', [DevicesController::class, 'deleteDevice'])->name('devices.deleteDevice');
});

/*
 * reports
 */
Route::group([
    'prefix' => 'reports',
    'namespace' => 'Reports',
], function () {
    Route::get('delivery', [ReportsController::class, 'delivery'])->name('reports.delivery');
    Route::get('fileUpload', [ReportsController::class, 'fileUploadReport'])->name('reports.fileUpload');
    Route::post('fileUpload/upload', [ReportsController::class, 'upload'])->name('reports.fileUpload.upload');
    Route::get('file/download', [ReportsController::class, 'download'])->name('reports.file.download');
    Route::get('transaction', [ReportsController::class, 'transactionReport'])->name('reports.transaction');
    Route::get('cardExp', [ReportsController::class, 'cardExp'])->name('reports.cardExp');
    Route::get('statements', [ReportsController::class, 'statements'])->name('reports.statements');
    Route::get('statements/getData', [ReportsController::class, 'getStatementData'])->name('reports.getStatementData');
    Route::get('statements/getfile', [ReportsController::class, 'fileDownload'])->name('reports.getfile');
    Route::get('uploads', [ReportsController::class, 'uploads'])->name('reports.uploads');
    Route::post('cardUpdate', [ReportsController::class, 'sendUpdateLink'])->name('reports.cardUpdate');
});
