<!--<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>-->
 <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDqUwWysKF_x_SkHDgB-q_NeaqBHpPTlME&sensor=false"></script>
<script src="http://mapstraction.com/mxn/build/latest/mxn.js?(googlev3)" type="text/javascript"></script>
<!-- Open Layer 
<script src="http://openlayers.org/api/OpenLayers.js"></script>
<script src="http://mapstraction.com/mxn/build/latest/mxn.js?(openlayers)" type="text/javascript"></script>
-->

<style type="text/css">
    #map {
        height: 400px;
    }
</style>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span4">
            <!--Sidebar content-->
            <table class="table table-striped">
                <caption>...</caption>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Safezone</th>
                        <th>Option</th>

                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Safezone 1</td>
                        <td>Edit Delete</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Safezone 2</td>
                        <td>Edit Delete</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Safezone 3</td>
                        <td>Edit Delete</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>Safezone 4</td>
                        <td>Edit Delete</td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>Safezone 5</td>
                        <td>Edit Delete</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="span8">
            <!--Body content-->
            <div id="map"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        //load_map();
        //var map = new mxn.Mapstraction('map', 'openlayers');
              window.map = new mxn.Mapstraction('map', 'googlev3'); 
        var latlon = new mxn.LatLonPoint(51.50733, -0.12769);

        map.setCenterAndZoom(latlon, 10);
    });


</script>