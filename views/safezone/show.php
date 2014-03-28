<!-- Open Layer 
<script src="http://openlayers.org/api/OpenLayers.js"></script>
<script src="http://mapstraction.com/mxn/build/latest/mxn.js?(openlayers)" type="text/javascript"></script>
-->
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDqUwWysKF_x_SkHDgB-q_NeaqBHpPTlME&sensor=false"></script>
<script src="http://mapstraction.com/mxn/build/latest/mxn.js?(googlev3)" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo $this->make_route('/js/mapfunctions.jsF') ?>"></script>

<style type="text/css">
    #map {
        height: 400px;
    }
</style>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span4">
            <!--Sidebar left content to TABLE SAFEZONE-->
            <table class="table table-striped">
                <caption>
                    <form action="<?php echo $this->make_route('/safezone') ?>" method="post">	
                        <button id="create_safezone" class="btn btn-primary">Add Safezone</button>
                    </form>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Option</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        foreach ($safezones as $safezone):
                            ?>

                            <tr>
                                <td><?php echo $i; ?></td>
                                <td><?php echo $safezone->name; ?></td>
                                <td>Edit Delete</td>
                            </tr>
                            <?php $i = $i + 1;
                        endforeach;
                        ?>

                    </tbody>
            </table>
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