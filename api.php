<?php
///////////////////////////////////////////////////////////
// [ CTM
///////////////////////////////////////////////////////////
//
// [::Last modi: 15.09.18 L.ey (µ~)::]
//
// - RESTFull DEMO API
//
//
///////////////////////////////////////////////////////////

   include_once './inc/C_AUTH.php';
   include_once './inc/C_CTM_API.php';

   $cauth = new C_AUTH();

   if(!isset($_SERVER['PHP_AUTH_USER']) || $cauth->authenticate() != C_AUTH_SUCCESS){
      header('WWW-Authenticate: Basic realm="CTM"');
      header($_SERVER["SERVER_PROTOCOL"].' 401 Unauthorized');
      echo 'Authorized Users Only.';
      exit;
   }else{
      header("Content-Type: application/json; charset=UTF-8");
      $ctm = new C_CTM_API();
      $ctm->start();
   }
?>
