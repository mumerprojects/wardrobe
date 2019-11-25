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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['middleware'=>['api','cors']],function(){

	// Gets user data and record it in database
    Route::post('User/userRegister','UserController@userRegister');
	// Gets Credentials and process login
    Route::post('User/userLogin','UserController@userLogin');
	// Get data from user and updates password
    Route::post('User/updatePassword','UserController@updatePassword');
	//Sents the verification email
    Route::post('User/basicRequestemail','UserController@basicRequestemail');
	//Add Advertisement to the database
    Route::post('User/addAdvertisement','UserController@addAdvertisement');
	
	//Add Rent Request to the database
    Route::post('User/addRequest','UserController@addRequest');
	
    //Updates the User Data
	Route::put('User/updateUser','UserController@updateUser');
	//Gets the user data from email arg.
    Route::get('User/getUserData/{EMAIL}','UserController@getUserData');
    //Gets the Dress Info.
    Route::get('User/getDressInfo','UserController@getDressInfo');
	//It will get all parameters of dress data
	Route::get('User/getDressData/{ID}','UserController@getDressData');
	//To check wether the ad user and viewing user is same.
	Route::get('User/getAdVer/{EMAIL}','UserController@getAdVer');

	//To Upload the picture
	Route::post('User/uploadPic','UserController@uploadPic');
});
