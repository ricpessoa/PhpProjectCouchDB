<!DOCTYPE html>
<!-- 
/**
 * @Author : ANHVNSE02067 <anhvnse@gmail.com>
 * @Website: www.nhatanh.net
 */
-->
<script>
    // Client here
    var socket = null;
    var uri = "ws://192.168.0.104:2207";
    var color = 'green';
    var username = 'guest_' + $.now();

    function parseMessage(data) {
        var jsonObj = JSON.parse(data);
        return jsonObj;
    }

    function serializeMessage(message) {
        var obj = {
            'username': username,
            'color': color,
            'message': message
        }
        return JSON.stringify(obj);
    }


    function renderMessage2(message) {
        return  message.message;
    }



    function connect() {
        socket = new WebSocket(uri);
        if (!socket || socket == undefined)
            return false;
        socket.onopen = function() {
            writeToScreen('Connected to server waitting for notification of device');
        }
        socket.onerror = function() {
            writeToScreen('Some error trying connect to server notifications. Try again later.');
        }
        socket.onclose = function() {
            $('#send').prop('disabled', true);
            $('#close').prop('disabled', true);
            $('#connect').prop('disabled', false);
            $('#username').prop('disabled', false);
            $('#color').prop('disabled', false);
            writeToScreen('Connection closed!');
        }
        socket.onmessage = function(e) {
            console.log(e.data);
            writeToScreen(e.data);
        }
        // Init user data
        username = '<?php echo User::current_user(); ?>';
        color = $('#color').val();
        // Enable send and close button
        $('#send').prop('disabled', false);
        $('#close').prop('disabled', false);
        $('#connect').prop('disabled', true);
        $('#username').prop('disabled', true);
        $('#color').prop('disabled', true);
    }
    function close() {
        socket.close();
    }
    function writeToScreen(msg) {
        console.log('write screen');
        msg = parseMessage(msg);
        $('#screenMonitoring').append(renderMessage2(msg) + "\n");

        screen.animate({scrollTop: screen.height()}, 10);
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
        // Clear input
        $('#message').val('');
    }
    $(document).ready(function() {
        $('#message').focus();
        $('#frmInput').submit(function() {
            sendMessage();
        });
        $('#connect').click(function() {
            connect();
        });
        $('#close').click(function() {
            close();
        });
        $('#clear').click(function() {
            clearScreen();
        });
    });
</script>
<div class="bs-docs-example span10">
    <textarea id="screenMonitoring" class="span10" rows="20" ></textarea>
</div>

<form id="frmInput" action="" onsubmit="return false;">
    <div id="input">
        <input type="text" id="username" value="guest">
        <input type="text" id="color" value="green">
        <input type="text" id="message" placeholder="Message here..">
        <button type="submit" id="send" disabled>Send</button>
        <button type="button" id="connect">Connect</button>
        <button type="button" id="close" disabled>Close</button>
        <button type="button" id="clear">Clear</button>
    </div>
</form>
