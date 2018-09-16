<?php
////////////////////////////////////////////////////////////////////////////////////
//
//  [ INDEX ]
// 
//
// Last Modi: 14.09.18 [µ~]
//
   include_once('./inc/C_Html5.php');

   start();
   
////////////////////////////////////////////////////////////////////////////////////
// start
////////////////////////////////////////////////////////////////////////////////////
function start(){
   $Html5 = new C_Html5();

   $aCssFiles = array();
   array_push($aCssFiles, "./css/ctm.css");

   $aJsFiles = array();
   array_push($aJsFiles, "./js/jquery.js");
   array_push($aJsFiles, "./js/ctm3.js");
   
   $Html5->write_Head('.ctm', $aCssFiles, $aJsFiles, 0, 0);
   write_Content();
   $Html5->write_Tail();
}
////////////////////////////////////////////////////////////////////////////////////
//  write_Content
////////////////////////////////////////////////////////////////////////////////////
function write_Content(){

   echo "<div id=\"content\">";

      echo "<div class=\"headl\">";
         echo "<a id=\"home\" href=\"javascript:getTablePlatform();\">home</a>";
         echo "<a id=\"realtime\" href=\"javascript:realtime()\">Realtime Update</a>";
         echo "<a id=\"showapi\" href=\"javascript:getApi()\">show Api</a>";
      echo "</div>";
   
      echo "<div class=\"clear\">"."</div>";

      echo "<div id=\"platform\">"."</div>";
      echo "<div id=\"conversion\">"."</div>";
      echo "<div id=\"connection\">"."</div>";
      echo "<div id=\"mvpath\">"."</div>";
      echo "<div id=\"mvpathdata\">"."</div>";
      echo "<div class=\"clear\">"."</div>";

   echo "</div>";
   
   echo "<div id=\"foot\" class=\"clear\">";
      echo "<div class=\"footc\">"."<a href=\"#top\">top</a></div>";
   echo "</div>";
}
?>
