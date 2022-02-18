<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Firebase extends Model
{
    
    use HasFactory;
    /**
    * Sending push message to single user by Firebase Registration ID
    * @param $to
    * @param $message
    *
    * @return bool|string
    */
   public function send( $to, $message ) {

    $fields = array(
       'to'   => $to,
       'data' => $message,
    );

    return $this->sendPushNotification( $fields );
 }
 
 
 
 
 public function FCMs($data){
     // Set POST variables
    $url = 'https://fcm.googleapis.com/fcm/send';
     

    // AAAA1YmUDTo:APA91bEuBOOM_rkhOAR3C2xdI9kBYjid8vAH495P5_qZMb8MUKUkL82_yeplU0kisSJpIloqWyCDOenVjBBaxzCD7btZR-w0VJ0y50cCWUMFXjTqqC_E7a6sQIEwH6ZIWMCwouZzMCW5
    
    $headers = array(
       'Authorization: key=AAAAQfL_xqE:APA91bGt1UfTkoBMt5vKSSFn_Aezl3Nuvqot1fVTv8-lngj2reueQ4cRWj4cSv8k7n1hqhIozSfv2NLVKU5TLlXkNOA5-4WGZdn_vmPaw1KIuLtHjc68k7byKdVqShb55fVDE6DK5GqP',
       'Content-Type: application/json'
    );
    // Open connection
    $ch = curl_init();

    // Set the url, number of POST vars, POST data
    curl_setopt( $ch, CURLOPT_URL, $url );

    curl_setopt( $ch, CURLOPT_POST, true );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

    // Disabling SSL Certificate support temporarly
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );

    curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $data ) );

    // Execute post
    $result = curl_exec( $ch );
    if ( $result === false ) {
       die( 'Curl failed: ' . curl_error( $ch ) );
    }

    // Close connection
    curl_close( $ch );

    return $result;
 }


 /**
  * Sending message to a topic by topic name
  * @param $to
  * @param $message
  *
  * @return bool|string
  */
 public function sendToTopic( $to, $message ) {
    $fields = array(
       'to'   => '/topics/' . $to,
       'data' => $message,
    );

    return $this->sendPushNotification( $fields );
 }


 /**
  * Sending push message to multiple users by firebase registration ids
  * @param $registration_ids
  * @param $message
  *
  * @return bool|string
  */
 public function sendMultiple( $registration_ids, $message ) {
    $fields = array(
       'to'   => $registration_ids,
       'data' => $message,
    );

    return $this->sendPushNotification( $fields );
 }

 /**
  * CURL request to firebase servers
  * @param $fields
  *
  * @return bool|string
  */
 private function sendPushNotification( $fields ) {

    // Set POST variables
    $url = 'https://fcm.googleapis.com/fcm/send';

    $headers = array(
       'Authorization: key=AAAA1YmUDTo:APA91bEuBOOM_rkhOAR3C2xdI9kBYjid8vAH495P5_qZMb8MUKUkL82_yeplU0kisSJpIloqWyCDOenVjBBaxzCD7btZR-w0VJ0y50cCWUMFXjTqqC_E7a6sQIEwH6ZIWMCwouZzMCW5',
       'Content-Type: application/json'
    );
    // Open connection
    $ch = curl_init();

    // Set the url, number of POST vars, POST data
    curl_setopt( $ch, CURLOPT_URL, $url );

    curl_setopt( $ch, CURLOPT_POST, true );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

    // Disabling SSL Certificate support temporarly
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );

    curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields ) );

    // Execute post
    $result = curl_exec( $ch );
    if ( $result === false ) {
       die( 'Curl failed: ' . curl_error( $ch ) );
    }

    // Close connection
    curl_close( $ch );

    return $result;
 }
}
