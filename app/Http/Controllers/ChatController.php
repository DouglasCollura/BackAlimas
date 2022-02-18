<?php
namespace App\Http\Controllers;
use App\Helpers\Api as ApiHelper;
use App\Traits\ApiController;
use App\Http\Resources\Data as Data;
use Illuminate\Http\Request;
use DB;

class RangoCtrl extends Controller
{
  use ApiController;

  public function get(){

    $res = DB::select("
      SELECT * FROM rango
    ");

    return $this->sendResponse($res);

  }

  public function getCliente(){

    $res = DB::connection('mysql2')->select('select * from clientes limit 5');

    return $this->sendResponse($res);

  }



}

