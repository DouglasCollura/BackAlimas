<?php
namespace App\Http\Controllers;
use App\Helpers\Api as ApiHelper;
use App\Traits\ApiController;
use App\Http\Resources\Data as Data;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ChatCtrl extends Controller
{
  use ApiController;

 //USUARIO====================================================================
  public function getChat(Request $request){

    $datos = request();

    $res = DB::select("
      SELECT id FROM chat where id_usuario = ".$datos->id_usuario."
    ");
    
    if(!empty($res[0])){

      return $this->sendResponse($res);
    }else{
      return $this->sendResponse('nulo');
    }
  }

  public function crearChat(Request $request){

    $datos = request();
    
    $mytime = Carbon::now();

    $res = DB::select("
      insert into chat(mensaje,fecha,leido, id_usuario)

      values(
          '".$datos->mensaje."',
          '".$mytime->toDateString()."',
          1,
          ".$datos->id_usuario."
      )
    ");
    
    $consulta = DB::select("select id from chat where id_usuario = ".$datos->id_usuario);
    return $this->sendResponse($consulta);

  }

  public function getMensaje(Request $request){
    
    $datos = request();

    $consulta = DB::select("select * from mensaje where id_chat = ".$datos->id_chat);
    return $this->sendResponse($consulta);
  }

  public function mensaje(Request $request){

    $datos = request();
    $mytime = Carbon::now();
    if($datos->tipo == 0){

        $res = DB::select("
            insert into mensaje(mensaje,fecha, hora, remitente, id_chat, tipo)

            values(
              '".$datos->mensaje."',
              '".$mytime->toDateString()."',
              '".$datos->hora."',
              ".$datos->remitente.",
              ".$datos->id_chat.",
              0
            )
        ");

        DB::select("
          update chat set 
            mensaje='".substr($datos->mensaje, 0, 45)."...', 
            fecha='".$mytime->toDateString()."',
            leido= 1

          where id = ".$datos->id_chat."
          ");

    }else{
        $imagen = $datos->file('imagen')->store('public/Mensaje');
        $url = Storage::url($imagen);
        DB::select("
            insert into mensaje(mensaje,fecha, hora,remitente, id_chat, tipo)

            values(
              '".$url."',
              '".$mytime->toDateString()."',
              '".$datos->hora."',
              ".$datos->remitente.",
              ".$datos->id_chat.",
              1
            )
        ");

        DB::select("
          update chat set 
            mensaje='Imagen..', 
            fecha='".$mytime->toDateString()."',
            leido= 1

          where id = ".$datos->id_chat."
        ");

    }
  }




 //ADMINISTRADOR====================================================================

  public function getChatAdmin(){

    $consulta = DB::select("select a.*, b.nombre_negocio, b.url_img_perfil from chat a, usuario b where b.id = a.id_usuario order by leido desc");

    return $this->sendResponse($consulta);
  }

  public function getMensajeAdmin(Request $request){
    $datos = request();

    DB::select("update chat set leido = 0 where id =".$datos->id_chat."");

    return $this->getMensaje(request());
  }

  public function Find(Request $request){
    $datos = request();
    
    $res = DB::select("
        select 
            a.*, b.nombre_negocio 
        from 
            chat a, usuario b 
        where 
            b.id = a.id_usuario and
            (b.nombre_negocio like '%".$datos->consulta."%' OR
            b.nombre_contacto like '%".$datos->consulta."%')
         order by leido desc
    ");
    return $this->sendResponse($res);

  }

}

