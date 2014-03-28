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