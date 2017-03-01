/**
 *  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script> 
 */
;(function($){
  /**
   *  Ajax for the form post which is not used currently
   */
  $(function(){ 
    $.fn.view_student = function() {
      $.ajax({                                      
          url: 'dbpacAjax.ajaxurl',                  //the script to call to get data 
          type: "POST",         
          data: {action: 'view_student'},                             
          dataType: 'json',                     //data format      
          success: function(data, response) {             //on recieve of reply   
            if(response.type == "success") { 
              $.each(data, function(i, item) {
                $("#data-table > tbody:first").append("<tr><td>" +
                                    item.id +
                                    "</td><td>" +
                                    item.name +
                                    "</td></tr>"); 
              });
          } else {
            $("#data-table > tbody:first").append("<tr><td>" +
                                    "No Student" +
                                    "</td><td>" +
                                    "No Student" +
                                    "</td></tr>"); 
          }
        } 
      }); 
    }  
  });

}(jQuery));