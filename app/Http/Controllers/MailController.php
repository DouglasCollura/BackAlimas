<?php

namespace App\Http\Controllers;
use App\Helpers\Api as ApiHelper;
use App\Traits\ApiController;
use App\Http\Resources\Data as Data;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\Correo;
use DB;

class MailController extends Controller
{
   use ApiController;

   	public function Correo(Request $request){
	    	$datos = request();
	    	$res = DB::select("select id, dni, nombre_contacto from usuario where correo = '".$datos->correo."'");

	    	if(!empty($res[0])){
	    		
	    		$clave = $this->GenerarClave($res);


	    		DB::select("update usuario set recovery_pass = '".$clave."' where id =".$res[0]->id);

	    		$obj =array('id' => $res[0]->id);
	    		

	    		$contenido = (object) array('clave' => $clave, 'nombre' => $res[0]->nombre_contacto);
	   	   		
	   	   		Mail::to($datos->correo)->send(new Correo($contenido));

	        	return $this->sendResponse($obj);

	    	}else{
	    		return 0;
	    	}
   	}

   	public function CorreoAdmin(Request $request){
	    	$datos = request();
	    	$res = DB::select("select id, nombre as nombre_contacto, correo as dni from administrador where correo = '".$datos->correo."'");

	    	if(!empty($res[0])){
	    		
	    		$clave = $this->GenerarClave($res);


	    		DB::select("update administrador set recovery_pass = '".$clave."' where id =".$res[0]->id);

	    		$obj =array('id' => $res[0]->id);
	    		

	    		$contenido = (object) array('clave' => $clave, 'nombre' => $res[0]->nombre_contacto);
	   	   		
	   	   		Mail::to($datos->correo)->send(new Correo($contenido));

	        	return $this->sendResponse($obj);

	    	}else{
	    		return 0;
	    	}
   	}

   	public function VerificarClave(Request $request){
	    	$datos = request();
	    	$res = DB::select("select id from usuario where id = ".$datos->id." and recovery_pass = '".$datos->clave."' ");

	    	if(!empty($res[0])){
	    		return 1;
	    	}else{
	    		return 0;
	    	}
   	}

   	public function VerificarClaveAdmin(Request $request){
	    	$datos = request();
	    	$res = DB::select("select id from administrador where id = ".$datos->id." and recovery_pass = '".$datos->clave."' ");

	    	if(!empty($res[0])){
	    		return 1;
	    	}else{
	    		return 0;
	    	}
   	}

   	public function CambiarClave(Request $request){
    	$datos = request();

    	$res = DB::select("update usuario set clave= '".Hash::make($datos->clave)."' where id= ".$datos->id);

   	}

   	public function CambiarClaveAdmin(Request $request){
    	$datos = request();

    	$res = DB::select("update administrador set clave= '".Hash::make($datos->clave)."' where id= ".$datos->id);

   	}

   	public function GenerarClave($res){
   		
   		$random='#/_41209#8Qqp{Zz}mXx';
		$clave=substr(strtoupper($res[0]->nombre_contacto), 0, 3);
		$clave.=substr($res[0]->dni, 0, 3);
		
		for ($i = 0; $i < 6; $i++) {
			$clave .= $random[rand(0, 19)];
		}
		return $clave;
   	} 
}