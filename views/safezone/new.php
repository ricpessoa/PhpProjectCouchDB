<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDqUwWysKF_x_SkHDgB-q_NeaqBHpPTlME&sensor=false"></script>
<script src="http://mapstraction.com/mxn/build/latest/mxn.js?(googlev3)" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo $this->make_route('/js/mapfunctions.js') ?>"></script>
<style type="text/css">
    #map {
        height: 400px;
    }
</style>
<script>
    function updateTextInput(val) {
        //console.log("updateTextInput"+(objJsonSafezone.safezones.length-1)+","+val);
        document.getElementById('radius').value = val + "m";
        document.getElementById('radius2').value = val+;

        
        //changeRadiusOfMarker(objJsonSafezone.safezones.length-1,val);
    }
    function updateSlider(val) {
        //console.log("updateSlider"+(objJsonSafezone.safezones.length-1)+","+val);
        document.getElementById('radiusSlider').value = val;
        //changeRadiusOfMarker(objJsonSafezone.safezones.length-1,val);
    }
</script>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span4">
            <!--Sidebar left content to form search-->
            <div id="div_search_address">
                <form class="form-search">
                    <input type="text" class="input-medium search-query">
                    <button type="submit" class="btn">Search</button>
                </form>
            </div>
            <div id="div_radius">
                <div class="controls">
                    <input id="radius2" type="number" min="500" max="10000" value="500" />
                    <p class="help-block">Must be higher than 500 and lower than 10000</p>
                </div>
                Radius: <input id="radius" type="text" size="6" value="500" onchange="updateSlider(this.value);">
                <input id="radiusSlider" type="range" name="points" min="500" max="10000" value="500" onchange="updateTextInput(this.value);">
            </div>

        </div>
        <div class="span8">
            <!--Body content to MAP-->
            <div id="map"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        //var map = new mxn.Mapstraction('map', 'openlayers');
        window.map = new mxn.Mapstraction('map', 'googlev3');
        var latlon = new mxn.LatLonPoint(51.50733, -0.12769);
        //map.addExtras();
        map.enableScrollWheelZoom();
        //window.geocoder = new google.maps.Geocoder();
        map.setCenterAndZoom(latlon, 10);

    });


</script>