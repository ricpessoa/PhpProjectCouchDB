<legend>My Safezones</legend>
<?php if ($numberSafezones == 0) { ?>
    <div class = "alert alert-info">
        Do not have safezones in this sensor GPS to add press "Add Safezone"
    </div>
<?php }
?>
<form action="<?php echo $this->make_route('/safezone/newsafezone') ?>" method="get">	
    <button id="create_safezone" class="btn btn-success">Add Safezone</button>
</form>
<?php if ($numberSafezones != 0) { ?>
    <!-- Open Layer 
    <script src="http://openlayers.org/api/OpenLayers.js"></script>
    <script src="http://mapstraction.com/mxn/build/latest/mxn.js?(openlayers)" type="text/javascript"></script>
    -->
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDqUwWysKF_x_SkHDgB-q_NeaqBHpPTlME&sensor=false"></script>
    <script src="http://mapstraction.com/mxn/build/latest/mxn.js?(googlev3)" type="text/javascript"></script>
    <script type="text/javascript" src="<?php echo $this->make_route('/js/mapfunctions.js') ?>"></script>

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
                        $str_safezones = "";
                        foreach ($safezones as $safezone):
                            $str_safezones .= $safezone->to_jsonString();
                            ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td><?php echo $safezone->name; ?></td>
                                <td>
                                    <button id="edit_safezone" class="btn btn-info btn-small">Edit</button>
                                    <button data-toggle="modal" data-id="<?php echo $safezone->_id; ?>" data-rev="<?php echo $safezone->_rev; ?>" title="Delete this Safezone" class="open-deleteSafezoneModal btn btn-danger  btn-small" href="#deleteSafezoneModal">Delete</button>
                                </td>
                            </tr>
                            <?php
                            $i = $i + 1;
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

    <!-- MODAL DELETE SAFEZONE -->
    <div class="modal fade" id="deleteSafezoneModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display:none">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Delete Safezone</h4>
                </div>
                <div class="modal-body">
                    Are you sure you want to permanently delete the Safezone?
                </div>
                <div class="modal-footer">
                    <form id="form_delete_safezone" method="post">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            //var map = new mxn.Mapstraction('map', 'openlayers');
            window.map = new mxn.Mapstraction('map', 'googlev3');
            //var latlon = new mxn.LatLonPoint(51.50733, -0.12769);
            //map.addExtras();
            map.enableScrollWheelZoom();
            //window.geocoder = new google.maps.Geocoder();
            //map.setCenterAndZoom(latlon, 10);
            //console.log('<?php echo "[" . substr($str_safezones, 0, -1) . "]"; ?>');
            getSafezones('<?php echo '{"safezones"' . ":[" . substr($str_safezones, 0, -1) . "]}"; ?>')
        });

        $(document).on("click", ".open-deleteSafezoneModal", function() {
            var myDocId = $(this).data('id');
            var myDocRev = $(this).data('rev');
            var finalURL = '/PhpProjectCouchDB/deletesafezone/' + myDocId + '/' + myDocRev;
            $(".modal-footer #form_delete_safezone").attr('action', finalURL);

        });
    </script>

<?php } ?>
