<?php
//////////////////////////////////////////////////////////////////////////////////
// [ CTM_API ] !Candidate for splitting! -> C_Platform, C_Conversion, C_Connection
//////////////////////////////////////////////////////////////////////////////////
//
// [::Last modi: 15.09.18 L.ey (µ~)::]
//
//
// - RESTFull DEMO API
//
// - implements a reduced REST api
//
//
//
////////////////////////////////////////////////

include_once './inc/C_Database.php';

////////////////////////////////////////////////

const CTM_API_DEBUG   = FALSE;
   
const CTM_API_ERROR   = 0x00;
const CTM_API_SUCCESS = 0x01;

////////////////////////////////////////////////
// [ class ]
////////////////////////////////////////////////

class C_CTM_API {

////////////////////////////////////////////////
// [ start ]
////////////////////////////////////////////////
public function start(){

   if(isset($_GET['platform'])){
   
      $this->processPlatform();
      
   }else
   if(isset($_GET['conversion'])){

      $this->processConversion();
      
   }else
   if(isset($_GET['connection'])){

      $this->processConnection();
      
   }else
   if( isset($_GET['min']) || isset($_GET['max']) && isset($_GET['table'])){
   
      $this->processMinMax();
 
   }else
   if(isset($_GET['connectionby']) && isset($_GET['val'])){

      $this->showConnectionBy($_GET['connectionby'], $_GET['val']);

   }else
   if(isset($_GET['join'])){

      $this->join($_GET['join']);

   }else $this->showApi();
}
////////////////////////////////////////////////
// [ processPlatform ]
////////////////////////////////////////////////
private function processPlatform(){

   if($_GET['platform'] === 'all'){
      
      if(!isset($_GET['start'])) $start = 0;
      else                       $start = $_GET['start'];
         
      if(!isset($_GET['limit'])) $limit = 100;
      else                       $limit = $_GET['limit'];

      if(!isset($_GET['order'])) $order = "";
      else                       $order = $_GET['order'];
         
      $this->showTable('platform', $start, $limit, $order);
         
   }else{
      
      if(isset($_GET['request'])){
         switch($_GET['request']){
            case 'first':
            case 'last':
            case 'center':
            case 'score':
            case 'sales': 
            case 'conversions': $this->showPlatformField($_GET['platform'], $_GET['request']); break;                                            
            default:            $this->response(404, "Parameter is missing");
         }
      }else $this->showPlatformByName($_GET['platform']);
   }
}
////////////////////////////////////////////////
// [ processConversion ]
////////////////////////////////////////////////
private function processConversion(){

   if($_GET['conversion'] === 'all'){
      
      if(!isset($_GET['start'])) $start = 0;
      else                       $start = $_GET['start'];
         
      if(!isset($_GET['limit'])) $limit = 100;
      else                       $limit = $_GET['limit'];
         
      if(!isset($_GET['order'])) $order = "";
      else                       $order = $_GET['order'];

      $this->showTable('conversion', $start, $limit, $order);

   }else{

      if(isset($_GET['request'])){
         switch($_GET['request']){
            case 'id_customer':
            case 'id_booking':
            case 'revenue':    $this->showConversionField($_GET['conversion'], $_GET['request']); break;                                            
            default:           $this->response(404, "Parameter is missing");
         }
      }else $this->showConversionById($_GET['conversion']);
   }
}
////////////////////////////////////////////////
// [ processConnection ]
////////////////////////////////////////////////
private function processConnection(){

   if($_GET['connection'] === 'all'){
      
      if(!isset($_GET['start'])) $start = 0;
      else                       $start = $_GET['start'];
         
      if(!isset($_GET['limit'])) $limit = 100;
      else                       $limit = $_GET['limit'];
         
      if(!isset($_GET['order'])) $order = "";
      else                       $order = $_GET['order'];
         
      $this->showTable('connection', $start, $limit, $order);

   }else{

      if(isset($_GET['request'])){
         switch($_GET['request']){
            case 'id_conversion':
            case 'id_platform':
            case 'time':       $this->showConnectionField($_GET['connection'], $_GET['request']); break;                                            
            default:           $this->response(404, "Parameter is missing");
         }
      }else $this->showConnectionById($_GET['connection']);      
   }
}
////////////////////////////////////////////////
// [ processMinMax ]
////////////////////////////////////////////////
private function processMinMax(){

   if(isset($_GET['min'])){
      $amp = $_GET['min'];
      $smm = 'MIN';
      
   }else{
      $amp = $_GET['max'];
      $smm = 'MAX';
   }

   if($_GET['table'] === 'platform'){
      switch($amp){
         case 'id_platform':
         case 'first':
         case 'last':
         case 'center':
         case 'score':
         case 'sales':
         case 'conversions': $this->showMinMax($_GET['table'], $smm, $amp); break;
         default:            $this->response(404, "Parameter is missing");
      }
   }else
   if($_GET['table'] === 'conversion'){
      switch($amp){
         case 'id_conversion':
         case 'id_customer':
         case 'id_booking':
         case 'revenue':    $this->showMinMax($_GET['table'], $smm, $amp); break;                                            
         default:           $this->response(404, "Parameter is missing");
      }
   }else
   if($_GET['table'] === 'connection'){
      switch($amp){
         case 'id_connection':
         case 'id_conversion':
         case 'id_platform':
         case 'time':       $this->showMinMax($_GET['table'], $smm, $amp); break;                                            
         default:           $this->response(404, "Parameter is missing");
      }
   }
}
////////////////////////////////////////////////
// [ showTable ]
////////////////////////////////////////////////
private function showTable($table, $start, $limit, $order){

   $fResult = ERROR;
   
   $CDB = new C_Database();
   
   if($CDB->connect_errno){
      if(DEBUG) $CDB->showConnectError($CDB);
      $this->response(500, 'SQL Error');
      return($fResult);
   }
   
   $sec_start = $CDB->secure($start, false);
   $sec_limit = $CDB->secure($limit, false);
   $sec_order = $CDB->secure($order, false);
   
   if($sec_order === ''){
   
      $sql = "SELECT * ".
             "FROM   ".$table." ".
             "LIMIT  ".$sec_start.", ".$sec_limit.";";
   }else{
   
      $sql = "SELECT * ".
             "FROM     ".$table." ".
             "ORDER BY ".$order." ".
             "LIMIT    ".$sec_start.", ".$sec_limit.";"; 
   }
    
   if($qResult = $CDB->query($sql)){
      if($qResult->num_rows == 0)
         $this->response(404, 'Database is empty');
      else{
         $data = array();
         while($aResult = $qResult->fetch_assoc())
            array_push($data, $aResult);

         $qResult->free();
         $this->response(200, $data);
         $fResult = SUCCESS;
      }
   }else{
      if(DEBUG) $CDB->showSqlError($CDB, $sql); 
      $this->response(500, 'SQL Error');
   }

   $CDB->close();
   
   return($fResult);
}
////////////////////////////////////////////////
// [ showPlatformField ]
////////////////////////////////////////////////
private function showPlatformField($platform, $choice){

   $fResult = ERROR;
   
   $CDB = new C_Database();
   
   if($CDB->connect_errno){
      if(DEBUG) $CDB->showConnectError($CDB);
      $this->response(500, 'SQL Error');
      return($fResult);
   }
   
   $sql = "SELECT name, ".$choice." ".
          "FROM   platform ".
          "WHERE  name = ".$CDB->secure($platform, true).";";
          
   if($qResult = $CDB->query($sql)){
      if($qResult->num_rows == 0)
         $this->response(404, 'Platform not Found');
      else
      if($qResult->num_rows == 1){
         $aResult = $qResult->fetch_assoc();
         $data['platform'] = $aResult['name'];
         $data[$choice]    = $aResult[$choice];
         $qResult->free();
         $this->response(200, $data);
         $fResult = SUCCESS;
      }else
         $this->response(500, 'Malformed Database');
   }else{
      if(DEBUG) $CDB->showSqlError($CDB, $sql); 
      $this->response(500, 'SQL Error');
   }

   $CDB->close();
   
   return($fResult);    
}
////////////////////////////////////////////////
// [ showPlatformField ]
////////////////////////////////////////////////
private function showPlatformByName($platform){

   $fResult = ERROR;
   
   $CDB = new C_Database();
   
   if($CDB->connect_errno){
      if(DEBUG) $CDB->showConnectError($CDB);
      $this->response(500, 'SQL Error');
      return($fResult);
   }
   
   $sql = "SELECT * ".
          "FROM   platform ".
          "WHERE  name = ".$CDB->secure($platform, true).";";
          
   if($qResult = $CDB->query($sql)){
      if($qResult->num_rows == 0)
         $this->response(404, 'Platform not Found');
      else
      if($qResult->num_rows == 1){
         $aResult = $qResult->fetch_assoc();
         $qResult->free();
         $this->response(200, $aResult);
         $fResult = SUCCESS;
      }else
         $this->response(500, 'Malformed Database');
   }else{
      if(DEBUG) $CDB->showSqlError($CDB, $sql); 
      $this->response(500, 'SQL Error');
   }

   $CDB->close();
   
   return($fResult);    
}
////////////////////////////////////////////////
// [ showConversionField ]
////////////////////////////////////////////////
private function showConversionField($id_conversion, $choice){

   $fResult = ERROR;
   
   $CDB = new C_Database();
   
   if($CDB->connect_errno){
      if(DEBUG) $CDB->showConnectError($CDB);
      $this->response(500, 'SQL Error');
      return($fResult);
   }
   
   $sql = "SELECT id_conversion, ".$choice." ".
          "FROM   conversion ".
          "WHERE  id_conversion = ".$CDB->secure($id_conversion, true).";";
          
   if($qResult = $CDB->query($sql)){
      if($qResult->num_rows == 0)
         $this->response(404, 'Conversion not Found');
      else
      if($qResult->num_rows == 1){
         $aResult = $qResult->fetch_assoc();
         $data['id_conversion'] = $aResult['id_conversion'];
         $data[$choice]    = $aResult[$choice];
         $qResult->free();
         $this->response(200, $data);
         $fResult = SUCCESS;
      }else
         $this->response(500, 'Malformed Database');
   }else{
      if(DEBUG) $CDB->showSqlError($CDB, $sql); 
      $this->response(500, 'SQL Error');
   }

   $CDB->close();
   
   return($fResult);       
}
////////////////////////////////////////////////
// showConversionField ]
////////////////////////////////////////////////
private function showConversionById($id_conversion){

   $fResult = ERROR;
   
   $CDB = new C_Database();
   
   if($CDB->connect_errno){
      if(DEBUG) $CDB->showConnectError($CDB);
      $this->response(500, 'SQL Error');
      return($fResult);
   }
   
   $sql = "SELECT * ".
          "FROM   conversion ".
          "WHERE  id_conversion = ".$CDB->secure($id_conversion, true).";";
 
   if($qResult = $CDB->query($sql)){
      if($qResult->num_rows == 0)
         $this->response(404, 'Conversion not Found');
      else
      if($qResult->num_rows == 1){
         $aResult = $qResult->fetch_assoc();
         $qResult->free();
         $this->response(200, $aResult);
         $fResult = SUCCESS;
      }else
         $this->response(500, 'Malformed Database');
   }else{
      if(DEBUG) $CDB->showSqlError($CDB, $sql); 
      $this->response(500, 'SQL Error');
   }

   $CDB->close();
   
   return($fResult);       
}
////////////////////////////////////////////////
// [ showConnectionField ]
////////////////////////////////////////////////
private function showConnectionField($id_connection, $choice){

   $fResult = ERROR;
   
   $CDB = new C_Database();
   
   if($CDB->connect_errno){
      if(DEBUG) $CDB->showConnectError($CDB);
      $this->response(500, 'SQL Error');
      return($fResult);
   }
   
   $sql = "SELECT id_connection, ".$choice." ".
          "FROM   connection ".
          "WHERE  id_connection = ".$CDB->secure($id_connection, true).";";
          
   if($qResult = $CDB->query($sql)){
      if($qResult->num_rows == 0)
         $this->response(404, 'Connection not Found');
      else
      if($qResult->num_rows == 1){
         $aResult = $qResult->fetch_assoc();
         $data['id_connection'] = $aResult['id_connection'];
         $data[$choice]    = $aResult[$choice];
         $qResult->free();
         $this->response(200, $data);
         $fResult = SUCCESS;
      }else
         $this->response(500, 'Malformed Database');
   }else{
      if(DEBUG) $CDB->showSqlError($CDB, $sql); 
      $this->response(500, 'SQL Error');
   }

   $CDB->close();
   
   return($fResult);       
}
////////////////////////////////////////////////
// [ showConnectionById ]
////////////////////////////////////////////////
private function showConnectionById($id_connection){

   $fResult = ERROR;
   
   $CDB = new C_Database();
   
   if($CDB->connect_errno){
      if(DEBUG) $CDB->showConnectError($CDB);
      $this->response(500, 'SQL Error');
      return($fResult);
   }
   
   $sql = "SELECT * ".
          "FROM   connection ".
          "WHERE  id_connection = ".$CDB->secure($id_connection, true).";";

   if($qResult = $CDB->query($sql)){
      if($qResult->num_rows == 0)
         $this->response(404, 'Connection not Found');
      else
      if($qResult->num_rows == 1){
         $aResult = $qResult->fetch_assoc();
         $qResult->free();
         $this->response(200, $aResult);
         $fResult = SUCCESS;
      }else
         $this->response(500, 'Malformed Database');
   }else{
      if(DEBUG) $CDB->showSqlError($CDB, $sql); 
      $this->response(500, 'SQL Error');
   }

   $CDB->close();
   
   return($fResult);       
}
////////////////////////////////////////////////
// [ showConnectionBy ]
////////////////////////////////////////////////
private function showConnectionBy($order, $val){

   $fResult = ERROR;
   
   $CDB = new C_Database();
   
   if($CDB->connect_errno){
      if(DEBUG) $CDB->showConnectError($CDB);
      $this->response(500, 'SQL Error');
      return($fResult);
   }

   $sec_val = $CDB->secure($val, true);
   
   $sql = "SELECT * ".
          "FROM   connection ".
          "WHERE  ".$order." = ".$sec_val.";";

   if($qResult = $CDB->query($sql)){
      if($qResult->num_rows == 0)
         $this->response(404, 'Connection not Found');
      else{
         $data = array();
         while($aResult = $qResult->fetch_assoc())
            array_push($data, $aResult);

         $qResult->free();
         $this->response(200, $data);
         $fResult = SUCCESS;
      }
   }else{
      if(DEBUG) $CDB->showSqlError($CDB, $sql); 
      $this->response(500, 'SQL Error');
   }

   $CDB->close();
   
   return($fResult);       
}
////////////////////////////////////////////////
// [ showMinMax ]
////////////////////////////////////////////////
private function showMinMax($table, $order, $choice){

   $fResult = ERROR;

   $CDB = new C_Database();
   
   if($CDB->connect_errno){
      if(DEBUG) $CDB->showConnectError($CDB);
      $this->response(500, 'SQL Error');
      return($fResult);
   }

   $sql = "SELECT * ".
          "FROM   ".$table." ".
          "WHERE  ".$choice." = (SELECT IFNULL(".$order."(".$choice."), 0) ".
                                "FROM   ".$table.")";
                       
   if(!$qResult = $CDB->query($sql)){
      if(DEBUG) $CDB->showSqlError($Cmysqli, $sql);
      $this->response(500, 'SQL Error');
   }

   if($qResult->num_rows == 0)
      $this->response(404, 'Database is empty');
   else{
      $aResult = $qResult->fetch_assoc();
      $this->response(200, $aResult);
      $qResult->free();
      $fResult = SUCCESS;
   }

   $CDB->close();
   
   return($fResult);
}
////////////////////////////////////////////////
// [ join ]
////////////////////////////////////////////////
private function join($id_platform){

   $fResult = ERROR;
   
   $CDB = new C_Database();
   
   if($CDB->connect_errno){
      if(DEBUG) $CDB->showConnectError($CDB);
      $this->response(500, 'SQL Error');
      return($fResult);
   }

   $sec_id_platform = $CDB->secure($id_platform, true);
   
   $sql = "SELECT     conversion.* ".
          "FROM       conversion ".
          "INNER JOIN connection ".
          "ON         conversion.id_conversion = connection.id_conversion ".
          "WHERE      connection.id_platform = ".$sec_id_platform." ".
          "GROUP BY   conversion.id_conversion DESC ".
          "LIMIT      0, 10;"; 

   if($qResult = $CDB->query($sql)){
      if($qResult->num_rows == 0)
         $this->response(404, 'Database is empty');
      else{
         $data = array();
         while($aResult = $qResult->fetch_assoc())
            array_push($data, $aResult);

         $qResult->free();
         $this->response(200, $data);
         $fResult = SUCCESS;
         
      }
   }else{
      if(DEBUG) $CDB->showSqlError($CDB, $sql); 
      $this->response(500, 'SQL Error');
   }

   $CDB->close();
}
////////////////////////////////////////////////
// [ showApi ]
////////////////////////////////////////////////
private function showApi(){

  $fResult = ERROR;

   header("Content-Type: application/json; charset=UTF-8");
   
   $getdata = '/get/';
   
   $get_max = $getdata.'max/platform/';
   $get_min = $getdata.'min/platform/';
   
   $aLinks = array("first", 'last', 'center', 'score', 'sales', 'conversions');
   $cLinks = count($aLinks);
   $nLinks = 0;
   
   for($nLinks; $nLinks < $cLinks; $nLinks++){
      $data[$nLinks]['link'] = $get_max.$aLinks[$nLinks];
   }
   
   for($nLinks, $nLinks2 = 0; $nLinks < $cLinks * 2; $nLinks++, $nLinks2++){
      $data[$nLinks]['link'] = $get_min.$aLinks[$nLinks2];
   }
   
   /////////////////////////////////////////
   
   $data[$nLinks++]['link'] = $getdata.'platform';
   
   $data[$nLinks++]['link'] = $getdata.'conversion';
   $data[$nLinks++]['link'] = $getdata.'conversion/1';
   
   $data[$nLinks++]['link'] = $getdata.'connection';
   $data[$nLinks++]['link'] = $getdata.'connection/1';
   
   /////////////////////////////////////////
   
   $CDB = new C_Database();
   
   if($CDB->connect_errno){
      if(DEBUG) $CDB->showConnectError($CDB);
      $this->response(500, 'SQL Error');
      return($fResult);
   }

   $sql = "SELECT name ".
          "FROM   platform;";
   
   if($qResult = $CDB->query($sql)){
   
      if($qResult->num_rows){
         while($aResult = $qResult->fetch_assoc()){
            $data[$nLinks++]['link'] = $getdata.'platform/'.$aResult['name'];
         }

         $qResult->free();
         $this->response(200, $data);
         $fResult = SUCCESS;
      }
   
   }else{
      if(DEBUG) $CDB->showSqlError($CDB, $sql); 
      $this->response(500, 'SQL Error');
   }
   
   $CDB->close();
   
   return($fResult); 
}
////////////////////////////////////////////////
// [ response ]
////////////////////////////////////////////////
private function response($status, $result){
   header($_SERVER["SERVER_PROTOCOL"]." ".$status); 
   $response['status'] = $status;
   $response['result'] = $result;
   echo json_encode($response);
}
////////////////////////////////////////////////
//
////////////////////////////////////////////////
}
?>
