<h3>Sensor GPS</h3>

<?php
$msgps = MSGPS::getMonitoringSensorByKeys(User::current_user(), $device->_id, "GPS");
//echo '<br> json'.$msgps->getArrayOfGpsTime();
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
                this map show the points in map
            </div>
            <div class="row-fluid">
                <div class="span5 thumbnail">
                    <h3 class="well well-small">Last Locations:</h3>
                    <?php
                    $z = 1;
                    foreach ($msgps as $_gps) {
                        ?>
                        <div class="media">
                            <a class="pull-left">
                                <img class="media-object thumbnail" src="https://cbks0.google.com/cbk?output=thumbnail&w=120&h=120&ll=<?php echo $_gps->latitude; ?>,<?php echo $_gps->longitude; ?>&thumb=0" style="width: 64px; height: 64px;">
                            </a>
                            <div class="media-body">
                                <h4 class="media-heading">Check-in</h4>
                                <div id="streatname<?php echo '' . $z; ?>"></div>
                                <?php echo 'in '. $_gps->timestamp; ?>
                                <script>codeLatLng(<?php echo $_gps->latitude; ?>,<?php echo $_gps->longitude; ?>,<?php echo $z ?>, function(location, index) {
                                        document.getElementById("streatname" + index).innerHTML = location;
                                    });</script>
                            </div>
                        </div>
                        <?php
                        $z = $z + 1;
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>