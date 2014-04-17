function addMagic() {
    mxn.addProxyMethods(mxn.Mapstraction, [
        /**
         * Add a method that can be called to add our extra stuff to an implementation.
         */
        'addExtras'
    ]);
}

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
if (!typeof mxn === 'undefined') {

    mxn.register('googlev3', {
        Mapstraction: {
            addExtras: function() {
                var me = this;
                me.markerAdded.addHandler(function(name, source, args) {
                    // enable dragend event for google
                    args.marker.dragend = new mxn.Event('dragend', args.marker);
                    google.maps.event.addListener(args.marker.proprietary_marker, 'dragend', function(latlng) {
                        //infowindow.open(map,marker);
                        var point = args.marker.proprietary_marker.getPosition();
                        console.log("dragend" + point);
                        args.marker.dragend.fire({location: new mxn.LatLonPoint(point.lat(), point.lng())});
                    });

                    google.maps.event.addListener(args.marker.proprietary_marker, 'click', function() {
                        var point = args.marker.proprietary_marker.getPosition();
                        console.log("click: " + point);
                        showStreetViewBasedInCoordinates(point.lat(), point.lng());
                    });
                });
            }
        }

    });
}
window.urlWriteSafezone = "";
window.arrayOfSafezones = new Array();
window.arrayMarkersSafezones = new Array();
window.arrayOfPOI = new Array();
window.objJsonSafezone;
var newArrayPoint = new Array();
var newMarkers = new Array();
var newAddress = new Array();
var newArrayPoint = new Array();

/******* Add Safezones on Map*/
function getSafezones(dataSafezone) {

    objJsonSafezone = jQuery.parseJSON(dataSafezone);
    console.log("nº Safezone: " + objJsonSafezone.safezones.length);
    //first safezone define center of map 
    for (x in objJsonSafezone.safezones) {
        var latlonOfSafezone = new mxn.LatLonPoint(objJsonSafezone.safezones[x].latitude, objJsonSafezone.safezones[x].longitude);
        var safezone = new mxn.Radius(latlonOfSafezone, 20); //20 is a quality and point of safezone
        var safezonePoly = safezone.getPolyline(objJsonSafezone.safezones[x]['radius'] / 1000, '#00F');
        map.addPolyline(safezonePoly);

        point = new mxn.LatLonPoint(objJsonSafezone.safezones[x].latitude, objJsonSafezone.safezones[x].longitude);
        var marker = new mxn.Marker(point);

        nameAddress = objJsonSafezone.safezones[x].address;

        constructInfoBubbleToSafezone(marker, objJsonSafezone.safezones[x].name, objJsonSafezone.safezones[x].address, objJsonSafezone.safezones[x].latitude, objJsonSafezone.safezones[x].longitude, objJsonSafezone.safezones[x]['radius']);
        //if (update)
        //   marker.setDraggable(true);

        arrayMarkersSafezones[x] = marker;
        arrayOfSafezones[x] = safezone;
        map.addMarker(arrayMarkersSafezones[x]);
        //if (update)
        //  addHandlerToDragAndDropSafezoneMarker(arrayMarkersSafezones[x]);
    }
    if (objJsonSafezone.safezones.length >= 2) {
        map.autoCenterAndZoom();
    } else if (objJsonSafezone.safezones.length == 1) {
        addHighlightToTheMarker(objJsonSafezone.safezones[0].latitude, objJsonSafezone.safezones[0].longitude)
    }
}


function saveSafezoneInDb(pos) {
    console.log("saving safezone: " + objJsonSafezone.safezones[pos].address);

    var selc = document.getElementById("notification_settings");
    var notification = selc.options[selc.selectedIndex].value;

    var dataJsonSend = '{"_id":"' + objJsonSafezone.safezones[pos]._id + '","address":"' + objJsonSafezone.safezones[pos].address + '","name":"' + document.getElementById("txt_name").value + '","latitude":' + objJsonSafezone.safezones[pos].latitude + ',"longitude":' + objJsonSafezone.safezones[pos].longitude + ',"radius":' + objJsonSafezone.safezones[pos].radius + ',"notification":"' + notification + '","device":"' + deviceAddress + '"}';
    //sentPOSTRequest(urlWriteSafezone, '{"safezone":' + dataJsonSend + '}', function(status) {
    //    console.log("sucessfull " + status);
    //window.location = "dashboardSafezones";
    //});
    var selc = document.getElementById("safezone").value = dataJsonSend;
    $("#form_new_safezone").submit();

    /*and add value in the input
     * nedd force the submit programmaticaly*/
}

function constructInfoBubbleToSafezone(marker, name, addr, lat, lon, safetyRadius) {
    if (name === null)
        marker.setInfoBubble("<h3><strong>" + addr + "</strong></h3> <br>lat: " + lat + " lon: " + lon + " Radius : " + safetyRadius + "<br>");
    else
        marker.setInfoBubble("<h3><strong>" + name + "</strong></h3><br>address:" + addr + " <br>lat: " + lat + " lon: " + lon + " safetyRadius: " + safetyRadius + "<br>");
}

/******** GET POIS FROM RAILS*/
/*
 function getPOIS(lat, long, i) {
 point = new mxn.LatLonPoint(lat, long);
 var marker = new mxn.Marker(point);
 window["map" + i].addMarker(marker);
 window["map" + i].autoCenterAndZoom();
 }*/

function getPOIS(datapois, i) {

    objJsonPOI = jQuery.parseJSON(datapois);
    console.log("nº POIS: " + objJsonPOI.pois.length);

    for (x in objJsonPOI.pois) {
        point = new mxn.LatLonPoint(objJsonPOI.pois[x].latitude, objJsonPOI.pois[x].longitude);
        var marker = new mxn.Marker(point);

        nameAddress = objJsonPOI.pois[x].address;

        //constructInfoBubbleToPOI(marker, objJsonPOI.pois[x].Name, objJsonPOI.pois[x].Address, objJsonPOI.pois[x].Latitude, objJsonPOI.pois[x].Longitude);
        //if (update)
        //  marker.setDraggable(true);

        arrayOfPOI[x] = marker;
        window["map" + i].addMarker(arrayOfPOI[x]);
        //if (update==true)
        // addHandlerToDragAndDropPoi(arrayOfPOI[x]);

    }
    ;
    if (objJsonPOI.pois.length >= 2) {
        window["map" + i].autoCenterAndZoom();
    } else if (objJsonPOI.pois.length == 1) {
        //addHighlightToTheMarker(objJsonPOI.pois[0].Latitude, objJsonPOI.pois[0].Longitude)
    }
}
function findUserLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var point = new mxn.LatLonPoint(position.coords.latitude, position.coords.longitude);
            map.setCenterAndZoom(point, 10);
        });
    }
}

function insertTempMarkers(addr, lat, lng) {
    var point = new mxn.LatLonPoint(lat, lng);
    var marker = new mxn.Marker(point);
    marker.setDraggable(true);

    newArrayPoint.push([lat, lng]);

    constructInfoBubbleToPOI(marker, addr, addr, lat, lng)
    map.addMarker(marker);
    marker.setDraggable(true);

    newMarkers.push(marker);
    addHandlerTempGeofences(newMarkers[newMarkers.length - 1]);
    newAddress.push(addr);
    marker.openInfoBubble.addHandler(myboxopened);
}

function insertGeofenceHandler() {
    map.click.addHandler(function(event_name, event_source, event_args) {
        removePreviousSearch();

        var coords = event_args.location;
        console.log("Mouse Click at: " + coords.lat + ' / ' + coords.lon);

        getAddressBasedOnCoordinates(coords.lat, coords.lon, function() {

            insertTempMarkers(nameAddress, coords.lat, coords.lon);

            addTableOfSearchResult(nameAddress);
            selectGeofence(0);
        });

        map.click.removeAllHandlers();
    });
}

/****** GET ON GOOGLE MAPS API THE ADDRESS OF POINT CLICKED */
function getAddressBasedOnCoordinates(lat, lon, callback) {
    var latlng = new google.maps.LatLng(lat, lon);
    geocoder.geocode({'latLng': latlng}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            if (results[1]) {
                window.nameAddress = results[1].formatted_address;
                callback(nameAddress);
            } else {
                //alert('No results found');
                callback("undefined");
            }
        } else {
            //alert('Geocoder failed due to: ' + status);
            callback("undefined");
        }
    });
}

function constructInfoBubbleToPOI(marker, name, addr, lat, lon) {
    if (name === null)
        marker.setInfoBubble("<h3><strong>" + addr + "</strong></h3> <br>lat: " + lat + " lon: " + lon + "<br>");
    else
        marker.setInfoBubble("<h3><strong>" + name + "</strong></h3><br>address:" + addr + " <br>lat: " + lat + " lon: " + lon + "<br>");

}
function constructInfoBubbleToSafezone(marker, name, addr, lat, lon, safetyRadius) {
    if (name === null)
        marker.setInfoBubble("<h3><strong>" + addr + "</strong></h3> <br>lat: " + lat + " lon: " + lon + " safetyRadius: " + safetyRadius + "<br>");
    else
        marker.setInfoBubble("<h3><strong>" + name + "</strong></h3><br>address:" + addr + " <br>lat: " + lat + " lon: " + lon + " safetyRadius: " + safetyRadius + "<br>");
}

function addHandlerTempGeofences(marker) {
    marker.dragend.addHandler(function(name, source, args) {
        var index = newMarkers.indexOf(source);
        newMarkers[index].closeBubble();

        getAddressBasedOnCoordinates(args.location.lat, args.location.lng, function() {
            newAddress[index] = nameAddress;
            newArrayPoint[index][0] = args.location.lat;
            newArrayPoint[index][1] = args.location.lng;
        });
        console.log("addHandlerTempGeofences ->pos:" + index + " was " + name + " : " + args.location.lat + " ; " + args.location.lng);
        showStreetView(args.location.lat, args.location.lng, nameAddress);//to refresh the street view
    });
}

function searchAddress() {
    removePreviousSearch();

    var texaddress = document.getElementById('address').value;
    if (texaddress.replace(/\s/g, "") === "") {
        console.log("No search apply");
        return;
    }
    ;

    geocoder.geocode({'address': texaddress + ", PT"}, function(results, status) {

        if (status == google.maps.GeocoderStatus.OK) {
            //map.setCenter(results[0].geometry.location);
            //console.log("resultado:"+results.length);
            for (var i = 0; i < results.length; i++) {
                insertTempMarkers(results[i].formatted_address, results[i].geometry.location.lat(), results[i].geometry.location.lng());
            }
            if (results.length >= 2) {
                map.autoCenterAndZoom();
            } else if (results.length == 1) {
                selectGeofence(0); // when found only one result auto select 
            }
            ;

        } else {
            alert('Geocode was not successful for the following reason: ' + status);
        }
        addTableOfSearchResult(newAddress);
        document.getElementById('myDynamicTable').style.visibility = "visible"; //to force show the table

    });
}

function codeLatLng(lat, lon, index, fn) {
    geocoder = new google.maps.Geocoder();
    var streetName = "";
    //var input = document.getElementById('latlng').value;
    //var latlngStr = input.split(',', 2);
    var lat = parseFloat(lat);
    var lng = parseFloat(lon);
    var latlng = new google.maps.LatLng(lat, lng);
    geocoder.geocode({'latLng': latlng}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            if (results[1]) {
                //map.setZoom(11);
                //marker = new google.maps.Marker({
                //    position: latlng,
                //    map: map
                //});
                // infowindow.setContent(results[1].formatted_address);
                // infowindow.open(map, marker);
                console.log("inside->" + lat + "," + lon + " - " + results[1].formatted_address);
                fn(results[0].formatted_address, index);
            } else {
                alert('No results found');
            }
        } else {
            alert('Geocoder failed due to: ' + status);
        }
    });
}

function insertSafezoneInMap(nameAddress, lat, lng, draggable) {
    var latlonOfSafezone = new mxn.LatLonPoint(lat, lng);
    var marker = new mxn.Marker(latlonOfSafezone);
    var safezone = new mxn.Radius(latlonOfSafezone, 20); //20 is a quality and point of safezone
    var radiusOfSafezone = 500;
    var poly = safezone.getPolyline(radiusOfSafezone / 1000, '#00F');
    map.addPolyline(poly);
    if (!update) {
        arrayOfSafezones.push(safezone);
        //var safezone_id ="Safezone-Bend-"+generateUUID();
        var safezone_id = "safezone_" + getCurrentTimeInMilliSeconds();
        var dataJsonSend = '{"_id":"' + safezone_id + '","name":"' + nameAddress + '","address":"' + nameAddress + '","latitude":' + lat + ',"longitude":' + lng + ',"radius":' + radiusOfSafezone + ',"notifications":"all"}';
    } else {
        arrayOfSafezones.pop();
        arrayOfSafezones.push(safezone);
        objJsonSafezone.safezones[objJsonSafezone.safezones.length - 1].address = nameAddress;
        objJsonSafezone.safezones[objJsonSafezone.safezones.length - 1].latitude = lat;
        objJsonSafezone.safezones[objJsonSafezone.safezones.length - 1].longitude = lng;
    }
    ;
    constructInfoBubbleToSafezone(marker, nameAddress, nameAddress, nameAddress, lat, lng, radiusOfSafezone);

    marker.setDraggable(draggable);
    map.addMarker(marker);

    if (!update) {
        window.objJsonSafezone = new Object();
        window.objJsonSafezone.safezones = new Array();
        arrayMarkersSafezones.push(marker);
        window.objJsonSafezone.safezones.push(jQuery.parseJSON(dataJsonSend));
//window.objJsonSafezone.push(jQuery.parseJSON('{"safezones":['+dataJsonSend+']}'));

    } else {
        arrayMarkersSafezones.pop();
        arrayMarkersSafezones.push(marker);
    }
    showSearchAddress();
}

function getCurrentTimeInMilliSeconds() {
    var d = new Date();
    return d.getTime();
}

function selectGeofence(pos) {
    selectedGeofence = pos;
    addHighlightToTheMarker(newArrayPoint[pos][0], newArrayPoint[pos][1])
    //showStreetViewBasedInCoordinates(newArrayPoint[pos][0],newArrayPoint[pos][1]);
    showStreetView(newArrayPoint[pos][0], newArrayPoint[pos][1], newAddress[pos][1]);
    document.getElementById('bt_next').style.visibility = "visible";
    map.click.removeAllHandlers(); //important to remove the handler when already selected the geofence
}

function pressNext(pos) {
    map.removeAllPolylines();
    map.removeAllMarkers();
    if (insertSafezones == true) {
        console.log(pos + " - " + newAddress[pos]);
        insertSafezoneInMap(newAddress[pos], newArrayPoint[pos][0], newArrayPoint[pos][1], false);
        document.getElementById("txt_name").value = newAddress[pos];
    }
    map.click.removeAllHandlers(); //important to remove the handler when already selected the geofence
}

function removePreviousSearch() {
    document.getElementById('myDynamicTable').style.display = "none";
    document.getElementById('pano').style.visibility = "hidden";

    map.removeAllPolylines();
    map.removeAllMarkers();

    newMarkers = [];
    newAddress = [];
    newArrayPoint = [];

}

function addHighlightToTheMarker(lat, log) {
    var latlonCenterOfTheMap = new mxn.LatLonPoint(lat, log);
    map.setCenterAndZoom(latlonCenterOfTheMap, 13);
}

function addTableOfSearchResult(arrayaddress) {
    if (typeof arrayaddress === 'string') {
        arrayaddress = [arrayaddress];
        address = [];
        address = arrayaddress;
    }

    document.getElementById("myDynamicTable").innerHTML = "";
    var myTableDiv = document.getElementById("myDynamicTable");

    var table = document.createElement('TABLE');
    table.className = "table table-bordered";

    var tableHead = document.createElement('THEAD');
    table.appendChild(tableHead);
    var tr = document.createElement('TR');

    var td = document.createElement('TD');
    td.appendChild(document.createTextNode("Resultados Encontrados:"));
    tr.appendChild(td);
    tableHead.appendChild(tr);



    var tableBody = document.createElement('TBODY');
    table.appendChild(tableBody);

    var tr = document.createElement('TR');

    var td = document.createElement('TD');
    //td.appendChild(document.createTextNode("Resultados Encontrados:"));
    //tr.appendChild(td);
    //tableBody.appendChild(tr);

    for (var i = 0; i < arrayaddress.length; i++) {
        tr = document.createElement('TR');
        tableBody.appendChild(tr);
        td = document.createElement('TD');
        var a = document.createElement('a');
        var linkText = document.createTextNode(arrayaddress[i]);
        a.appendChild(linkText);
        a.title = arrayaddress[i];
        a.href = "javascript:selectGeofence(" + i + ");;";
        td.appendChild(a)
        tr.appendChild(td);
    }
    document.getElementById('myDynamicTable').style.display = "";

    myTableDiv.appendChild(table);
}


function showStreetViewBasedInCoordinates(lat, lon) {
    var streetViewService = new google.maps.StreetViewService();

    var fenway = new google.maps.LatLng(lat, lon);
    // Note: constructed panorama objects have visible: true
    // set by default.
    var panoOptions = {
        position: fenway,
        addressControlOptions: {
            position: google.maps.ControlPosition.BOTTOM_CENTER
        },
        linksControl: false,
        panControl: false,
        zoomControlOptions: {
            style: google.maps.ZoomControlStyle.SMALL
        },
        enableCloseButton: false
    };

    streetViewService.getPanoramaByLocation(fenway, 50, function(streetViewPanoramaData, status) {
        if (status === google.maps.StreetViewStatus.OK) {
            // ok
            console.log("streetView founded");
            var panorama = new google.maps.StreetViewPanorama(document.getElementById('pano'), panoOptions);
            document.getElementById('pano').style.visibility = "visible";
        } else {
            console.log("streetView not found!!!");
            document.getElementById('pano').style.visibility = "hidden";
            // no street view available in this range, or some error occurred
        }

    });
}

function showStreetView(lat, lon, addressToLookup) {
    // initialize the geocoder API functions. We need this to convert the address to a geolocation (GPS coordinates)
    // then we call the geocode function with the address we want to use as parameter
    var latlng = new google.maps.LatLng(lat, lon);

    geocoder.geocode({'address': addressToLookup + ',Portugal'}, function(results, status) {
        // set the lookTo var to contain the coordinates of the address entered above
        var lookTo = latlng;
        // if the address is found and the geocoder function returned valid coordinates
        if (status == google.maps.GeocoderStatus.OK) {
            // set the options for the panorama view
            var panoOptions = {
                position: lookTo,
                panControl: false,
                addressControl: false,
                linksControl: false,
                zoomControlOptions: false
            };
            // initialize a new panorama API object and point to the element with ID streetview as container
            var pano = new google.maps.StreetViewPanorama(document.getElementById('pano'), panoOptions);
            // initialize a new streetviewService object
            var service = new google.maps.StreetViewService;
            // call the "getPanoramaByLocation" function of the Streetview Services to return the closest streetview position for the entered coordinates
            service.getPanoramaByLocation(pano.getPosition(), 50, function(panoData) {
                // if the function returned a result
                if (panoData != null) {
                    // the GPS coordinates of the streetview camera position
                    var panoCenter = panoData.location.latLng;
                    // this is where the magic happens!
                    // the "computeHeading" function calculates the heading with the two GPS coordinates entered as parameters
                    var heading = google.maps.geometry.spherical.computeHeading(panoCenter, lookTo);
                    //console.log("heading "+heading);
                    // now we know the heading (camera direction, elevation, zoom, etc) set this as parameters to the panorama object
                    var pov = pano.getPov();
                    pov.heading = heading;
                    pano.setPov(pov);
                    // set a marker on the location we are looking at, to verify the calculations were correct
                    var marker = new google.maps.Marker({
                        map: pano,
                        position: lookTo
                    });
                    document.getElementById('pano').style.visibility = "visible";
                } else {
                    // no streetview found :(
                    showStreetViewBasedInCoordinates(lat, lon);
                }
            });
        } else {
            // there were no coordinates found for the address entered (or the address was not found)
            showStreetViewBasedInCoordinates(lat, lon);
        }
    });
}

function changeRadiusOfMarker(index, radius) {
    console.log("changeRadiusOfMarker index" + index);
    map.removeAllPolylines();
    map.addPolyline(arrayOfSafezones[index].getPolyline(radius / 1000, '#F00'));
    objJsonSafezone.safezones[index].radius = radius;
}

function myboxopened(event_name, event_source, event_args) {
    // alert('Opened bubble attached to marker at ' + event_source.location);
    for (var i = 0; i < newMarkers.length; i++) {
        newMarkers[i].closeBubble();
    }
    for (var i = 0; i < map.polylines.length; i++) {
        arrayMarkersSafezones
    }
}

function initializeAutoComplete() {

    var input = (document.getElementById('address'));
    var autocomplete = new google.maps.places.Autocomplete(input);

    var componentRestrictions = {country: 'pt'};
    autocomplete.setComponentRestrictions(componentRestrictions);

    google.maps.event.addListener(autocomplete, 'place_changed', function() {
        removePreviousSearch();

        var place = autocomplete.getPlace();
        if (!place.geometry) {
            // Inform the user that the place was not found and return.
            //console.log("Autocomplete not used so i try search by address");
            searchAddress();
            input.className = 'notfound';
            return;
        }
        // auto complete select only one marker
        insertTempMarkers(place.name, place.geometry.location.lat(), place.geometry.location.lng());
        addTableOfSearchResult(newAddress);
        selectGeofence(0); //auto select 
    });
}