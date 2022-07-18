<?php
use Illuminate\Support\Facades\Route;
/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    //return view('home');
    return $router->app->version();
});


//$router->group(['prefix'=>'api/v1', 'middleware' => 'auth'], function() use($router){
$router->group(['prefix'=>'api/v1'], function() use($router){
    $router->get('/student', 'StudentController@index');
    $router->post('/createstudent', 'StudentController@create');
    $router->get('/showstudent/{id}', 'StudentController@show');
    $router->put('/editstudent/{id}', 'StudentController@update');
    $router->delete('/deletestudent/{id}', 'StudentController@destroy');

});
