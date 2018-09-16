<?php
class C_Html5{
   ////////////////////////////////////////////////////////////////////////////////////
   // [ write_Head ]
   ////////////////////////////////////////////////////////////////////////////////////
   function write_Head($title, $aCssFiles, $aJsFiles, $icon, $time){
      echo "<!DOCTYPE html><html lang=\"de\"><head>".   
           "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"/>";

      if($time)
	     echo "<meta http-equiv=\"refresh\" content=\"".$time."; URL=./\"/>";
	  
      if($aCssFiles)
         if(count($aCssFiles))
            for($i = 0; $i < count($aCssFiles); $i++)
               echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$aCssFiles[$i]."\"/>";

      if($icon)
         if(strlen($icon)){
            echo "<link rel=\"Shortcut Icon\" href=\"icons/micro.ico\" type=\"image/x-icon\"/>";
	     }
		 
	  if($title)
	     if(strlen($title)){
            echo "<title>".$title."</title>";
         }else{
		    echo $title." : ".strlen($title);
		 }
	  
	  if($aJsFiles){
         if(count($aJsFiles)){
            for($i = 0; $i < count($aJsFiles); $i++)
               echo "<script type=\"text/javascript\" src=\"".$aJsFiles[$i]."\"></script>";
	        echo "</head><body onload=\"init()\">";
         }else{
		    echo "</head><body>";
		 }
      }else{
          echo "</head><body>";
      }
   }

   ////////////////////////////////////////////////////////////////////////////////////
   // [ write_Tail ]
   ////////////////////////////////////////////////////////////////////////////////////
   function write_Tail(){
      echo "</body></html>";
   }
}
?>