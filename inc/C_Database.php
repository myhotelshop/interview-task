<?php

   class C_Database extends mysqli {
   
      private $db     = 'CTM';
      private $dbhost = '127.0.0.1:3306';
      private $dbuser = 'mhs';
      private $dbpass = 'mhs4mhs@ctm.';

      ////////////////////////////////////////////////
      // construct
      ////////////////////////////////////////////////
      function __construct(){
         return(parent::__construct($this->dbhost, $this->dbuser, $this->dbpass, $this->db));
      }
      ////////////////////////////////////////////////
      // showSqlError
      ////////////////////////////////////////////////
      public function showSqlError($sql){
         return('Query: '.$sql.':'.
                'Errno: '.$this->errno.':'.
                'Error: '.$this->error);
      }
      ////////////////////////////////////////////////
      // showConnectError
      ////////////////////////////////////////////////
      public function showConnectError(){
         return('Errno: '.$this->connect_errno.':'.
                'Error: '.$this->connect_error);
      }
      ////////////////////////////////////////////////
      // secure
      ////////////////////////////////////////////////
      public function secure($param, $quotes){
         if($quotes)
            $sec = "'".$this->escape_string($param)."'";
         else
            $sec = $this->escape_string($param);
            
         return($sec);
      }
   }

?>
