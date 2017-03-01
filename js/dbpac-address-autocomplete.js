;(function($){
  $(function(){
    /**
     * Smarty Street
     * https://smartystreets.com/
     */

    var htmlKey = '33427077806766735'; //'33427081096309297'; //'8140491234758226'
    var liveaddress = $.LiveAddress({
        key: htmlKey,   // An HTML key from your account
        verifySecondary: true,
        waitForStreet: false,
        debug: false,
        target: "US",
        //autoVerify: true,
        addresses: [{
            freeform: '#freeform',
            country: '#country'
        }]
    });

    liveaddress.on("AddressAccepted", function(event, data, previousHandler){
      if (data.response.isMissingSecondary())
      {
        data.address.abort(event);
        alert("Don't forget your apartment number!");
      }
      else
        previousHandler(event, data);
    });
  });

}(jQuery));