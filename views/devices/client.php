<!DOCTYPE html>
<!-- 
/**
 * @Author : ANHVNSE02067 <anhvnse@gmail.com>
 * @Website: www.nhatanh.net
 */
http://www.sanwebe.com/2013/05/chat-using-websocket-php-socket/comment-page-1#comment-12652
http://www.nhatanh.net/blog/websocket-php-html5-tutorial-p2-realtime-chatroom.html
//http://www.codeproject.com/Tips/418814/Socket-Programming-in-PHP
http://www.binarytides.com/udp-socket-programming-in-php/
https://github.com/reactphp/socket
-->
<script>
    // Client here
    var socket = null;
    var uri = "ws://192.168.0.104:8000/monitoring_devices";

    function parseMessage(data) {
        var jsonObj = JSON.parse(data);
        return jsonObj;
    }

    function serializeMessage(message) {
        var obj = {
            'action': 'echo',
            'data': message
        }
        return JSON.stringify(obj);
    }

    function connect() {
        socket = new WebSocket(uri);
        if (!socket || socket == undefined)
            return false;
        socket.onopen = function() {
            $('#lbl_connected').show();
            $('#lbl_disconnected').hide();
            $('#close').show();
            $('#connect').hide();
            writeToScreen('Connected to server waitting for notification of device');
        }
        socket.onerror = function() {
            $('#lbl_connected').hide();
            $('#lbl_disconnected').show();
            writeToScreen('Some error trying connect to server notifications. Try again later.');
        }
        socket.onclose = function() {
            $('#lbl_connected').hide();
            $('#lbl_disconnected').show();
            $('#close').hide();
            $('#connect').show();
            writeToScreen('Connection closed!');
        }
        socket.onmessage = function(e) {
            var json = parseMessage(e.data);
//            console.log("json:" + json);
//            console.log("action:" + json.action);
//            console.log("username:" + json.data[0].username);
//            console.log(json.data[0].lat + " - " + json.data[0].log);
//            console.log(json.data[0].tmp);
//            console.log(json.data[0].bat);
//            console.log(json.data[0].press);
//            console.log(json.data[0].username + " ?? " + '<?php echo User::current_user(); ?>')
            //if (json.data[0].username == '<?php echo User::current_user(); ?>') {
            var str = "--------------------------------------------\n";
            str += "Device MAC Adrress: " + json.data[0].mac_address + " on " + json.data[0].time + " \n";
            if (json.data[0].lat != "" & json.data[0].log != "") {
                str += "\t Sensor GPS: latitude:" + json.data[0].lat + " longitude:" + json.data[0].log + "\n";
            }
            if (json.data[0].tmp != "") {
                str += "\t Sensor Temperature: " + json.data[0].tmp + " ºC\n";
            }
            if (json.data[0].bat != "") {
                str += "\t Battery Level: " + json.data[0].bat + " %\n";
            }
            if (json.data[0].press != "") {
                str += "\t Sensor Panic Button: pressed " + json.data[0].press + "\n";
            }
            str += "--------------------------------------------";
            writeToScreen(str);
        }
    }
    function close() {
        socket.close();
    }
    function writeToScreen(msg) {
        //console.log('write screen');
        $('#screenMonitoring').append(msg + "\n");
        $('#screenMonitoring').scrollTop(psconsole[0].scrollHeight - psconsole.height()); //scroll down
    }
    function clearScreen() {
        $('#screenMonitoring').html('');
    }
    function sendMessage() {
        if (!socket || socket == undefined)
            return false;
        var mess = $.trim($('#message').val());
        if (mess == '')
            return;
        socket.send(serializeMessage(mess));
    }
    $(document).ready(function() {
        $('#connect').click(function() {
            connect();
        });
        $('#close').click(function() {
            close();
        });
        $('#clear').click(function() {
            clearScreen();
        });
        connect();
    });
</script>



<div class="bs-docs-example span10">
    <span id="lbl_connected" class="label label-success pull-right">Connected</span>
    <span id="lbl_disconnected" class="label pull-right">Disconnected</span>
    <textarea id="screenMonitoring" class="span10" style="margin-top: 15px;"rows="20" ></textarea>
</div>
<form id="frmInput" action="" class="pull-right" onsubmit="return false;">
    <div id="input">
        <!--<input type="text" id="message" value="test client">
        <button type="submit" id="send" disabled>Send</button>-->
        <button type="button" class="btn" id="status" disabled>Get Status</button>
        <button type="button" class="btn btn-success" id="connect" hidden>Connect</button>
        <button type="button" class="btn btn-danger" id="close" hidden>Disconnect</button>
        <button type="button" class="btn btn-danger" id="clear">Clear</button>
    </div>
</form>
