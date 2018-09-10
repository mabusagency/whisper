<?php

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'AppController@index');

Route::group(['middleware' => ['auth','admin','check_sessions']], function () {

    Route::put('/user','UserController@update')->name('users.update');
    Route::put('/user/settings','UserController@update_security')->name('users.update.security');

    //Institutions
    Route::resource('/institutions','InstitutionController');
    Route::get('/institutions/set/{institution}', 'InstitutionController@set');

    //Fields
    Route::resource('/fields', 'FieldController');

    //Upload Students
    Route::post('/campaign/student/upload_preview', 'StudentsController@upload_preview')->name('students.upload.preview');
    Route::get('/campaign/student/upload_preview', 'StudentsController@upload_preview')->name('students.upload.preview');
    Route::post('/campaign/student/upload_execute', 'StudentsController@upload_execute')->name('students.upload.execute');
});

//Managers
Route::group(['middleware' => ['auth','manager','check_sessions']], function () {

    //Campaigns
    Route::delete('/campaigns/{campaign}/contacts', 'CampaignController@reset');
    Route::get('/campaign/settings', 'CampaignController@settings');

    //Results
    Route::get('/campaign/results', 'CampaignController@results');

    //Staff
    Route::resource('/staff', 'StaffController');
});

//Staff
Route::group(['middleware' => ['auth','check_sessions']], function () {

    //Campaigns
    Route::resource('/campaigns', 'CampaignController');
    Route::get('/campaigns/set/{campaign}', 'CampaignController@set');

    //Students
    Route::resource('/campaign/students', 'StudentsController');
    Route::get('/campaign/student/search', 'StudentsController@index')->name('students.search');
    Route::get('/campaign/student/export', 'StudentsController@export')->name('students.export');
    Route::get('/campaign/student/{student_id}/restore', 'StudentsController@restore')->name('student.restore');

    Route::get('/note/delete/{student}/{note}', 'NoteController@destroy')->name('note.delete');

});

//Settings
Route::get('/settings/profile', 'SettingsController@profile');
Route::get('/settings/security', 'SettingsController@security');