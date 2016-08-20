var country;
var city;
var loader = "<h3 class='text-center'><i class='fa fa-spinner fa-pulse fs40 darkgrey'></i></h3>";
var query_string = function () {
  // This function is anonymous, is executed immediately and 
  // the return value is assigned to QueryString!
  var query_string = {};
  var query = window.location.search.substring(1);
  var vars = query.split("&");
  for (var i=0;i<vars.length;i++) {
    var pair = vars[i].split("=");
    // If first entry with this name
    if (typeof query_string[pair[0]] === "undefined") {
      query_string[pair[0]] = pair[1];
      // If second entry with this name
    } else if (typeof query_string[pair[0]] === "string") {
      var arr = [ query_string[pair[0]], pair[1] ];
      query_string[pair[0]] = arr;
        // If third or later entry with this name
    } else {
      query_string[pair[0]].push(pair[1]);
    }
  } 
  return query_string;
} ();

jQuery(document).ready(function() {
  jQuery('#col-right').css('min-height',(jQuery(window).height())+'px');
  jQuery('#col-right-wr').css('min-height',(jQuery(window).height())+'px');
  
  jQuery('.page').css('min-height',(jQuery(window).height())+'px');
  setTimeout(repeatedTimeout, 100);

  if(jQuery('#filters').length) { 

if(sessionCity.length) { 
  country = sessionCountry.toLowerCase();
  city = sessionCity.toLowerCase();
   jQuery('#country_dict option').filter(function() {
       return (jQuery(this).text().toLowerCase() == country || jQuery(this).attr('data-localised').toLowerCase() == country); 
     }).prop('selected', true);
    
     jQuery('#country_dict').change();
     changeCities( jQuery('#country_dict').val());
     jQuery('#country_dict').change(function() { changeCities(this.value); } );
     jQuery('#city_dict').change(function() {   filterObjects();} );
     jQuery('#start_date').change(function() {   filterObjects();} );
   

} else { 
  jQuery.ajax( { 
    url: '//freegeoip.net/json/', 
    type: 'POST', 
    dataType: 'jsonp',
    timeout: 3000,
    success: function(location) {
         jQuery('#country_dict option').filter(function() {
           return (jQuery(this).val().toLowerCase() == location.country_name.toLowerCase() || jQuery(this).attr('data-localised').toLowerCase() == location.country_name.toLowerCase()); 
         }).prop('selected', true);
         country = location.country_name.toLowerCase();
         city = location.city.toLowerCase();
     
   }, 
   complete: function (request) {
     jQuery('#country_dict').change();
     changeCities( jQuery('#country_dict').val());
      jQuery('#country_dict').change(function() { changeCities(this.value); } );
     jQuery('#city_dict').change(function() {   filterObjects();} );
     jQuery('#start_date').change(function() {   filterObjects();} );
   }
     });
}
  }

  if(jQuery('#filtersType').length) { 
    if(query_string.country_dict == undefined){
      jQuery('#country_dict option').filter(function() {
         return (jQuery(this).attr('value').toLowerCase() == sessionCountry.toLowerCase() || jQuery(this).text().toLowerCase() == sessionCountry.toLowerCase() || jQuery(this).attr('data-localised').toLowerCase() == sessionCountry.toLowerCase() || jQuery(this).attr('data-code').toLowerCase() == sessionCountry.toLowerCase()); 
       }).prop('selected', true);
    }
    
     jQuery('#country_dict').change();
       
       var type = '';
      if(jQuery('#ideacategory_id').length) {  type = 'ideas'; }
      if(jQuery('#placecategory_id').length) { type = 'places'; }
      if(jQuery('#eventcategory_id').length) {  type = 'events'; }
      jQuery('#'+type).html(loader);
      changeCitiesForObjectsPage( jQuery('#country_dict').val(),type);

       jQuery('#country_dict').change(function() {  jQuery('#'+type).html(loader); changeCitiesForObjectsPage(this.value,type); } );
       jQuery('#city_dict').change(function() {  filterObjectsByType(type); } );
       jQuery('#start_date').change(function() {    filterObjectsByType(type); } );
      if(jQuery('#ideacategory_id').length) {  jQuery('#ideacategory_id').change(function() {     filterObjectsByType(type); } ); }
      if(jQuery('#placecategory_id').length) {  jQuery('#placecategory_id').change(function() {   filterObjectsByType(type); } ); }
      if(jQuery('#eventcategory_id').length) {  jQuery('#eventcategory_id').change(function() {  filterObjectsByType(type); } ); }
      if(jQuery('#travelcategory_id').length) {  jQuery('#travelcategory_id').change(function() {  filterObjectsByType(type); } ); }
      if(jQuery('#interestcategory_id').length) {  jQuery('#interestcategory_id').change(function() {  filterObjectsByType(type); } ); }
      if(jQuery('#discountcategory_id').length) {  jQuery('#discountcategory_id').change(function() {  filterObjectsByType(type); } ); }
 //   }
 // });
  }
});

function changeCities(val) { 
  showLoader();
  jQuery.ajax( { 
    url: BASE_URL+'funstica/index/getCitiesByCountryId', 
    type: 'POST', 
    data: 'countryId='+val+'&isAjax=true',
    success: function(response) {
     var ids = response.split(',');
     jQuery('#city_dict').html('');
     for(i = 0; i<ids.length; i++ ) { 
      var pair = ids[i].split('=');
      jQuery('#city_dict').append('<option  data-localised="'+pair[2]+'" value="'+pair[0]+'">'+pair[1]+'</option>');
      }
    
    jQuery('#city_dict option').filter(function() {
      return (jQuery(this).text().toLowerCase() == city || jQuery(this).attr('data-localised').toLowerCase() == city || jQuery(this).val().toLowerCase() == city); 
    }).prop('selected', true);

     jQuery('#city_dict').select2();
     filterObjects();
  }
});

}

function changeCitiesForObjectsPage(val,type) { 

  if(val != undefined && val != '') { 
  jQuery.ajax( { 
    url: BASE_URL+'funstica/index/getCitiesByCountryId', 
    type: 'POST', 
    data: 'countryId='+val+'&isAjax=true',
    success: function(response) {
     var ids = response.split(',');
     jQuery('#city_dict').html('');
     for(i = 0; i<ids.length; i++ ) { 
      var pair = ids[i].split('=');
      jQuery('#city_dict').append('<option  data-localised="'+pair[2]+'" value="'+pair[0]+'">'+pair[1]+'</option>');
      }
    
     if(query_string.city_dict == undefined){
        jQuery('#city_dict option').filter(function() {
          return (jQuery(this).attr('value').toLowerCase() == sessionCity || jQuery(this).text().toLowerCase() == sessionCity || jQuery(this).attr('data-localised').toLowerCase() == sessionCity); 
        }).prop('selected', true);
      } else { 
          jQuery('#city_dict option').filter(function() {
          return (jQuery(this).text().toLowerCase() == query_string.city_dict || jQuery(this).attr('data-localised').toLowerCase() == query_string.city_dict || jQuery(this).attr('value').toLowerCase() == query_string.city_dict); 
        }).prop('selected', true);
      }
     jQuery('#city_dict').select2();
    filterObjectsByType(type);
  }
});
} else { 
   jQuery('#city_dict').html('');
     jQuery('#city_dict').select2();
    filterObjectsByType(type);
}

}

function changeCitiesByCode(val,customerCity) { 
  showLoader();
  jQuery.ajax( { 
    url: BASE_URL+'funstica/index/getCitiesByCountryCode', 
    type: 'POST', 
    data: 'countryCode='+val+'&isAjax=true',
    success: function(response) {
     var ids = response.split(',');
     jQuery('#city').html('');
    for(i = 0; i<ids.length; i++ ) { 
      var pair = ids[i].split('=');
      jQuery('#city').append('<option  data-localised="'+pair[2]+'" value="'+pair[0]+'">'+pair[1]+'</option>');
    }

    jQuery('#city option').filter(function() {
      return (jQuery(this).text().toLowerCase() == customerCity || jQuery(this).attr('data-localised').toLowerCase() == customerCity || jQuery(this).attr('value').toLowerCase() == customerCity); 
    }).prop('selected', true);

     jQuery('#city').select2();
    
  }
});

}

function showLoader() { 
    jQuery('#ideas').html(loader);
    jQuery('#places').html(loader);
    jQuery('#events').html(loader);
     jQuery('#travels').html(loader);
         jQuery('#interests').html(loader);
         jQuery('#discounts').html(loader);

}

function filterObjects() { 
  if(jQuery("#filters").length) { 
      showLoader();
      jQuery.ajax( { 
        url: BASE_URL+'funstica/index/getObjectsForHomepage', 
        type: 'POST', 
        data: jQuery("#filters").serialize(),
        success: function(response) {
         var data = JSON.parse(response);
         jQuery('#ideas').html(data.ideas);
         jQuery('#places').html(data.places);
         jQuery('#events').html(data.events);
        jQuery('#travels').html(data.travels);
         jQuery('#interests').html(data.interests);
         jQuery('#discounts').html(data.discounts);

         setTimeout(repeatedTimeout, 100);

       }
     });
  }
}

  function filterObjectsByType(type) { 
    if(jQuery("#filtersType").length) { 
      if(jQuery('#ideas').length) jQuery('#ideas').html(loader);
      if(jQuery('#places').length) jQuery('#places').html(loader);
      if(jQuery('#events').length) jQuery('#events').html(loader);
      if(jQuery('#travels').length) jQuery('#travels').html(loader);
      if(jQuery('#interests').length) jQuery('#interests').html(loader);
       if(jQuery('#discounts').length) jQuery('#discounts').html(loader);

       var add = '';
       if(jQuery('#queryText').length) add = '?queryText=' + jQuery('#queryText').val();
       
      jQuery.ajax( { 
        url: BASE_URL+'funstica/index/getObjectsForHomepage'+add, 
        type: 'POST', 
        data: jQuery("#filtersType").serialize(),
        success: function(response) {
         var data = JSON.parse(response);
         if(jQuery('#ideas').length)  jQuery('#ideas').html(data.ideas);
         if(jQuery('#places').length) jQuery('#places').html(data.places);
         if(jQuery('#events').length) jQuery('#events').html(data.events);
         if(jQuery('#travels').length) jQuery('#travels').html(data.travels);
         if(jQuery('#interests').length) jQuery('#interests').html(data.interests);
          if(jQuery('#discounts').length) jQuery('#discounts').html(data.discounts);
         setTimeout(repeatedTimeout, 100);

       }
     });
    }
  }

  function repeatedTimeout() { 
    setTimeout(equalHeight, 100);
    setTimeout(equalHeight, 500);
    setTimeout(equalHeight, 1000);
  }

  function equalHeight() { 
    jQuery(".equal").css('height','auto');
    jQuery(".equal").responsiveEqualHeightGrid(3);
  }

 
