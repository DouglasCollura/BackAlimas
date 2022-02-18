<?php
namespace App\Http\Controllers;
use App\Helpers\Api as ApiHelper;
use App\Traits\ApiController;
use App\Http\Resources\Data as Data;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class UsuarioController extends Controller
{
  use ApiController;

  public function RegistrarUsuario(Request $request){
    $datos = request();

    $cliente = DB::select("select dni, dir_cliente from ".env('DB_TABLE_SECOND').".clientes where dni = '".$datos->dni."' ");
    if(!empty($cliente[0])){

        $query = DB::select("select dni from usuario where dni = '".$datos->dni."' ");

        if(!empty($query[0])){
            return $this->sendResponse(2);
        }else{
            $mytime = Carbon::now();

            $res = DB::select("
            insert into usuario 
              (nombre_negocio, nombre_contacto, correo, dni, direccion, fecha_reg, telefono, clave, pts_canjeados) 
            values
              ( 
                '".$datos->nombre_negocio."',
                '".$datos->nombre_contacto."',
                '".$datos->correo."',
                '".$cliente[0]->dni."',
                '".$cliente[0]->dir_cliente."',
                '".$mytime->toDateString()."',
                '".$datos->telefono."',
                '".Hash::make($datos->clave)."',
                0
              )
            ");

            DB::select("
            insert into ".env('DB_TABLE_SECOND').".puntos_bonos 
              (documento, puntos_obtenidos) 
            values
              ( 
                '".$cliente[0]->dni."',
                0
              )
            ");
          
            return $this->sendResponse(1);
        }
    
    }else{
        return $this->sendResponse(0);
    }

  }

  public function RefreshUser(Request $request){
    $datos = request();
    $res = DB::select("
        select 
            a.*, b.puntos_obtenidos as puntos,
            (
                SELECT SUBSTRING_INDEX(valor, '-', 1) as valor
                FROM rango  where valor > b.puntos_obtenidos order by id asc limit 1
            ) as rango,
            (
                select id from ctrl_notificacion 
                where 
                    id_usuario = a.id  and
                    visto = 1
                limit 1
            ) as new_notificacion
        from 
            ".env('DB_TABLE_MAIN').".usuario a, ".env('DB_TABLE_SECOND').".puntos_bonos b 
        where 
            a.dni = '".$datos->dni."' and
            a.dni = b.documento
        ");
    return $this->sendResponse($res);
    
  }


  public function Login(Request $request){
    $datos = request();

    $res = DB::select("
        select 
            a.*, b.puntos_obtenidos as puntos,
            (
                SELECT SUBSTRING_INDEX(valor, '-', 1) as valor
                FROM rango  where valor > b.puntos_obtenidos order by id asc limit 1
            ) as rango,
            (
                select id from ctrl_notificacion 
                where 
                    id_usuario = a.id  and
                    visto = 1
                limit 1
            ) as new_notificacion
        from 
            ".env('DB_TABLE_MAIN').".usuario a, ".env('DB_TABLE_SECOND').".puntos_bonos b 
        where 
            a.dni = '".$datos->dni."' and
            a.dni = b.documento
        ");
    if(!empty($res[0])){

      if(Hash::check($datos->clave,$res[0]->clave)){
        if($datos->token == $res[0]->token){
            return $this->sendResponse($res);
        }else{
            DB::select("update usuario set token = '".$datos->token."' where dni = '".$datos->dni."'");
            return $this->sendResponse($res);
        }
      }else{
        return $this->sendResponse(0);
      }
    }
  }

  public function LoginAdmin(Request $request){
    $datos = request();

    $res = DB::select("select nombre, clave from administrador where correo = '".$datos->correo."'");
    if(!empty($res[0])){

      if(Hash::check($datos->clave,$res[0]->clave)){
        return $this->sendResponse($res[0]->nombre);
      }else{
        return $this->sendResponse(0);
      }
    }
  }

  public function VerificarDni(Request $request){
    $datos = request();

    $res = DB::select("select * from usuario where dni = '".$datos->dni."'");
    $cliente = DB::connection('mysql2')->select("select puntos_obtenidos from puntos_bonos where documento = '".$datos->dni."' ");
    $res[0]->{'puntos'} = $cliente[0]->puntos_obtenidos;
    if(!empty($res[0])){
        return $this->sendResponse($res);
    }else{
      return $this->sendResponse(0);
    }

  }


  public function get(){
    $res = DB::select("
        select 
            a.*, b.puntos_obtenidos
        from 
            ".env('DB_TABLE_MAIN').".usuario a, ".env('DB_TABLE_SECOND').".puntos_bonos b 
        where 
            a.dni = b.documento

        order by id desc
    ");
    return $this->sendResponse($res);
  }

  public function getId(Request $request){
    $datos = request();

    $res = DB::select("select * from usuario where id = ".$datos->id);
    return $this->sendResponse($res);
  }

  public function getNegocioRango(Request $request){
    $datos = request();
    $arr = (array) $datos;
    $where_rango = '';

    for($i = 0; $i < count($arr); $i++) {
        if($datos[$i]){
            $rangos = $datos[$i];

            if($where_rango == ''){
                if($rangos[0] == '4500'){
                    $where_rango ="puntos_obtenidos >= 4500 and a.dni = b.documento";
                }else{
                    $where_rango ="puntos_obtenidos BETWEEN ".$rangos[0]." AND ".$rangos[1]." and a.dni = b.documento";
                }
            }else{
                if($rangos[0] == '4500'){
                    $where_rango .=" OR puntos_obtenidos >= 4500 and a.dni = b.documento";
                }else{
                    $where_rango .=" OR puntos_obtenidos BETWEEN ".$rangos[0]." AND ".$rangos[1]." and a.dni = b.documento";
                }
            }   
        }else{
            continue;
        }

    }
    $cliente = DB::select("
        select 
           a.id, a.nombre_negocio, b.documento 
        from 
            ".env('DB_TABLE_MAIN').".usuario a, ".env('DB_TABLE_SECOND').".puntos_bonos b  
        where 
            ".$where_rango."
            "    
    );  

    if(!empty($cliente[0])){
        return $this->sendResponse($cliente);
    }else{
        return $this->sendResponse('nulo'); 
    }


    
    if($datos->rango_max == '5000'){
        $cliente = DB::select("
            select 
               a.id, a.nombre_negocio, b.documento 
            from 
                ".env('DB_TABLE_MAIN').".usuario a, ".env('DB_TABLE_SECOND').".puntos_bonos b  
            where 
                puntos_obtenidos >= ".$datos->rango_min." and 
                a.dni = b.documento"
        );   
    }else{
        $cliente = DB::select("
            select 
               a.id, a.nombre_negocio, b.documento 
            from 
                ".env('DB_TABLE_MAIN').".usuario a, ".env('DB_TABLE_SECOND').".puntos_bonos b  
            where 
                puntos_obtenidos BETWEEN ".$datos->rango_min." AND ".$datos->rango_max." and 
                a.dni = b.documento"    
        );        
    }


    if(!empty($cliente[0])){
        return $this->sendResponse($cliente);
    }else{
        return $this->sendResponse('nulo'); 
    }

    // if(!empty($cliente[0])){

    //   foreach ($cliente as &$doc) {
    //     array_push($arr, $doc->documento);
    //   }
    //   $res = DB::select("select id, nombre_negocio from usuario where dni in (".implode(',', $arr).")");
    //   return $this->sendResponse($res);  
    // }else{
    //   return $this->sendResponse('nulo');  
    // }

  }


    public function getPuntos(Request $request){
        $datos = request();

        $cliente = DB::connection('mysql2')->select("select puntos_obtenidos from puntos_bonos where documento = '".$datos->dni."' ");

        return $this->sendResponse($cliente);
    }

    public function insertFoto(Request $request){
    
        $datos = request();
        $imagen = $datos->file('imagen')->store('public/Users');
        $url = Storage::url($imagen);

        if($datos->tipo == 1){
            $res = DB::select("update usuario set url_img_perfil = '".$url."' where id = ".$datos->id);
        }else{
            $res = DB::select("update usuario set url_img_portada = '".$url."' where id = ".$datos->id);
        }
        
        return $this->sendResponse($url);
    }


    public function Filtrar(Request $request){
        
        $datos = request();
        
        $fecha= '';
        $rango='';

        if($datos->anio != 0){
            $fecha .='YEAR(fecha_reg) = '.$datos->anio;
        }
        if($datos->mes != 0){
            if($fecha != ''){
                $fecha.=' AND ';
            }

            $fecha .='MONTH(fecha_reg) = '.$datos->mes;
        }
        if($datos->dia != 0){
            if($fecha != ''){
                $fecha.=' AND ';
            }
            $fecha .='DAY(fecha_reg) = '.$datos->dia;
        }

        if($datos->max_rango != null){
            if($fecha != ''){
                $rango .= " AND ";
            }

            if($datos->max_rango == '5000'){
              $rango.="b.puntos_obtenidos >= ".$datos->min_rango;

            }else{
              $rango.="b.puntos_obtenidos Between ".$datos->min_rango." and ".$datos->max_rango;

            }
        }


        $res = DB::select("
            select 
                a.*, b.puntos_obtenidos  
            from 
                ".env('DB_TABLE_MAIN').".usuario a, ".env('DB_TABLE_SECOND').".puntos_bonos b 
            where 
                a.dni = b.documento 

                and ".$fecha." ".$rango."
        ");          


        return $this->sendResponse($res);

    }


    public function Find(Request $request){
        $datos = request();

        $res = DB::select("
            select 
            a.*, b.puntos_obtenidos  
        from 
            ".env('DB_TABLE_MAIN').".usuario a, ".env('DB_TABLE_SECOND').".puntos_bonos b 
        where 
            a.dni = b.documento AND
            (nombre_negocio like '%".$datos->consulta."%' OR
            nombre_contacto like '%".$datos->consulta."%')
        ");    
        return $this->sendResponse($res);
    }



}

