// This example displays an address form, using the autocomplete feature
// of the Google Places API to help users fill in the information.

// This example requires the Places library. Include the libraries=places
// parameter when you first load the API. For example:
// <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

var placeSearch, freeform;
var componentForm = {
  street_number: 'short_name',
  route: 'long_name',
  locality: 'long_name',
  administrative_area_level_1: 'short_name',
  country: 'long_name',
  postal_code: 'short_name'
};

function initAutocomplete() {
  // Create the freeform object, restricting the search to geographical
  // location types.
  freeform = new google.maps.places.Autocomplete(
      /** @type {!HTMLInputElement} */(document.getElementById('freeform')),
      {types: ['geocode']});
  
  freeform.addListener('place_changed', function(){
    var place = freeform.getPlace();
    var address = place.formatted_address;
  
    //alert(document.getElementById("fill-in-address").value);
    document.getElementById("freeform").value = address;
  });
}
