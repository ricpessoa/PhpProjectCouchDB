<div class="well">
    <h4>Safezone Settings</h4>
    <?php if ($numberSafezones == 0) { ?>
        <div class = "alert alert-info">
            Do not have safezones in this sensor GPS to add press "Add Safezone"
        </div>
        <?php
    }
    ?>
    <form action="<?php echo $this->make_route('/safezone/newsafezone') ?>" method="post">	
        <button id="create_safezone" name="create_safezone" type="input" class="btn btn-success" value="<?php echo $deviceID; ?>"><i class="icon-plus icon-white"></i> Add Safezone</button> 
    </form>

    <?php if ($numberSafezones != 0) { ?>
        <!-- Open Layer 
        <script src="http://openlayers.org/api/OpenLayers.js"></script>
        <script src="http://mapstraction.com/mxn/build/latest/mxn.js?(openlayers)" type="text/javascript"></script>
        -->
        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC0MqefKdvXwvIfHcipfBjT9aP2eMH2Wks&sensor=false"></script>
        <script src="http://mapstraction.com/mxn/build/latest/mxn.js?(googlev3)" type="text/javascript"></script>
        <script type="text/javascript" src="<?php echo $this->make_route('/js/mapfunctions.js') ?>"></script>

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

                            foreach (json_decode($jsonSafezones) as $safezone):
                                ?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo $safezone->name; ?></td>
                                    <td>
                                        <form action="<?php echo $this->make_route('/safezone/newsafezone') ?>" method="post">
                                            <span><button id="edit_safezone" name="edit_safezone" class="btn btn-info btn-small" value="true"><i class="icon-pencil icon-white"></i> Edit</button></span>
                                            <input id="id_safezone_to_edit" name="id_safezone_to_edit" type="hidden" value="<?php echo $safezone->_id; ?>" >
                                            <samp><button data-toggle="modal" data-id="<?php echo $safezone->_id; ?>" data-rev="<?php echo $safezone->_rev; ?>" data-dev="<?php echo $safezone->device; ?>" title="Delete this Safezone" class="open-deleteSafezoneModal btn btn-danger  btn-small" href="#deleteSafezoneModal"><i class="icon-trash icon-white"></i> Delete</button></samp>
                                        </form>
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
                getSafezones('<?php echo '{"safezones"' . ":" . $jsonSafezones . "}"; ?>');
            });

            $(document).on("click", ".open-deleteSafezoneModal", function() {
                var myDocId = $(this).data('id');
                var myDocRev = $(this).data('rev');
                var myDocDev = $(this).data('dev');

                var finalURL = '/PhpProjectCouchDB/deletesafezone/' + myDocId + '/' + myDocRev + '/' + myDocDev;
                $(".modal-footer #form_delete_safezone").attr('action', finalURL);

            });
        </script>

    <?php } ?>

</div>