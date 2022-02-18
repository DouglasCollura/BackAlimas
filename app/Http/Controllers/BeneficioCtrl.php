<?php
namespace App\Http\Controllers;
use App\Helpers\Api as ApiHelper;
use App\Traits\ApiController;
use App\Http\Resources\Data as Data;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BeneficioCtrl extends Controller
{
    use ApiController;

    public function get(){
        $sql = DB::select("
            SELECT * from beneficio order by id desc 
        ");   

        return $this->sendResponse($sql);

    }

    public function insertImg(Request $request){

        $imagen = $request->file('imagen')->store('public/');
        $url = Storage::url($imagen);
        
        return $this->sendResponse($url);

    }

    public function insert(Request $request){
        $datos = request();

        $columna='';
        $dato="";

        if(empty($datos->descuento)){
            $columna = 'precio';
            $dato = "'".$datos->precio."'";
        }else{
            $columna = 'descuento';
            $dato = $datos->descuento;
        }

        DB::select("
            insert into beneficio(
                tipo, 
                titulo,
                nombre_negocio, 
                tipo_negocio, 
                direccion, 
                telefono,
                fecha_desde,
                fecha_hasta,
                descripcion,
                puntos,
                ".$columna.",
                url_img,
                cod_qr,
                acc_nombre,
                acc_dni,
                id_rango  
            ) 
            values
            ( 
                ".$datos->tipo.",
                '".$datos->titulo."',
                '".$datos->nombre_negocio."',
                '".$datos->tipo_negocio."',
                '".$datos->direccion."',
                '".$datos->telefono."',
                '".$datos->fecha_desde."',
                '".$datos->fecha_hasta."',
                '".$datos->descripcion."',
                ".$datos->puntos.",
                ".$dato.",
                '".$datos->url_img."',
                '0',
                '".$datos->acc_nombre."',
                '".$datos->acc_dni."',
                '".$datos->id_rango."'
            )
        ");

        $sql = DB::select("
            SELECT id from beneficio order by id desc limit 1; 
        ");

        $cod_qr = 'cod_qr'.$sql[0]->id;

        DB::select("
            update beneficio set cod_qr = '".$sql[0]->id."' where id = ".$sql[0]->id."
        ");

        return $this->sendResponse($cod_qr);
    }

    public function getCanjeados(){
        $sql = DB::select("
            SELECT
                a.id, a.titulo, a.nombre_negocio, a.descuento, a.puntos, a.url_img,
                
                (select count(id) from ctrl_beneficio where id_beneficio = a.id ) as total, 
                
                (select count( DISTINCT id_usuario) from ctrl_beneficio where id_beneficio = a.id) as total_empresas
    
            FROM 
                beneficio a
        ");
        return $this->sendResponse($sql);

    }

    public function getNegocioCanjeado(){

        $datos = request();

        $sql = DB::select("

            SELECT DISTINCT 
                b.nombre_negocio, b.url_img_perfil, c.puntos_obtenidos
            FROM 
                ".env('DB_TABLE_MAIN').".ctrl_beneficio a, 
                ".env('DB_TABLE_MAIN').".usuario b,
                ".env('DB_TABLE_SECOND').".puntos_bonos c
            WHERE 
                a.id_beneficio = ".$datos->id." and b.id = a.id_usuario  and b.dni = c.documento
            group by a.id, b.nombre_negocio, b.url_img_perfil, c.puntos_obtenidos
        ");

        return $this->sendResponse($sql);

    }

    public function VerificarEdit(Request $request){
        $datos = request();

        $sql = DB::select("
            SELECT
                id
            from 
                ctrl_beneficio 
            where 
                id_beneficio = ".$datos->id."  
            limit 1
        ");

        return $this->sendResponse($sql);
    }

    public function EditInt(Request $request){
        $datos = request();

        $columna='';
        $nullcolumna="";
        $dato="";

        if(empty($datos->descuento)){
            $columna = 'precio';
            $nullcolumna = 'descuento';
            $dato = "'".$datos->precio."'";
        }else{
            $columna = 'descuento';
            $nullcolumna = 'precio';
            $dato = $datos->descuento;
        }

        $res = DB::select("
            update beneficio 

            set 
                titulo = '".$datos->titulo."', 
                descripcion = '".$datos->descripcion."',
                fecha_desde = '".$datos->fecha_desde."',
                fecha_hasta = '".$datos->fecha_hasta."',
                puntos = '".$datos->puntos."',
                id_rango = '".$datos->id_rango."',
                ".$columna." = ".$dato.",
                ".$nullcolumna." = null

            where 
                id = ".$datos->id."
        ");

    }

    public function EditExt(Request $request){
        $datos = request();

        $columna='';
        $nullcolumna="";
        $dato="";

        if(empty($datos->descuento)){
            $columna = 'precio';
            $nullcolumna = 'descuento';
            $dato = "'".$datos->precio."'";
        }else{
            $columna = 'descuento';
            $nullcolumna = 'precio';
            $dato = $datos->descuento;
        }

        $res = DB::select("
            update beneficio 

            set 
                titulo = '".$datos->titulo."', 
                descripcion = '".$datos->descripcion."',
                fecha_desde = '".$datos->fecha_desde."',
                fecha_hasta = '".$datos->fecha_hasta."',
                puntos = '".$datos->puntos."',
                id_rango = '".$datos->id_rango."',
                ".$columna." = ".$dato.",
                ".$nullcolumna." = null,
                acc_nombre = '".$datos->acc_nombre."',
                acc_dni = '".$datos->acc_dni."',
                nombre_negocio = '".$datos->nombre_negocio."', 
                tipo_negocio  = '".$datos->tipo_negocio."',
                direccion  = '".$datos->direccion."',
                telefono  = '".$datos->telefono."'
            where 
                id = ".$datos->id."
        ");

    }


    //USUARIO=======================================


    public function getPorPuntos(Request $request){
        $datos = request();
        $mytime = Carbon::now();

        $sql = DB::select("
            SELECT 
                * 
            from 
                beneficio 
            where 
                puntos <= ".$datos->puntos." and 
                fecha_desde <= '".$mytime->toDateString()."' and 
                fecha_hasta > '".$mytime->toDateString()."' and 
                id_rango like CONCAT('%',(SELECT id FROM rango where valor like '%-".$datos->rango."'),'%')
        ");

        return $this->sendResponse($sql);

    }


    public function insertCtrl(Request $request){
        $datos = request();

        DB::select("
            insert into ctrl_beneficio(
                cod_qr,
                canjeado,
                fecha_hasta,
                id_beneficio,
                id_usuario    
            ) 
            values
            ( 
                '0',
                0,
                '".$datos->fecha_hasta."',
                ".$datos->id_beneficio.",
                ".$datos->id_usuario."
            )
        ");

        $sql = DB::select("
            SELECT id from ctrl_beneficio order by id desc limit 1; 
        ");

        $random='_41-2AaBbQqWw(09)@876QqpZzmXx';
        $clave='';
        
        for ($i = 0; $i < 6; $i++) {
            $clave .= $random[rand(0, 28)];
        }

        $cod_qr = $clave.$datos->cod_qr."!".$sql[0]->id;

        DB::select("
            update ctrl_beneficio set cod_qr = '".$cod_qr."' where id = ".$sql[0]->id."
        ");

        DB::select("
            update usuario set pts_canjeados = pts_canjeados + ".$datos->cupon_puntos." where id = ".$datos->id_usuario."
        ");

        DB::connection('mysql2')->select("update puntos_bonos set puntos_obtenidos = ".$datos->puntos." where documento = ".$datos->documento);

        return $this->sendResponse($cod_qr);
    }

    public function getDisponibles(Request $request){
        $datos = request();
        $mytime = Carbon::now();

        $sql = DB::select("
            SELECT 
                a.id, a.canjeado, a.cod_qr, a.fecha_hasta, b.nombre_negocio, b.tipo_negocio, b.puntos, b.descuento, b.telefono, b.direccion, b.url_img 
            FROM 
                ctrl_beneficio a, beneficio b 
            WHERE 
                a.id_usuario = ".$datos->id." AND 
                a.canjeado = 0  AND a.id_beneficio = b.id AND 
                b.fecha_hasta > '".$mytime->toDateString()."'  order by id desc
        ");
        
        return $this->sendResponse($sql);
    }

    public function getProximos(Request $request){
       
        $datos = request();
        $mytime = Carbon::now();

        $sql = DB::select("

            SELECT 
                * 
            FROM 
                beneficio 
            WHERE 
                id_rango = (SELECT id FROM rango where valor like '".$datos->rango."-%') 
            OR 
                fecha_desde > '".$mytime->toDateString()."' and 
                id_rango like CONCAT('%',(SELECT id FROM rango where valor like '%-".$datos->rango."'),'%')
            OR 
            id_rango like CONCAT('%',(SELECT id FROM rango where valor like '%-".$datos->rango."'),'%') AND puntos > ".$datos->puntos."


            ");

        return $this->sendResponse($sql);

    }
    
    public function getUtilizados(Request $request){
        $datos = request();
        $sql = DB::select("
            SELECT a.id, a.canjeado, a.cod_qr, a.fecha_hasta, b.nombre_negocio, b.tipo_negocio, b.puntos, b.descuento, b.telefono, b.direccion, b.url_img FROM ctrl_beneficio a, beneficio b WHERE a.id_usuario = ".$datos->id." AND a.canjeado = 1  AND a.id_beneficio = b.id order by id desc
        ");

        return $this->sendResponse($sql);
        
    }


    public function ValidarCupon(Request $request){
        $datos = request();

        $sql = DB::select("
            SELECT canjeado from ctrl_beneficio where cod_qr = '".$datos->codigo."'
        ");
        
        if(!empty($sql[0])){
            if($sql[0]->canjeado == 0){
                return $this->sendResponse(1);
            }else{
                return $this->sendResponse(2);
            }
        }else{
            return $this->sendResponse(0);
        }
    }


    public function ValidarCredencial(Request $request){
        $datos = request();

        $sql = DB::select("
                select a.id 

                from 
                    ctrl_beneficio a, beneficio b
                where
                    a.cod_qr ='".$datos->codigo."' and 
                    b.acc_nombre = '".$datos->negocio."' and 
                    b.acc_dni = '".$datos->dni."'

            ");

        if(!empty($sql[0])){
            $sql2 = DB::select("
                SELECT 
                    a.cod_qr, a.fecha_hasta,
                    b.descuento, b.nombre_negocio,
                    b.direccion, b.telefono, a.id, b.url_img

                FROM 
                    ctrl_beneficio a,
                    beneficio b
                    
                WHERE 
                    a.cod_qr ='".$datos->codigo."' and 
                    b.acc_nombre = '".$datos->negocio."' and 
                    b.acc_dni = '".$datos->dni."'
            ");

            return $this->sendResponse($sql2);

        }else{
            return $this->sendResponse(0);
        }
    }

    public function Canjear(Request $request){
        $datos = request();

        $sql = DB::select("
            UPDATE ctrl_beneficio set canjeado = 1 where id = ".$datos->id."
        ");
    }



}
