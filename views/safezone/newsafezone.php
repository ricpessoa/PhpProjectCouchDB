<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDqUwWysKF_x_SkHDgB-q_NeaqBHpPTlME&sensor=false&libraries=places"></script>
<!--<script src="http://mapstraction.com/mxn/build/latest/mxn.js?(googlev3)" type="text/javascript"></script>-->
<script src="http://mapstraction.com/mxn/build/latest/mxn.js?(googlev3,[geocoder])" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo $this->make_route('/js/mapfunctions.js') ?>"></script>

<style type="text/css">
    #map {
        height: 400px;
    }
</style>

<script>
    function updateTextInput(val) {
        console.log("updateTextInput" + (objJsonSafezone.safezones.length - 1) + "," + val);
        document.getElementById('radius').value = val;
        changeRadiusOfMarker(objJsonSafezone.safezones.length - 1, val);
    }

    function updateSlider(val) {
        console.log("updateSlider" + (objJsonSafezone.safezones.length - 1) + "," + val);
        document.getElementById('radiusSlider').value = val;
        changeRadiusOfMarker(objJsonSafezone.safezones.length - 1, val);
    }

    function showSearchAddress() {
        document.getElementById('div_search_address').style.display = "";

        if (map.markers.length > 0) {
            document.getElementById('bt_next').style.visibility = "";
        } else {
            document.getElementById('bt_next').style.visibility = "hidden";
        }

        //document.getElementById('div_buttons').style.visibility = "hidden";
        document.getElementById('bt_save').style.display = "none";
        document.getElementById('div_SelectNotification').style.display = "none";

        document.getElementById('bt_editLocation').style.display = "none";
        document.getElementById('editSafezone').style.display = "none";
        document.getElementById('bt_back').style.display = "none";
        document.getElementById('div_radius').style.display = "none";
    }

    function showEditRadius() {
        document.getElementById('editSafezone').style.display =  "";
        document.getElementById('div_radius').style.display  = "";
        document.getElementById('div_buttons').style.display = "";
        document.getElementById('bt_save').style.display = "";
        document.getElementById('div_SelectNotification').style.display = "";

        if (update) {
            document.getElementById('bt_back').style.display = "none";
            document.getElementById('bt_editLocation').style.display = "";
        }
        else {
            document.getElementById('bt_back').style.display = "";
            document.getElementById('bt_editLocation').style.display = "none";
        }

        document.getElementById('bt_next').style.visibility = "hidden";
        document.getElementById('div_search_address').style.display = "none";
        document.getElementById('myDynamicTable').style.display = "none"; //to remove the table of results


    }
</script>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span4">
            <!--Sidebar left content to form search-->
            <div id="div_search_address" class="row-fluid" style="margin-top: 10px;margin-bottom:10px;">
                <label class="control-label">Search street:</label>
                <input id="address" type="text" class="input-medium search-query">
                <button type="submit" class="btn" onclick="searchAddress();">Search</button>
            </div>
            <table id="myDynamicTable"></table>
            <div id="div_radius" class="row-fluid" style="margin-top: 10px;margin-bottom:10px;">
                <label class="control-label">Radius:</label>
                <input id="radiusSlider" type="range" class="input-medium" name="points" min="500" max="10000" value="500" onchange="updateTextInput(this.value);">
                <input id="radius" type="text" class="input-mini" value="500" onchange="updateSlider(this.value);">
            </div>
            <div id="editSafezone" style="margin-top: 10px;margin-bottom:10px;">
                <label class="control-label">Name:</label>
                <input id="txt_name" type="text" size="35" value="">
            </div>
            <div id="div_SelectNotification" class="row-fluid" style="margin-top: 10px;margin-bottom:10px;">
                <label class="control-label">Notification Settings:</label>
                <select id="notification_settings" class="input-medium">
                    <option id="NONE" value="NONE">None</option>
                    <option id="CHECK_INS_ONLY" value="CHECK_INS_ONLY">Check-in events</option>
                    <option id="CHECK_OUTS_ONLY" value="CHECK_OUTS_ONLY">Check-out events</option>
                    <option id="ALL" value="ALL">Both events</option>
                </select>
            </div>
            <div id = "div_buttons" class="row-fluid" style="margin-top: 10px;margin-bottom:10px;">
                <form id="form_new_safezone" action="<?php echo $this->make_route('/safezone') ?>" method="post">
                    <button id="bt_back" type="button" class="normalbutton" onclick="javascript:showSearchAddress()">Back</button>
                    <button id="bt_save" type="button" class="normalbutton" onclick="javascript:saveSafezoneInDb(objJsonSafezone.safezones.length - 1)">Save Safezone</button>
                    <button id="bt_editLocation" type="button" class="normalbutton" onclick="javascript:showSearchAddress();
                            /* passSafezoneOfPoiToTempMarker(objJsonSafezone.safezones[0].Address, objJsonSafezone.safezones[0].Latitude, objJsonSafezone.safezones[0].Longitude);*/">Edit Locations</button>
                    <button id="bt_next" type="button" class="normalbutton" onclick="javascript:pressNext(selectedGeofence);
                            showEditRadius();">Next</button>
                    <input id="safezone" type="hidden" name="safezone">
                </form>
            </div>
            <div id="pano" style="width: 300px; height: 250px;"></div>
        </div>
        <div class="span8">
            <!--Body content to MAP-->
            <div id="map"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    window.insertSafezones = true;
    window.update = false;
    $(document).ready(function() {
        //var map = new mxn.Mapstraction('map', 'openlayers');
        window.map = new mxn.Mapstraction('map', 'googlev3');
        var latlon = new mxn.LatLonPoint(51.50733, -0.12769);
        map.addExtras();
        map.enableScrollWheelZoom();
        window.geocoder = new google.maps.Geocoder();
        map.setCenterAndZoom(latlon, 10);
        //getSafezones('<%=@mySafezones%>');
        map.removeAllPolylines();
        map.removeAllMarkers();
        if (insertSafezones) {
            findUserLocation();
            insertGeofenceHandler();
            showSearchAddress();
        }
    });
    google.maps.event.addDomListener(window, 'load', initializeAutoComplete);
</script>