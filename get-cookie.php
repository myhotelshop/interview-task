<?php 

   $Platform = array('trivago', 'kayak', 'tripadvisor', 'google', 'amazon', 'ebay', 'facebook', 'twitter', 'momondo', 'urlaubsguru', 'myhotelshop', 'instagram');
   
   $date = new DateTime();
   
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
   
   echo "SET COOKIE:"."</br>";

   echo $jcookie."</br>";
   
   setcookie("mhs-tracking", $jcookie);
   
   echo "<a href=\"/set/".$id."/".$bookingNumber."/".$revenue."\">"."collect cookie"."</a>"

?>
