<?php
//////////////////////////////////////////////////////////////////////////////////
// [ Cookie - Collector 0.1 ]
//////////////////////////////////////////////////////////////////////////////////
//
// [::Last modi: 19.08.18 L.ey (µ~)::]
//
// - process cookie[mhs-tracking]
// - calculate score points
// - update database CTM
//
////////////////////////////////////////////////

include_once './inc/C_Database.php';

////////////////////////////////////////////////

const C_CTM_DEBUG   = TRUE;
   
const C_CTM_ERROR   = 0x00;
const C_CTM_SUCCESS = 0x01;
   
const C_CTM_NAME    = 0x00;
const C_CTM_ID      = 0x01;
const C_CTM_POINTS  = 0x02;
const C_CTM_COUNT   = 0x03;
const C_CTM_FIRST   = 0x04;
const C_CTM_LAST    = 0x05;
const C_CTM_CENTER  = 0x06;

////////////////////////////////////////////////
//
////////////////////////////////////////////////

class C_CTM {

private $aJson  = array();
private $aScore = array();
private $aName  = array();
private $csql;
private $id_conv;
private $sError = 'SUCCESS';

////////////////////////////////////////////////

public function getError(){return($this->sError);}

////////////////////////////////////////////////
// start
////////////////////////////////////////////////
public function start(){

   // Check the arguments
   if($this->checkArguments() == C_CTM_ERROR) return(C_CTM_ERROR);

   //////////////////////////////////////////////////////////////////////////////////
   // Check the cookie

   if($this->checkCookie() == C_CTM_ERROR) return(C_CTM_ERROR);

   /////////////////////////////////////////////////////////
   // create $this->aScore 
   //
   //  ex: $aScore[Platform][C_CTM_NAME]   = name of the platform
   //      $aScore[Platform][C_CTM_POINTS] = new points for this platform
   //      $aScore[Platform][C_CTM_COUNT]  = counter for this platform 
   //      $aScore[Platform][C_CTM_ID]     = id of the platform
   //      $aScore[Platform][C_CTM_FIRST]  = 1 if first  in chain else 0
   //      $aScore[Platform][C_CTM_LAST]   = 1 if last   in chain else 0
   //      $aScore[Platform][C_CTM_CENTER] = 1 if center in chain else 0
   
   // create an array in $this->aScore for every unique platform in this conversion
   
   foreach($this->aJson['placements'] as $item){
   
      $bIn = FALSE;
      
      for($nScore = 0; $nScore < count($this->aScore); $nScore++){

         if(!array_key_exists('platform', $item)){
            if(C_CTM_DEBUG) $this->sError = 'malformed json data';
            return(C_CTM_ERROR);
         }
         
         if($item['platform'] === $this->aScore[$nScore][C_CTM_NAME]){
            $bIn = TRUE;
            break;
         }
      }
      
      if($bIn == FALSE)
         array_push($this->aScore, array($item['platform'], 0, 0, 0, 0, 0, 0));
   }

   ///////////////////////////////////////////////////////////////////////
   // calculate the points for all unique platforms

   if($this->calcScore() == C_CTM_ERROR) return(C_CTM_ERROR);

   /////////////////////////////////////////////////////////////////////////////////////////
   // Now we have calculated our score points and can save everything in to the Database

   $fResult = C_CTM_ERROR;
   
   $this->csql = new C_Database();
   
   if($this->csql->connect_errno){
      if(C_CTM_DEBUG) $this->csql->showConnectError();
      return(C_CTM_ERROR);
   }

   ////////////////////////////////////////////////////////////////////

   if($this->updatePlatform()   == C_CTM_ERROR) return(C_CTM_ERROR);
   if($this->updateConversion() == C_CTM_ERROR) return(C_CTM_ERROR);
   if($this->updateConnection() == C_CTM_ERROR) return(C_CTM_ERROR);
 
   ////////////////////////////////////////////////////////////////////
   // Clean up

   $this->csql->close();

   return(C_CTM_SUCCESS);
}
////////////////////////////////////////////////
// checkArguments
////////////////////////////////////////////////
private function checkArguments(){

   if(!isset($_GET['customerId'])    || 
      !isset($_GET['bookingNumber']) || 
      !isset($_GET['revenue'])){
      if(C_CTM_DEBUG) $this->sError = 'argument is missing';
      return(C_CTM_ERROR);
   }
   
   if($_GET['customerId'] != 123){
      if(C_CTM_DEBUG) $this->sError = 'Do Nothing if customerId != 123';
      return(C_CTM_ERROR);
   }

   if(!is_numeric($_GET['revenue'])    || 
      !is_numeric($_GET['customerId']) || 
      !is_numeric($_GET['bookingNumber'])){
      if(C_CTM_DEBUG) $this->sError = 'argument is not numeric';
      return(C_CTM_ERROR);
   }
   
   if($_GET['revenue']       <= 0 || 
      $_GET['customerId']    <= 0 || 
      $_GET['bookingNumber'] <= 0){
      if(C_CTM_DEBUG) $this->sError = 'argument <= 0';
      return(C_CTM_ERROR);
   }
   
   return(C_CTM_SUCCESS);
}
////////////////////////////////////////////////
// checkCookie
////////////////////////////////////////////////
private function checkCookie(){

   if(!isset($_COOKIE['mhs-tracking'])){
      if(C_CTM_DEBUG) $this->sError = 'cant find mhs-tracking cookie';
      return(C_CTM_ERROR);
   }
   
   if(!$this->aJson = json_decode($_COOKIE['mhs-tracking'], true, 4)){
      if(C_CTM_DEBUG) $this->sError = 'malformed json';
      return(C_CTM_ERROR);
   }
   
   if(!array_key_exists('placements', $this->aJson)){
      if(C_CTM_DEBUG) $this->sError = 'malformed json data';
      return(C_CTM_ERROR);
   }
   
   if(!count($this->aJson['placements'])){
      if(C_CTM_DEBUG) $this->sError = 'malformed json data';
      return(C_CTM_ERROR);
   }

   return(C_CTM_SUCCESS);
}
////////////////////////////////////////////////
// calcScore
////////////////////////////////////////////////
private function calcScore(){

/*
calculate the new points for all unique platforms

case 1: User has contact with 1 placement and buy immediately.
        This is the best variant. platform get weight[0] points.

case 2: User has contact with 2 placements.
        If only 1 platform is connected, then this platform get weight[1] points.
        If 2 platforms are connected, first get weight[4] and last get weight[5] points.

case 3: User has contact with 3 placements.
        If only 1 platform is connected, then this platform get weight[2] points.
        If 2 or 3 platforms are connected, first get weight[4], center get weight[6] and last get weight[5] points.
        If 1 platform has several contacts, sum up the points

case 4: User has contact with 4 or more placements.
        If only 1 platform is connected, then this platform get also weight[3] points.
        Maximal 3 placements are counted per platform. First and Last are counted in any case.
        simplifying ex: 
        placements = (trivago, tripadvisor, trivago, kayak, trivago, trivago)
        result: trivago get 12 points (First = 7 + Last = 4 + Center = 1), 
                tripadvisor and Kayak get 1 point
*/

   $weight = array(16, 15, 14, 13, 7, 4, 1);
   
   $cPlacements = count($this->aJson['placements']);
   $cScore      = count($this->aScore);

   switch($cPlacements){

      case 1: 
      
         $this->aScore[0][C_CTM_POINTS] = $weight[0];
         $this->aScore[0][C_CTM_FIRST]  = 1;
         $this->aScore[0][C_CTM_LAST]   = 1;
         $this->aScore[0][C_CTM_CENTER] = 1;
         break;

      case 2: 
      
         if($cScore == 1){
            $this->aScore[0][C_CTM_POINTS] = $weight[1];
            $this->aScore[0][C_CTM_FIRST]  = 1;
            $this->aScore[0][C_CTM_LAST]   = 1;
            
         }else{
            $this->aScore[0][C_CTM_POINTS] = $weight[4];
            $this->aScore[0][C_CTM_FIRST]  = 1;
            
            $this->aScore[1][C_CTM_POINTS] = $weight[5];
            $this->aScore[1][C_CTM_LAST]   = 1;
         }    
         break;

      case 3:
      
         if($cScore == 1){
            $this->aScore[0][C_CTM_POINTS] = $weight[2];
            $this->aScore[0][C_CTM_FIRST]  = 1;
            $this->aScore[0][C_CTM_LAST]   = 1;
         }else
            for($nPlacement = 0; $nPlacement < $cPlacements; $nPlacement++)
               for($nScore = 0; $nScore < $cScore; $nScore++)
                  if($this->aScore[$nScore][C_CTM_NAME] === $this->aJson['placements'][$nPlacement]['platform'])
                     switch($nPlacement){
                        case 0: $this->aScore[$nScore][C_CTM_POINTS] += $weight[4];
                                $this->aScore[$nScore][C_CTM_FIRST]   = 1;
                                break; // First
                        case 1: $this->aScore[$nScore][C_CTM_POINTS] += $weight[6]; 
                                $this->aScore[$nScore][C_CTM_CENTER]  = 1;
                                break; // Center
                        case 2: $this->aScore[$nScore][C_CTM_POINTS] += $weight[5]; 
                                $this->aScore[$nScore][C_CTM_LAST]    = 1;
                                break; // Last
                     }
         break;

     default: 
     
         if($cScore == 1){
            $this->aScore[0][C_CTM_POINTS] = $weight[3];
            $this->aScore[0][C_CTM_FIRST]  = 1;
            $this->aScore[0][C_CTM_LAST]   = 1;
            $this->aScore[0][C_CTM_CENTER] = 1;
         }else{

            for($nScore = 0; $nScore < $cScore; $nScore++){
               // get First
               if($this->aScore[$nScore][C_CTM_NAME] === $this->aJson['placements'][0]['platform']){
                  $this->aScore[$nScore][C_CTM_POINTS] += $weight[4];
                  $this->aScore[$nScore][C_CTM_COUNT]++;
                  $this->aScore[$nScore][C_CTM_FIRST] = 1;
               }
               // get Last
               if($this->aScore[$nScore][C_CTM_NAME] === $this->aJson['placements'][$cPlacements - 1]['platform']){
                  $this->aScore[$nScore][C_CTM_POINTS] += $weight[5];
                  $this->aScore[$nScore][C_CTM_COUNT]++;
                  $this->aScore[$nScore][C_CTM_LAST] = 1;
               }
            }
               
            // get Center
            for($nPlacement = 1; $nPlacement < $cPlacements - 1; $nPlacement++)
               for($nScore = 0; $nScore < $cScore; $nScore++)
                  if($this->aScore[$nScore][C_CTM_NAME] === $this->aJson['placements'][$nPlacement]['platform'] && 
                     $this->aScore[$nScore][C_CTM_COUNT] < 3){
                          
                     $this->aScore[$nScore][C_CTM_POINTS] += $weight[6];
                     $this->aScore[$nScore][C_CTM_COUNT]++;
                     $this->aScore[$nScore][C_CTM_CENTER] = 1;
                  }
           }
           break;
   }
   
   return(C_CTM_SUCCESS);
}
////////////////////////////////////////////////
// updatePlatform
////////////////////////////////////////////////
private function updatePlatform(){

   for($i = 0; $i < count($this->aScore); $i++){

      // prevent cross site scripting
      $sec_name = htmlspecialchars($this->aScore[$i][C_CTM_NAME], ENT_QUOTES, 'UTF-8');
   
      // prevent sql injection
      $sec_name = $this->csql->secure($sec_name, true);
   
      $sec_revenue = $this->csql->secure($_GET['revenue'], true);
      //sql round $sec_revenue like round($sec_revenue, 0, PHP_ROUND_HALF_UP)

      $sql = "SELECT * ".
             'FROM   platform '.
             "WHERE  name = ".$sec_name.";";

      if(!$qTemp = $this->csql->query($sql)){
         if(C_CTM_DEBUG) $this->sError = $this->csql->showSqlError($sql); 
         return(C_CTM_ERROR);
      }

      /////////////////////////////////////////////////////////////////
      // if not exist in table platform, create a new entry
      
      if($qTemp->num_rows == 0){  
      
         $sql = 'SELECT id_platform '.
                'FROM   platform';

         if(!$qTemp = $this->csql->query($sql)){
            if(C_CTM_DEBUG) $this->sError = $this->csql->showSqlError($sql);  
            return(C_CTM_ERROR);
         }

         if($this->aScore[$i][C_CTM_LAST]) $price = $sec_revenue;
         else                              $price = 0;

         
         $this->aScore[$i][C_CTM_ID] = $qTemp->num_rows + 1;
         
         // ex: $this->$aName['trivago'] = 3; We need this in update connection
         $this->aName[$this->aScore[$i][C_CTM_NAME]] = $this->aScore[$i][C_CTM_ID];
      
         $sql = "INSERT INTO platform ".
                "VALUES ('".$this->aScore[$i][C_CTM_ID]."', '". 
                            $sec_name."', '".
                            $this->aScore[$i][C_CTM_POINTS]."', '".
                            $this->aScore[$i][C_CTM_FIRST]."', '".
                            $this->aScore[$i][C_CTM_LAST]."', '".
                            $this->aScore[$i][C_CTM_CENTER]."', '".
                            $price."', 1);";

         if(!$this->csql->query($sql)){
            if(C_CTM_DEBUG) $this->sError = $this->csql->showSqlError($sql);  
            return(C_CTM_ERROR);
         }
         
      }else // if exist in table platform, update the entry
      if($qTemp->num_rows == 1){ 

         $tResult = $qTemp->fetch_assoc();

         $this->aScore[$i][C_CTM_ID] = $tResult['id_platform'];
         
         // ex: $this->$aName['trivago'] = 3; We need this in update connection
         $this->aName[$this->aScore[$i][C_CTM_NAME]] = $this->aScore[$i][C_CTM_ID];

         $NewScore  = $tResult['score']  + $this->aScore[$i][C_CTM_POINTS];
         $NewFirst  = $tResult['first']  + $this->aScore[$i][C_CTM_FIRST];
         $NewLast   = $tResult['last']   + $this->aScore[$i][C_CTM_LAST];
         $NewCenter = $tResult['center'] + $this->aScore[$i][C_CTM_CENTER];
         
         if($this->aScore[$i][C_CTM_LAST]) $NewSales = $tResult['sales'] + $sec_revenue;
         else      $NewSales = $tResult['sales'];
         
         $NewConversions = $tResult['conversions'] + 1;


         $sql = "UPDATE platform ".
                "SET    conversions  = ".$NewConversions.", ".
                       "first  = ".$NewFirst.", ".
                       "last   = ".$NewLast.", ".
                       "center = ".$NewCenter.", ".
                       "sales  = ".$NewSales.", ".
                       "score  = ".$NewScore." ".
                 "WHERE id_platform = '".$tResult['id_platform']."';";

         if(!$this->csql->query($sql)){
            if(C_CTM_DEBUG) $this->sError = $this->csql->showSqlError($sql); 
            return(C_CTM_ERROR);
         }
         
      }else{ 
         if(C_CTM_DEBUG) $this->sError = 'malformed database';
         return(C_CTM_ERROR);
      }
   }
   
   return(C_CTM_SUCCESS);
}
////////////////////////////////////////////////
// updateConversion
////////////////////////////////////////////////
private function updateConversion(){

   $sql = 'SELECT id_conversion '.
          'FROM   conversion '.
          'ORDER  BY id_conversion DESC '.
          'LIMIT  1;';
         
   if(!$qTempUpdate = $this->csql->query($sql)){
      if(C_CTM_DEBUG) $this->sError = $this->csql->showSqlError($sql);
      return(C_CTM_ERROR);
   }

   // id for this conversion
   $tResult = $qTempUpdate->fetch_assoc();
   $this->id_conv = $tResult['id_conversion'] + 1;

   // prevent sql injection
   $sec_revenue       = $this->csql->secure($_GET['revenue'], true);
   $sec_customerId    = $this->csql->secure($_GET['customerId'], true);
   $sec_bookingNumber = $this->csql->secure($_GET['bookingNumber'], true);

   //sql round $sec_revenue like round($sec_revenue, 0, PHP_ROUND_HALF_UP)."', '".

   $sql = "INSERT INTO conversion ".
          "VALUES ('".$this->id_conv."', ".
                      $sec_customerId.", ".
                      $sec_bookingNumber.", ".
                      $sec_revenue.");";
   
   if(!$this->csql->query($sql)){
      if(C_CTM_DEBUG) $this->sError = $this->csql->showSqlError($sql); 
      return(C_CTM_ERROR);
   }
   
   return(C_CTM_SUCCESS);
}
////////////////////////////////////////////////
// updateConnection
////////////////////////////////////////////////
private function updateConnection(){

   ////////////////////////////////////////////////////////////////////
   // loop through all Placements and save them in table connection
   for($n = 0; $n < count($this->aJson['placements']); $n++){

      $timestamp = $this->aJson['placements'][$n]['date_of_contact'];
      
      // get id by name 
      $id_platform = $this->aName[$this->aJson['placements'][$n]['platform']]; 

      $sql = "INSERT INTO connection ".
             "VALUES (NULL, '$this->id_conv', '$id_platform', '$timestamp');";
      
      if(!$this->csql->query($sql)){
         if(C_CTM_DEBUG) $this->sError = $this->csql->showSqlError($sql); 
         return(C_CTM_ERROR);
      }
   }
   
   return(C_CTM_SUCCESS);
}
///////////////////////////////////////////////////
//
///////////////////////////////////////////////////
}
?>
