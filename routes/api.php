<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Psicologia\PsicologoController;

Route::get('/psicologos', [PsicologoController::class, 'getPsicologos'])->name('api-getPsicologo');
