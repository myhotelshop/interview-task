<?php
//////////////////////////////////////////////////////////////////////////////////
// [ CTM
//////////////////////////////////////////////////////////////////////////////////
//
// [::Last modi: 09.09.18 L.ey (µ~)::]
//
// - process cookie[mhs-tracking]
// - calculate score points
// - update database CTM
//
   //$start = microtime(TRUE);

   include_once './inc/C_CTM.php';
   
   ////////////////////////////////////////////////

   $ctm = new C_CTM();
   
   $result = $ctm->start();

   header("Content-Type: application/json; charset=UTF-8");
   header($_SERVER["SERVER_PROTOCOL"]." 200");

   $response['status'] = 200;
   $response['result'] = $result;
   
   if(C_CTM_DEBUG) $response['debug'] = $ctm->getError();

   echo json_encode($response);
   
   //$stop = microtime(TRUE);
   //$time = $stop - $start;
   //echo 'zeit: '.$time.'<br/>';
   
?>
