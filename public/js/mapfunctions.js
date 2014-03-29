mxn.addProxyMethods( mxn.Mapstraction, [
  /**
   * Add a method that can be called to add our extra stuff to an implementation.
   */
  'addExtras'
]);

/*mxn.register( 'google', {
  Mapstraction: {
    addExtras: function() {
      var me = this;
      me.markerAdded.addHandler( function( name, source, args ) {
        // enable dragend event for google
        args.marker.dragend = new mxn.Event( 'dragend', args.marker );
        google.maps.Event.addListener( args.marker.proprietary_marker, 'dragend', function( latlng ) {
          args.marker.dragend.fire( { location: new mxn.LatLonPoint( latlng.lat(), latlng.lng() ) } );
        });
      });
    }
  }*/
mxn.register( 'googlev3', {
  Mapstraction: {
    addExtras: function() {
      var me = this;
      me.markerAdded.addHandler( function( name, source, args ) {
        // enable dragend event for google
        args.marker.dragend = new mxn.Event( 'dragend', args.marker );
        google.maps.event.addListener(args.marker.proprietary_marker, 'dragend', function(latlng) {
          //infowindow.open(map,marker);
          var point = args.marker.proprietary_marker.getPosition();
          console.log("dragend"+point);
          args.marker.dragend.fire( { location: new mxn.LatLonPoint( point.lat(), point.lng() ) } );
        });

          google.maps.event.addListener(args.marker.proprietary_marker, 'click', function() {
          var point = args.marker.proprietary_marker.getPosition();
          console.log("click: " + point);
          showStreetViewBasedInCoordinates(point.lat(),point.lng());
          });
      });
    }
  }

});

    window.arrayOfSafezones = new Array();
    window.arrayMarkersSafezones = new Array();
    window.objJsonSafezone;
    
/******* Add Safezones on Map*/
function getSafezones(dataSafezone){

  objJsonSafezone = jQuery.parseJSON(dataSafezone);
  console.log("nº Safezone: "+objJsonSafezone.safezones.length);
  //first safezone define center of map 
  for (x in objJsonSafezone.safezones) {
      var latlonOfSafezone = new mxn.LatLonPoint(objJsonSafezone.safezones[x].latitude, objJsonSafezone.safezones[x].longitude);
      var safezone = new mxn.Radius(latlonOfSafezone,20); //20 is a quality and point of safezone
      var safezonePoly = safezone.getPolyline(objJsonSafezone.safezones[x]['radius']/1000, '#00F');
      map.addPolyline(safezonePoly);

      point = new mxn.LatLonPoint(objJsonSafezone.safezones[x].latitude,objJsonSafezone.safezones[x].longitude);
      var marker = new mxn.Marker(point);

      nameAddress = objJsonSafezone.safezones[x].address;

      constructInfoBubbleToSafezone(marker,objJsonSafezone.safezones[x].name,objJsonSafezone.safezones[x].address,objJsonSafezone.safezones[x].latitude,objJsonSafezone.safezones[x].longitude,objJsonSafezone.safezones[x]['radius']
        );
      //if (update)
      //   marker.setDraggable(true);

      arrayMarkersSafezones[x] = marker;
      arrayOfSafezones[x] = safezone;
      map.addMarker(arrayMarkersSafezones[x]);
      //if (update)
      //  addHandlerToDragAndDropSafezoneMarker(arrayMarkersSafezones[x]);
   }
    if (objJsonSafezone.safezones.length>=2) {
       map.autoCenterAndZoom();  
    }else if(objJsonSafezone.safezones.length==1){
      addHighlightToTheMarker(objJsonSafezone.safezones[0].latitude,objJsonSafezone.safezones[0].longitude)
    }
}

function constructInfoBubbleToSafezone(marker,name,addr,lat,lon,safetyRadius){
  if (name===null)
    marker.setInfoBubble("<h3><strong>"+addr+"</strong></h3> <br>lat: "+lat+" lon: "+lon+" Radius : "+safetyRadius+"<br>");
  else
    marker.setInfoBubble("<h3><strong>"+name+"</strong></h3><br>address:"+addr+" <br>lat: "+lat+" lon: "+lon+" safetyRadius: "+safetyRadius+"<br>");

}
