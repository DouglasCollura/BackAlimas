<?php
namespace App\Http\Controllers;
use App\Helpers\Api as ApiHelper;
use App\Traits\ApiController;
use App\Http\Resources\Data as Data;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Models\Firebase;
use App\Models\Push;

class NotificacionCtrl extends Controller
{
  use ApiController;

  public function Insert(Request $request){
    $datos = request();
    $mytime = Carbon::now();

    DB::select("
      insert into notificacion 
        (titulo, descripcion, fecha, rango) 
      values
        ( 
          '".$datos->titulo."',
          '".$datos->descripcion."',
          '".$mytime->toDateString()."',
          '".$datos->rango."'
        )
    ");


    $res = DB::select("
      SELECT id FROM notificacion order by id desc limit 1 
    ");

    $arr = $datos->usuarios;

    foreach ($arr as &$usuario) {
     DB::select("
      insert into ctrl_notificacion 
        (visto, fecha, id_usuario, id_notificacion) 
      values
        ( 
          1,
          '".$mytime->toDateString()."',
          ".intval($usuario).",
          ".$res[0]->id."
        )
      ");

        $token = DB::select("select token from usuario where id =".intval($usuario));

        $titulo = "Prueba Titulo";
        $mensaje = 'Descripcion notificaci贸n';
        $data = '{
        "to" : "'.$token[0]->token.'",
        "notification" : {
        "body" : "'.$datos->descripcion.'",
        "title": "'.$datos->titulo.'",
        "icon": ""}}';
        $resultado = json_decode($data);
        $new = new Firebase;
        $new->FCMs($resultado);


    }


  }

  public function get(){
    $res = DB::select("
        SELECT * from notificacion order by id desc
    ");

    return $this->sendResponse($res);

  }

  public function getUser(Request $request){
    $datos = request();

    $res = DB::select("
      SELECT 
        a.titulo, a.descripcion, b.fecha, b.id, b.visto 

      FROM 
        notificacion a, ctrl_notificacion b 

      WHERE 
        a.id = b.id_notificacion AND
        b.id_usuario = ".$datos->id."

      ORDER BY 
        visto desc, id desc
    ");

    DB::select("update ctrl_notificacion set visto = 0 where id_usuario = ".$datos->id);

    return $this->sendResponse($res);
  }


  public function edit(Request $request){
    $datos = request();

    $res = DB::select("
        UPDATE notificacion set titulo = '".$datos->titulo."', descripcion = '".$datos->descripcion."' where id = ".$datos->id."
    ");
  }

  public function a(){
    // $token = "cDNNPoqpQe2we-I5TFmmXw:APA91bE6LsAEMFPnDY3vyvycmQptsiIgZ5uFAgju_LfH8Y2UmF1oThzlQUeIA8sh29pvuwQI54NOBgD-Tr5md3eGzVZfkggqkmy-h9KN2OnVIJ0Ne7KvEIpYbHOMjMKX8ZzDf26DVbD9";
    //     $titulo = "Prueba Titulo";
    //     $mensaje = 'Descripcion notificaci贸n';
    //     $data = '{
    //     "to" : "'.$token.'",
    //     "notification" : {
    //     "body" : "'.$mensaje.'",
    //     "title": "'.$titulo.'",
    //     "icon": ""}}';
    //     $resultado = json_decode($data);
    //     $new = new Firebase;
    //     $new->FCMs($resultado);

  }


}

