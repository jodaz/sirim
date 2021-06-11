<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('tokens/login', 'ManageTokenController@login');

// Route::middleware('auth:sanctum')->group(function () {
     Route::get('years', 'YearController@index');
     Route::apiResource('concepts', 'ConceptController');
     Route::apiResource('purposes', 'PurposeController');
     Route::apiResource('terrain-classifications', 'TerrainClassificationController');
     Route::apiResource('payment-methods', 'PaymentMethodController')
         ->except(['show']);
     Route::apiResource('payment-types', 'PaymentTypeController')
         ->except(['show']);
     Route::apiResource('representation-types', 'RepresentationTypeController');
     Route::apiResource('tax-units', 'TaxUnitController')
         ->except(['show']);
     Route::apiResource('brands', 'BrandController')
         ->except(['show']);
     Route::apiResource('colors', 'ColorController');
     Route::apiResource('users', 'UserController');
     Route::apiResource('ordinances', 'OrdinanceController')
         ->except(['show']);
     Route::apiResource('charging-methods', 'ChargingMethodController')
         ->except(['show']);

     Route::apiResource('economic-activities', 'EconomicActivityController');

     // Controlled level permissions
     Route::apiResource('liquidation-types', 'LiquidationTypeController')
         ->except(['show']);
     Route::apiResource('states', 'StateController');
     Route::apiResource('municipalities', 'MunicipalityController');
     Route::apiResource('communities', 'CommunityController');
     Route::apiResource('parishes', 'ParishController');
     Route::apiResource('license', 'LicenseController');
     Route::apiResource('taxpayers', 'TaxpayerController');
     Route::apiResource('liquidations', 'LiquidationController');
     Route::apiResource('companies', 'CompanyController');
     Route::apiResource('commercial-registers', 'CommercialRegisterController');
     Route::apiResource('accounts', 'AccountController');
     Route::apiResource('accounting-accounts', 'AccountingAccountController');
     Route::apiResource('account-types', 'AccountTypeController');
     Route::apiResource('payments', 'PaymentController');
     Route::apiResource('cancellations', 'CancellationController');
     Route::apiResource('cancellation-types', 'CancellationTypeController');
     Route::apiResource('licenses', 'LicenseController');
     Route::apiResource('affidavits', 'AffidavitController');
     Route::apiResource('applications', 'ApplicationController');
     Route::apiResource('fines', 'FineController');
     Route::apiResource('deductions', 'DeductionController');

     /**
      * Consults
      */
     Route::get('petro-prices', 'PetroPriceController@index');
     Route::get('taxpayer-types', 'TaxpayerTypeController@index');
     Route::get('taxpayer-classifications', 'TaxpayerClassificationController@index');
     Route::get('status', 'StatusController@index');
     Route::get('intervals', 'IntervalController@index');
     Route::get('movements', 'MovementController@index');
     Route::get('closures', 'ClosureController@index');
// });