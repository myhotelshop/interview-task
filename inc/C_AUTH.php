<?php
//////////////////////////////////////////////////////////////////////////////////
// [ AUTH ]
//////////////////////////////////////////////////////////////////////////////////
//
// [::Last modi: 09.09.18 L.ey (µ~)::]
//
// 
// - Basic HTTP Authentication
// - Use Table CTM.customer
// - Ex. Entry [id_customer][name][pass_hash]
//
////////////////////////////////////////////////

include_once './inc/C_Database.php';

////////////////////////////////////////////////

const C_AUTH_DEBUG   = 0x01;
   
const C_AUTH_ERROR   = 0x00;
const C_AUTH_SUCCESS = 0x01;

////////////////////////////////////////////////
//
////////////////////////////////////////////////

class C_AUTH {

////////////////////////////////////////////////
// authenticate
////////////////////////////////////////////////
public function authenticate(){

   $fResult = C_AUTH_ERROR;

   $CDB = new C_Database();
   
   if($CDB->connect_errno){
      if(C_AUTH_DEBUG) $CDB->showConnectError();
      return($fResult);
   }

   $sql = "SELECT pass_hash ".
          "FROM   customer ".
          "WHERE  name = ".$CDB->secure($_SERVER['PHP_AUTH_USER'], true).";";
   
   if($qResult = $CDB->query($sql)){
   
      if($qResult->num_rows == 1){
         $aResult = $qResult->fetch_assoc();
         $qResult->free();

         if(password_verify($_SERVER['PHP_AUTH_PW'], $aResult['pass_hash']))
            $fResult = C_AUTH_SUCCESS;
      }
      
   }else if(C_AUTH_DEBUG) $CDB->showSqlError($sql);

   $CDB->close();

   return($fResult);
}
////////////////////////////////////////////////
//
////////////////////////////////////////////////
}
