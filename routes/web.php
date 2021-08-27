<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->name('admin.')->prefix('admin')->group(function () {
    Route::get('roles', [AdminController::class, 'roles'])->name('role');
    Route::post('roles', [AdminController::class, 'roles']);
    Route::get('users', [AdminController::class, 'users'])->name('users');
    Route::post('users', [AdminController::class, 'users']);
    Route::get('projects', [AdminController::class, 'projects'])->name('projects');
    Route::post('projects', [AdminController::class, 'projects']);
    Route::get('project-members/{project}', [AdminController::class, 'project_members'])->name('project_members');
    Route::post('project-members/{project}', [AdminController::class, 'project_members']);
    Route::get('tasks/{project}', [AdminController::class, 'project_tasks'])->name('project_tasks');
    Route::post('tasks/{project}', [AdminController::class, 'project_tasks']);
});

Route::middleware(['auth'])->name('user.')->prefix('user')->group(function () {
    Route::get('projects', [UserController::class, 'projects'])->name('projects');
    Route::post('projects', [UserController::class, 'projects']);
    Route::get('project/{project}', [UserController::class, 'project'])->name('project');
    Route::get('tasks', [UserController::class, 'tasks'])->name('tasks');
    Route::post('tasks', [UserController::class, 'tasks']);
});
