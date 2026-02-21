<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MusicaController;
use App\Http\Controllers\ArtistaController;
use App\Http\Controllers\EstiloController;
use App\Http\Controllers\MomentoController;

// Prefixo Global da API (v1)
Route::prefix('v1')->group(function () {

    // --- GRUPO 1: MÚSICAS (O Principal) ---
    // Rotas: GET, POST, PUT, DELETE em /api/v1/musicas
    Route::apiResource('musicas', MusicaController::class);

    // --- GRUPO 2: ARTISTAS ---
    // Rotas: GET, POST, PUT, DELETE em /api/v1/artistas
    Route::apiResource('artistas', ArtistaController::class);

    // --- GRUPO 3: ESTILOS ---
    // Rotas: GET, POST, PUT, DELETE em /api/v1/estilos
    Route::apiResource('estilos', EstiloController::class);

    // --- GRUPO 4: MOMENTOS ---
    // Rotas: GET, POST, PUT, DELETE em /api/v1/momentos
    Route::apiResource('momentos', MomentoController::class);

});
