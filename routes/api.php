<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\NotificacionCtrl;
use App\Http\Controllers\RangoCtrl;
use App\Http\Controllers\BeneficioCtrl;
use App\Http\Controllers\ChatCtrl;
use App\Http\Controllers\MailController;
use App\Http\Controllers\ExcelCtrl;


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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ============= USUARIO ================================

Route::post('/Login', [UsuarioController::class, 'Login']);
Route::post('/LoginAdmin', [UsuarioController::class, 'LoginAdmin']);
Route::post('/RefreshUser', [UsuarioController::class, 'RefreshUser']);

Route::post('/VerificarDni', [UsuarioController::class, 'VerificarDni']);
Route::post('/RegistrarUsuario', [UsuarioController::class, 'RegistrarUsuario']);
Route::get('/GetUsuario', [UsuarioController::class, 'get']);
Route::post('/GetUsuarioId', [UsuarioController::class, 'getId']);
Route::post('/GetNegocioRango', [UsuarioController::class, 'getNegocioRango']);
Route::post('/GetPuntos', [UsuarioController::class, 'getPuntos']);

Route::post('/CargarFotoPerfil', [UsuarioController::class, 'insertFoto']);


Route::post('/FiltrarUsuario', [UsuarioController::class, 'Filtrar']);
Route::post('/BuscarUsuario', [UsuarioController::class, 'Find']);


// ============= NOTIFICACION ================================

Route::get('/GetNotificacion', [NotificacionCtrl::class, 'get']);
Route::post('/InsertNotificacion', [NotificacionCtrl::class, 'insert']);

Route::post('/GetNotificacionUser', [NotificacionCtrl::class, 'getUser']);
Route::post('/EditarNotificacion', [NotificacionCtrl::class, 'edit']);


// ============= BENEFICIO ================================


Route::get('/GetBeneficio', [BeneficioCtrl::class, 'get']);
Route::post('/GetBeneficioPorPunto', [BeneficioCtrl::class, 'getPorPuntos']);
Route::post('/GetBeneficioDisponible', [BeneficioCtrl::class, 'getDisponibles']);
Route::post('/GetBeneficioUtilizados', [BeneficioCtrl::class, 'getUtilizados']);
Route::post('/GetBeneficioProximos', [BeneficioCtrl::class, 'getProximos']);
Route::get('/GetBeneficioCanjeados', [BeneficioCtrl::class, 'getCanjeados']);
Route::post('/GetNegocioCanjeado', [BeneficioCtrl::class, 'getNegocioCanjeado']);


Route::post('/InsertImgBeneficio', [BeneficioCtrl::class, 'insertImg']);
Route::post('/InsertBeneficio', [BeneficioCtrl::class, 'insert']);
Route::post('/InsertBeneficioCtrl', [BeneficioCtrl::class, 'insertCtrl']);


Route::post('/VerificarBeneficioEdit', [BeneficioCtrl::class, 'VerificarEdit']);
Route::post('/EditBeneficioInt', [BeneficioCtrl::class, 'EditInt']);
Route::post('/EditBeneficioExt', [BeneficioCtrl::class, 'EditExt']);


Route::post('/ValidarCupon', [BeneficioCtrl::class, 'ValidarCupon']);
Route::post('/ValidarCredencial', [BeneficioCtrl::class, 'ValidarCredencial']);
Route::post('/Canjear', [BeneficioCtrl::class, 'Canjear']);


// ============= RANGO ================================

Route::get('/GetRango', [RangoCtrl::class, 'get']);
Route::get('/GetCliente', [RangoCtrl::class, 'getCliente']);


// ============= CHAT ================================

Route::post('/CargarChat', [ChatCtrl::class, 'getChat']);
Route::post('/CrearChat', [ChatCtrl::class, 'crearChat']);
Route::post('/Mensaje', [ChatCtrl::class, 'mensaje']);
Route::post('/CargarMensaje', [ChatCtrl::class, 'getMensaje']);

Route::get('/CargarChatAdmin', [ChatCtrl::class, 'getChatAdmin']);

Route::post('/CargarMensajeAdmin', [ChatCtrl::class, 'getMensajeAdmin']);

Route::post('/BuscarChat', [ChatCtrl::class, 'Find']);


// ============= CORREO ================================


Route::post('/EnviarCorreo', [MailController::class, 'Correo']);
Route::post('/EnviarCorreoAdmin', [MailController::class, 'CorreoAdmin']);

Route::post('/VerificarClave', [MailController::class, 'VerificarClave']);
Route::post('/VerificarClaveAdmin', [MailController::class, 'VerificarClaveAdmin']);

Route::post('/CambiarClave', [MailController::class, 'CambiarClave']);
Route::post('/CambiarClaveAdmin', [MailController::class, 'CambiarClaveAdmin']);


// ============= EXCEL ================================


Route::post('/ExportExcelUsuarios', [ExcelCtrl::class, 'ExportExcelUsuarios']);



// ============= PUSH ================================

Route::get('/a', [NotificacionCtrl::class, 'a']);
