   <?php
////////////////////////////////////////////////////////////////////////////////////
//
//  [ INDEX ]
// 
//
// Last Modi: 20.08.12 [µ~]
//
// COOKIE GENERATOR
   
   $ch = curl_init();
   
   $Platform = array('trivago', 'kayak', 'tripadvisor', 'google', 'amazon', 'ebay', 'facebook', 'twitter', 'momondo', 'urlaubsguru', 'myhotelshop', 'instagram');
   
   $date = new DateTime();
   
   if(isset($_GET['count'])) $count = $_GET['count'];
   else                      $count = 1000;
   echo "Start"."</br>";
   
   for($m = 0; $m < $count; $m++){

      $cookie = array('placements' => array());
   
      $id = rand(123, 123);
      
      $revenue = rand(1000, 200000) / 100;
      
      $bookingNumber = rand(100, 1000);
   
      $timestamp = rand(1161502725, 1291502725);
 
      for($n = 0; $n < rand(0, 20); $n++){
   
         $date->setTimestamp($timestamp);
         $time = $date->format('Y-m-d H:i:s');
   
         $b = array('platform' => $Platform[rand(0, count($Platform) - 1)], 'customer_id' => $id, 'date_of_contact' => $time );
   
         array_push($cookie['placements'], $b);
      
         $timestamp += 150000; 
      }

      $jcookie = json_encode($cookie);

      $URL = 'http://192.168.23.197/collect.php?revenue='.$revenue.'&customerId='.$id.'&bookingNumber='.$bookingNumber;
      
      curl_setopt($ch, CURLOPT_COOKIE, 'mhs-tracking='.$jcookie);
      curl_setopt($ch, CURLOPT_URL, $URL);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      
      $result = curl_exec($ch); 
      
      if($aJson = json_decode($result, true, 4)){
      
         if($aJson['result'] != 1){
            echo $aJson['status'].":".$aJson['result']."</br>";
            echo $URL."</br>";
            echo $jcookie."</br>";
         }
         
      }
   }
   
   curl_close($ch);
   
   echo "Done"."</br>";
   
   ?>
