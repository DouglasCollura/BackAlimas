<?php

namespace App\Http\Controllers;
use App\Helpers\Api as ApiHelper;
use App\Traits\ApiController;
use App\Http\Resources\Data as Data;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsuarioExport;


class ExcelCtrl extends Controller
{
    use ApiController;
   


    public function ExportExcelUsuarios(Request $request){
    	$datos = request();
     	
     	$data = array(
     	 	array(
     	 		'NOMBRE DEL NEGOCIO',"NOMBRE DEL CONTACTO","CORREO","DNI","TELEFONO","DIRECCION","FECHA DE REGISTRO", "PUNTOS"
     	 	)
     	);
     	
     	foreach ($datos->usuarios as $dato) {
     		array_push($data,
	     		array(
	     			$dato['nombre_negocio'],
	     			$dato['nombre_contacto'],
	     			$dato['correo'],
	     			$dato['dni'],
	     			$dato['telefono'],
	     			$dato['direccion'],
	     			$dato['fecha_reg'],
	     			$dato['puntos_obtenidos']
	     		)
	     	);

     	}

    	$export = new UsuarioExport($data);

    	return Excel::download($export, 'asd.xlsx');
    
    }

}
