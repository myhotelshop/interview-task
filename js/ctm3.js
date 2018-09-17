///////////////////////////////////////////////////////////////////////////////////////////
//
// CTM - DEMO
//
// 
// Last Modi: 14.09.18 [µ~]
//

var atable = new Array("name", "id", "score", "sales", "first", "center", "last", "conversions");

var sURL_Platform = '/get/platform/0/100/name';
var nURL_Platform = 0;

var sURL_Conversion = '/get/join/';
var nURL_Conversion = 0;
var nURL_Conversion_Name = '';

var sURL_Connection = '/get/connectby/id_conversion/0';
var nURL_Connection = 0;

var aPlatform;
var aConversion;
var aConnection;
var aApi;

var bTimer = false;
var vTimer;

///////////////////////////////////////////////////////////////////////////////////////////
// [ $(document).ready ]
///////////////////////////////////////////////////////////////////////////////////////////	
$(document).ready(function(){
   $('#realtime').css('color', '#74ac6d');
   getTablePlatform();
});
///////////////////////////////////////////////////////////////////////////////////////////
// [ realtime ]
///////////////////////////////////////////////////////////////////////////////////////////	
function realtime(){
   
   if(bTimer == true){
      clearInterval(vTimer);
      $('#realtime').css('color', '#74ac6d');
      bTimer = false;
   }else{
      vTimer = setInterval(execute, 1000);
      $('#realtime').css('color', '#e3d678');
      bTimer = true;
   }
}
///////////////////////////////////////////////////////////////////////////////////////////
// [ stop ]
///////////////////////////////////////////////////////////////////////////////////////////	
function stop(){
   

}
///////////////////////////////////////////////////////////////////////////////////////////
// [ execute ]
///////////////////////////////////////////////////////////////////////////////////////////	
function execute(){
   
   //getTable1(0, 0);
   getTablePlatform();
   //getTable3(0, 0);
   
   getConvByPlatform();
   getConnectByConversion();
}
///////////////////////////////////////////////////////////////////////////////////////////
// [ getTablePlatform ]
///////////////////////////////////////////////////////////////////////////////////////////	
function getTablePlatform(){

   $.ajax({ 
      type:     'GET', 
      url:       sURL_Platform,  
      dataType: 'json',
      success:   function(data){aPlatform = data; setPlatform(aPlatform);}
   });
}
///////////////////////////////////////////////////////////////////////////////////////////
// [ getConvByPlatform ]
///////////////////////////////////////////////////////////////////////////////////////////	
function getConvByPlatform(){

   $.ajax({ 
      type:     'GET', 
      url:       sURL_Conversion,  
      dataType: 'json',
      success:   function(data){aConversion = data; setConversion(aConversion);}
   });
}
///////////////////////////////////////////////////////////////////////////////////////////
// [ getConnectByConversion ]
///////////////////////////////////////////////////////////////////////////////////////////	
function getConnectByConversion(){

   $.ajax({ 
      type:     'GET', 
      url:       sURL_Connection,  
      dataType: 'json',
      success:   function(data){aConnection = data; setConnect(aConnection);}
   });
}
///////////////////////////////////////////////////////////////////////////////////////////
// [ getTablePlatform ]
///////////////////////////////////////////////////////////////////////////////////////////	
function setPlatform(data){

   $("#platform").html("");
   $('#platform').append($('<div>', {text: "TABLE PLATFORM"}).addClass("fhhead"));
   $('#platform').append($('<div>').addClass("clear"));
               
   var sClass;
               
   for(m = 0; m < atable.length; m++){
            
      if(m == nURL_Platform) sClass = "fhhead2";
      else                   sClass = "fhhead";
          
      $('#platform').append('<div class=\"' + sClass + '\" ><a href=\"javascript:setPlatformOrder(' + m + ')\">' + atable[m] + '</a></div>');   
   }
               
   $('#platform').append($('<div>').addClass("clear"));
  
   for(n = 0; n < data.result.length; n++){
         
      if(nURL_Conversion == data.result[n].id_platform){
         sClass = "fhitem2";
         nURL_Conversion_Name = data.result[n].name;
      }else{                                            
         sClass = "fhitem";
      }
      
      $('#platform').append('<div class=\"' + sClass + '\" >' + 
                          '<a href=\"javascript:setConvByPlatform(' + data.result[n].id_platform + ')\">' + data.result[n].name + 
                          '</a></div>');
      
      $('#platform').append('<div class=\"' + sClass + '\" >' + data.result[n].id_platform + '</div>');
      $('#platform').append('<div class=\"' + sClass + '\" >' + data.result[n].score       + '</div>');
      $('#platform').append('<div class=\"' + sClass + '\" >' + data.result[n].sales       + '</div>');
      $('#platform').append('<div class=\"' + sClass + '\" >' + data.result[n].first       + '</div>');
      $('#platform').append('<div class=\"' + sClass + '\" >' + data.result[n].center      + '</div>');
      $('#platform').append('<div class=\"' + sClass + '\" >' + data.result[n].last        + '</div>');
      $('#platform').append('<div class=\"' + sClass + '\" >' + data.result[n].conversions + '</div>');

      $('#platform').append($('<div>').addClass("clear"));      
   } 
}
///////////////////////////////////////////////////////////////////////////////////////////
// [ setConversion ]
///////////////////////////////////////////////////////////////////////////////////////////	
function setConversion(data){

   $("#conversion").html("");	 
   $('#conversion').append($('<div>', {text: "LAST 10 CONVERSIONS FROM " + nURL_Conversion_Name}).addClass("fhitem2"));
   $('#conversion').append($('<div>').addClass("clear"));
               
   $('#conversion').append($('<div>', {text: "id"}).addClass("fhitem2"));
   $('#conversion').append($('<div>', {text: "customer"}).addClass("fhitem2"));
   $('#conversion').append($('<div>', {text: "booking"}).addClass("fhitem2"));
   $('#conversion').append($('<div>', {text: "revenue"}).addClass("fhitem2"));
   $('#conversion').append($('<div>').addClass("clear"));

   for(n = 0; n < data.result.length; n++){
      if(data.result[n].id_conversion == nURL_Connection) sClass = "fh3";
      else                                                sClass = "fh4";

      $('#conversion').append('<div class=\"' + sClass + '\" ><a href=\"javascript:setConnectByConversion(' + data.result[n].id_conversion + ')\">' + data.result[n].id_conversion + '</a></div>');
      $('#conversion').append($('<div>', {text: data.result[n].id_customer}).addClass(sClass));
      $('#conversion').append($('<div>', {text: data.result[n].id_booking}).addClass(sClass));
      $('#conversion').append($('<div>', {text: data.result[n].revenue}).addClass(sClass));
      $('#conversion').append($('<div>').addClass("clear"));
   }  
}
///////////////////////////////////////////////////////////////////////////////////////////
// [ setConnect ]
///////////////////////////////////////////////////////////////////////////////////////////	
function setConnect(data){
   
   $("#connection").html("");	 
   $('#connection').append($('<div>', {text: "CONVERSION " + nURL_Connection}).addClass("fh5head"));
   $('#connection').append($('<div>').addClass("clear"));
               
   $('#connection').append($('<div>', {text: "id"}).addClass("fh5head"));
   $('#connection').append($('<div>', {text: "conversion"}).addClass("fh5head"));
   $('#connection').append($('<div>', {text: "platform"}).addClass("fh5head"));
   $('#connection').append($('<div>', {text: "time"}).addClass("fh5headbig"));
   $('#connection').append($('<div>').addClass("clear"));
   
   var sClass;
   var sBigClass;
   
   for(n = 0; n < data.result.length; n++){

      if(data.result[n].id_platform == nURL_Conversion){
         sClass    = 'fh5';
         sBigClass = 'fh5big';
      }else{                                       
         sClass    = 'fh6';
         sBigClass = 'fh6big';
      }
      
      
      //var time = timeConverter(data.result[n].time);
      
      $('#connection').append($('<div>', {text: data.result[n].id_connection}).addClass(sClass));
      $('#connection').append($('<div>', {text: data.result[n].id_conversion}).addClass(sClass));
      $('#connection').append($('<div>', {text: data.result[n].id_platform}).addClass(sClass));
      //$('#connection').append($('<div>', {text: time}).addClass(sBigClass));
      $('#connection').append($('<div>', {text: data.result[n].time}).addClass(sBigClass));
      $('#connection').append($('<div>').addClass("clear"));
   }
}
///////////////////////////////////////////////////////////////////////////////////////////
// [ setPlatformOrder ]
///////////////////////////////////////////////////////////////////////////////////////////	
function setPlatformOrder(order){

   switch(order){
      case 0: sURL_Platform = '/get/platform/0/100/name';             nURL_Platform = 0; break;
      case 1: sURL_Platform = '/get/platform/0/100/id_platform';      nURL_Platform = 1; break;
      case 2: sURL_Platform = '/get/platform/0/100/score desc';       nURL_Platform = 2; break;
      case 3: sURL_Platform = '/get/platform/0/100/sales desc';       nURL_Platform = 3; break;
      case 4: sURL_Platform = '/get/platform/0/100/first desc';       nURL_Platform = 4; break;
      case 5: sURL_Platform = '/get/platform/0/100/center desc';      nURL_Platform = 5; break;
      case 6: sURL_Platform = '/get/platform/0/100/last desc';        nURL_Platform = 6; break;
      case 7: sURL_Platform = '/get/platform/0/100/conversions desc'; nURL_Platform = 7; break;
   }
   
   getTablePlatform();
}
///////////////////////////////////////////////////////////////////////////////////////////
// [ setConvByPlatform ]
///////////////////////////////////////////////////////////////////////////////////////////	
function setConvByPlatform(id_platform){
   
   sURL_Conversion = '/get/join/' + id_platform;
   nURL_Conversion = id_platform;
   
   getConvByPlatform();
   setPlatform(aPlatform); // TEST /////////////
}
///////////////////////////////////////////////////////////////////////////////////////////
// [ setConnectByConversion ]
///////////////////////////////////////////////////////////////////////////////////////////	
function setConnectByConversion(id_conversion){
   
   sURL_Connection = '/get/connectby/id_conversion/' + id_conversion;
   nURL_Connection = id_conversion;
   
   getConnectByConversion();
   setConversion(aConversion); // TEST ////////////
}
///////////////////////////////////////////////////////////////////////////////////////////
// [ getApi ]
///////////////////////////////////////////////////////////////////////////////////////////	
function getApi(){
   
   $.ajax({ 
      type:     'GET', 
      url:      '/get',  
      dataType: 'json',
      success:   function(data){aApi = data; showApi(aApi);}
   });  
}
///////////////////////////////////////////////////////////////////////////////////////////
// [ showApi ]
///////////////////////////////////////////////////////////////////////////////////////////	
function showApi(data){
 
   $("#platform").html("");
   $("#conversion").html("");
   $("#connection").html("");

   for(n = 0; n < data.result.length; n++){
      
      $('#mvpath').append('<ul>');

      var path = data.result[n].link;
      
      $('#mvpath').append('<div class=\"lh\" >' + 
                            '<a href=\"javascript:getPath(\' ' + path + ' \')\">' + path + 
                            '</a></div>');

      $('#mvpath').append('</ul>');
      
   }
}
///////////////////////////////////////////////////////////////////////////////////////////
// [ getPath ]
///////////////////////////////////////////////////////////////////////////////////////////	
function getPath(path){
   $.ajax({ 
      type:     'GET', 
      url:       path,  
      dataType: 'json',
      success:   function(data){showPath(data);}
   });
}
///////////////////////////////////////////////////////////////////////////////////////////
// [ showPath ]
///////////////////////////////////////////////////////////////////////////////////////////	
function showPath(data){

   var t = data.result;

   $("#mvpathdata").html("");

   var sClass;
   var sBigClass;

   if(Array.isArray(data.result)){

      var res = JSON.stringify(data.result[0]);
      var res = res.slice(1, res.length - 1);
      var aRes  = res.split(",");
         
      for(m = 0; m < aRes.length; m++){
         var aRes2 = aRes[m].split(":");
         $('#mvpathdata').append($('<div>', {text: aRes2[0].slice(1, aRes2[0].length - 1)}).addClass('fh5head'));
      }
      $('#mvpathdata').append($('<div>').addClass("clear"));
         
      for(n = 0; n < data.result.length; n++){

         var res = JSON.stringify(data.result[n]);
         var res = res.slice(1, res.length - 1);
         var aRes  = res.split(",");

         for(m = 0; m < aRes.length; m++){
            var aRes2 = aRes[m].split(":");
            $('#mvpathdata').append($('<div>', {text: aRes2[1].slice(1, aRes2[1].length - 1)}).addClass('fh6'));
         }
         
         $('#mvpathdata').append($('<div>').addClass("clear"));
      }
      
   }else{
      
      var res = JSON.stringify(data.result);
      
      var res = res.slice(1, res.length - 1);
   
      var aRes = res.split(",");
   
      for(n = 0; n < aRes.length; n++){
         var aRes2 = aRes[n].split(":");
  
         $('#mvpathdata').append($('<div>', {text: aRes2[0].slice(1, aRes2[0].length - 1)}).addClass('fh5big'));
         $('#mvpathdata').append($('<div>', {text: aRes2[1].slice(1, aRes2[1].length - 1)}).addClass('fh6big'));
         $('#mvpathdata').append($('<div>').addClass("clear"));
      }
   }
   
   location.href = "#top";
}
///////////////////////////////////////////////////////////////////////////////////////////
// [ timeConverter ]
///////////////////////////////////////////////////////////////////////////////////////////	
function timeConverter(timestamp){
   
   var a = new Date(timestamp * 1000);

   var year  = a.getFullYear();
  
   var month = a.getMonth() + 1;
   var date  = a.getDate();
   var hour  = a.getHours();
   var min   = a.getMinutes();
   var sec   = a.getSeconds();

  
   var zMonth;
   var zDate;
   var zHours;
   var zMinutes;
   var zSeconds;
  
   if(month < 10) zMonth = '0' + month;
   else           zMonth = month;
  
   if(date < 10)  zDate = '0' + date;
   else           zDate = date;
  
   if(hour < 10)  zHours = '0' + hour;
   else           zHours = hour;
  
   if(min < 10)   zMinutes = '0' + min;
   else           zMinutes = min;
  
   if(sec < 10)   zSeconds = '0' + sec;
   else           zSeconds = sec;

   var time = year + '-' + zDate + '-' + zMonth + ' ' + zHours + ':' + zMinutes + ':' + zSeconds;
   
   return(time);
}
///////////////////////////////////////////////////////////////////////////////////////////
// [ getTable ]
///////////////////////////////////////////////////////////////////////////////////////////	
function getTable1(id, param){

   //alert('1');

   $.ajax({ 
      type:    'GET', 
      url:     '/get/conversion/0/10/id_conversion desc',  
      dataType: 'json',
      success: function(data){ 
        
         //alert(data);
         //alert(data.result.rev_full);
         
               $("#mvstream").html("");	 
               

               $('#mvstream').append($('<div>', {text: "id_conversion"}).addClass("fhhead"));
               $('#mvstream').append($('<div>', {text: "id_customer"}).addClass("fhhead"));
               $('#mvstream').append($('<div>', {text: "id_booking"}).addClass("fhhead"));
               $('#mvstream').append($('<div>', {text: "rev_round"}).addClass("fhhead"));
               $('#mvstream').append($('<div>').addClass("clear"));

               for(n = 0; n < data.result.length; n++){
               //alert(data.result[n].name);
                  $('#mvstream').append($('<div>', {text: data.result[n].id_conversion}).addClass("fhitem"));
                  $('#mvstream').append($('<div>', {text: data.result[n].id_customer}).addClass("fhitem"));
                  $('#mvstream').append($('<div>', {text: data.result[n].id_booking}).addClass("fhitem"));
                  $('#mvstream').append($('<div>', {text: data.result[n].rev_round}).addClass("fhitem"));
                  $('#mvstream').append($('<div>').addClass("clear"));
               }
         
         //$("#mvajax").html(data.result.rev_full);
      }
   });
}
///////////////////////////////////////////////////////////////////////////////////////////
// [ getTable3 ]
///////////////////////////////////////////////////////////////////////////////////////////	
function getTable3(id, param){

   //alert('1');

   $.ajax({ 
      type:    'GET', 
      url:     '/get/connection/0/100/id_connection desc',  
      dataType: 'json',
      success: function(data){ 
        

			   $("#mvfilter").html("");	 
               
               $('#mvfilter').append($('<div>', {text: "id_connection"}).addClass("fhhead"));
               $('#mvfilter').append($('<div>', {text: "id_conversion"}).addClass("fhhead"));
               $('#mvfilter').append($('<div>', {text: "id_platform"}).addClass("fhhead"));
               $('#mvfilter').append($('<div>', {text: "time"}).addClass("fhhead"));
               $('#mvfilter').append($('<div>').addClass("clear"));
               
               
               for(n = 0; n < data.result.length; n++){
               //alert(data.result[n].name);
                  $('#mvfilter').append($('<div>', {text: data.result[n].id_connection}).addClass("fhitem"));
                  $('#mvfilter').append($('<div>', {text: data.result[n].id_conversion}).addClass("fhitem"));
                  $('#mvfilter').append($('<div>', {text: data.result[n].id_platform}).addClass("fhitem"));
                  $('#mvfilter').append($('<div>', {text: data.result[n].time}).addClass("fhitem"));
                  $('#mvfilter').append($('<div>').addClass("clear"));
               }

         
         //$("#mvajax").html(data.result.rev_full);
      }
   });
}
//////////////////////////////////////////////////////////////////////////////////
function ChangeSize(){
   var obj = document.getElementById("content");
	   
   if(!obj){
      alert('Object existiert nicht - 307');
   }else{
      if(!obj.style){
         alert('obj.style existiert nicht - 307');
      }else{
         aSize = getSize()
         //alert(aSize[0] + ' TEST+TEST ' + aSize[1]);
         //alert(obj.offsetHeight + ' + ' + aSize[1]);

         if(obj.offsetHeight < aSize[1] - 37){
            obj.style.height = aSize[1] + 187 + 'px';
         }
      }
   }
}
//////////////////////////////////////////////////////////////////////////////////
function getSize(){
   var myWidth = 0, myHeight = 0;
		
   if(typeof(window.innerWidth) == 'number'){
      //Non-IE
      myWidth  = window.innerWidth;
      myHeight = window.innerHeight;
   }else 
   if(document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ){
      //IE 6+ in 'standards compliant mode'
      myWidth  = document.documentElement.clientWidth;
      myHeight = document.documentElement.clientHeight;
   } 
   else 
   if(document.body && (document.body.clientWidth || document.body.clientHeight)){
      //IE 4 compatible
      myWidth = document.body.clientWidth;
      myHeight = document.body.clientHeight;
   }
   //window.alert( 'Width = ' + myWidth );
   //window.alert( 'Height = ' + myHeight );
   return [myWidth, myHeight];
}
//////////////////////////////////////////////////////////////////////////////////	 
function getScrollXY(){
   var scrOfX = 0, scrOfY = 0;
		
   if(typeof(window.pageYOffset) == 'number'){
      //Netscape compliant
      scrOfY = window.pageYOffset;
      scrOfX = window.pageXOffset;
   }else 
   if(document.body && ( document.body.scrollLeft || document.body.scrollTop ) ){
      //DOM compliant
      scrOfY = document.body.scrollTop;
      scrOfX = document.body.scrollLeft;
   }else 
   if(document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) ){
      //IE6 standards compliant mode
      scrOfY = document.documentElement.scrollTop;
      scrOfX = document.documentElement.scrollLeft;
   }
   return [ scrOfX, scrOfY ];
}
//////////////////////////////////////////////////////////////////////////////////	
function init(){
   //ChangeSize();
}
