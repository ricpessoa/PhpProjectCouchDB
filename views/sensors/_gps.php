<h3>Sensor GPS</h3>
<?php
$msgps = MSGPS::getMonitoringSensorByKeys(User::current_user(), $device->_id, "GPS");
//echo '<br> json'.$msgps->getArrayOfGpsTime();
//echo MSGPS::getArrayOfGPSToJson($msgps);

if ($msgps === NULL) {
    ?>
    <div class="alert alert-info">
        Not yet received any information from GPS sensor!
    </div>
<?php } else {
    ?>
    <div class="container-fluid<?php echo $i; ?>">
        <div class="row-fluid">
            <div class="span7">
                <!--Sidebar content-->
                <div id="map<?php echo $i ?>" style="width: 600px; height: 400px;"></div>
            </div>
            <div class="row-fluid">
                <div class="span5 thumbnail">
                    <h3 class="well well-small">Last Locations:</h3>
                    <?php
                    $z = 1;
                    $msgps = MSGPS::getMonitoringSensorByKeys(User::current_user(), $device->_id, "GPS");
                    foreach ($msgps as $_gps) {
                        ?>
                        <div class="media">
                            <a class="pull-left">
                                <img class="media-object thumbnail" src="https://cbks0.google.com/cbk?output=thumbnail&w=120&h=120&ll=<?php echo $_gps->latitude; ?>,<?php echo $_gps->longitude; ?>&thumb=0" style="width: 64px; height: 64px;">
                            </a>
                            <div class="media-body">
                                <h4 class="media-heading"><?php echo $_gps->notification; ?></h4>
                                <!--<div id="streatname<?php echo '' . $z; ?>"></div>-->
                                <?php echo $_gps->address . "<br>"; ?>
                                <?php echo 'in ' . $_gps->timestamp; ?>
                                <script>
                                    //$(function() {
                                    //    window.map<?php echo $i; ?> = new mxn.Mapstraction('map<?php echo $i; ?>', 'googlev3');
                                    //getPOIS(<?php echo MSGPS::getArrayOfGPSToJson($msgps); ?>);
                                    //getPOIS('<?php echo '{"pois"' . ":" . MSGPS::getArrayOfGPSToJson($msgps) . "}"; ?>');
                                    //});
                                    //codeLatLng(<?php echo $_gps->latitude; ?>,<?php echo $_gps->longitude; ?>,<?php echo $z ?>, function(location, index) {
                                    //    document.getElementById("streatname" + index).innerHTML = location;
                                    //    getPOIS(<?php echo $_gps->latitude; ?>,<?php echo $_gps->longitude; ?>,<?php echo $i; ?>);
                                    //});
                                </script>
                            </div>
                        </div>
                        <?php
                        $z = $z + 1;
                    }
                    ?>
                    <script>
                        window.map<?php echo $i; ?> = new mxn.Mapstraction('map<?php echo $i; ?>', 'googlev3');
                        getPOIS('<?php echo '{"pois"' . ":" . MSGPS::getArrayOfGPSToJson($msgps) . "}"; ?>',<?php echo $i; ?>);
                    </script>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<script type="text/javascript">
    $(document).ready(function() {

        $('#myTab a').on('shown', function(e) {
            //alert($('#myTab .active').text());
            //alert($('#myTab .active a').attr('href'));
            var str = $('#myTab .active a').attr('href');
            var str = str.substr(str.length - 1, str.length);
            window["map" + str].resizeTo('600px', '400px');
            window["map" + str].autoCenterAndZoom();
        });
    });
</script>