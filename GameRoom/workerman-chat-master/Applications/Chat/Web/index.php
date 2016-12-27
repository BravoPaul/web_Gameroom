<?php
/**
 * Created by PhpStorm.
 * User: paul
 * Date: 22/03/16
 * Time: 17:21
 */
?>
<html lang="en">
<head>

    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <!-- Include these three JS files: -->
    <script type="text/javascript" src="/js/swfobject.js"></script>
    <script type="text/javascript" src="/js/web_socket.js"></script>
    <script type="text/javascript" src="/js/jquery.min.js"></script>


    <style>
        #main{
            width:98%;
        }

        #sidebarmain{
            margin: 5cm 0 1cm 0;
            float:left;
            width:65%;
            height:70%

        }

        #sidebar{
            margin: 0 0 0 0;
            float:left;
            width:65%;
            height:70%
        }

        #sidebar2{
            margin: 0 0 0 0;
            float:left;
            width:65%;
            height:25%
        }

        #content{
            margin: 5cm 0 0 0;
            float:right;
            width:25%;

        }



    </style>

    <style>
        body{background-image:url(maxresdefault.jpg) }

        caption {font-size: 1.7em; color: #333; text-align: left;}
        table { padding: 0; border-collapse: collapse; width: 100%; filter:alpha(opacity=30);
            opacity:.8;}
        td, th {padding: 10px 4px; border-bottom: 1px solid #EEE;}
        td + td {border-left: 1px solid #FAFAFA; color: #999;}
        td + td + td {color: #666; border-left: none;}
        td a {color: #444; text-decoration: none; text-align: right;}
        td a, th a {display: block; width: 100%;}
        td a:hover {background: #444; color: #FFF;}
        tfoot th {text-align: right;}
        th {text-align: left;}
        th + th {text-align: right;}
        th + th + th {text-align: left;}
        th a {color: #F06; text-decoration: none; font-size: 1.1em;}
        th a:visited {color: #F69;}
        th a:hover {color: #F06; text-decoration: underline;}
        thead tr, tfoot tr {color: #555; font-size: 0.8em;}
        tr {font: 12px sans-serif; background: url(prettyinpink_row.png) repeat-x #F8F8F8; color: #666;}
        tr:hover {background: #FFF;}
    </style>


    <script type="text/javascript">
        if (typeof console == "undefined") {    this.console = { log: function (msg) {  } };}
        WEB_SOCKET_SWF_LOCATION = "/swf/WebSocketMain.swf";
        WEB_SOCKET_DEBUG = true;
        var ws, name,client_list={},wait=0;

        // 连接服务端
        function connect() {
            // 创建websocket
            ws = new WebSocket("ws://"+document.domain+":7272");
            // 当socket连接打开时，输入用户名
            ws.onopen = onopen;
            // 当有消息时根据消息类型显示不同信息
            ws.onmessage = onmessage;
            ws.onclose = function() {
                console.log("连接关闭，定时重连");
                connect();
            };
            ws.onerror = function() {
                console.log("出现错误");
            };
        }

        // 连接建立时发送登录信息
        function onopen()
        {
            if(!name)
            {
                show_prompt();
            }
            // 登录
            var login_data = '{"type":"login","client_name":"'+name.replace(/"/g, '\\"')+'","room_id":"<?php echo isset($_GET['room_id']) ? $_GET['room_id'] : 1?>"}';
            console.log("websocket握手成功，发送登录数据:"+login_data);
            ws.send(login_data);
        }

        function onmessage(e)
        {
            console.log(e.data);
            var data = eval("("+e.data+")");
            switch(data['type']){
                // 服务端ping客户端
                case 'ping':
                    ws.send('{"type":"pong"}');
                    break;
                // 登录 更新用户列表
                case 'login':
                    //{"type":"login","client_id":xxx,"client_name":"xxx","client_list":"[...]","time":"xxx"}
                    if(data['client_list'])
                    {
                        client_list = data['client_list'];
                    }

                    flush_client_list();
                    console.log(data['client_name']+"登录成功");
                    break;

                case 'say':
                    //{"type":"say","from_client_id":xxx,"to_client_id":"all/client_id","content":"xxx","time":"xxx"}
                    say(data['from_client_id'], data['from_client_name'], data['content'], data['time']);
                    break;
                // 用户退出 更新用户列表
                case 'logout':
                    delete client_list[data['from_client_id']];
                    flush_client_list();
                    break;
                case 'invite':
                    getInvite(data['from_client_id'],data['content']);
                    break;
                case 'gameStart':
                    if (confirm("You will have game with "+data['from_client_name'])){
                        window.open ("http://192.168.0.10:55152",""+name+"");
                    }
                    break;
                case 'refuse':
                    alert(data['from_client_name']+"has refused");
                    break;

            }
        }


        function say(from_client_id, from_client_name, content,time){
            $("#dialog").append('<div class="speech_item"><img src="http://lorempixel.com/38/38/?'+from_client_id+'" class="user_icon" /> '+from_client_name+' <br> '+time+'<div style="clear:both;"></div><p class="triangle-isosceles top">'+content+'</p></div>');
        }

        function reInvite(button,ifAccept){
            var to_client_id = button.value;
            var to_client_name = client_list[to_client_id]['client_name'];

            if(ifAccept==1){

                ws.send('{"type":"reInvite","to_client_id":"'+to_client_id+'","to_client_name":"'+to_client_name+'"}');
                window.open (window.open ("http://192.168.0.10:55152",""+name+""));
            }
            else {
                ws.send('{"type":"refuse","to_client_id":"'+to_client_id+'","to_client_name":"'+to_client_name+'"}');
            }
        }

        function getInvite(id,content){
            //alert("xianshi le ");
            var invitelist_window =  $("#invitelist");
            var trHTML = "<tr><td>"+content+"</td><td><button value='"+id+"' style=\"cursor:pointer\" onclick='reInvite(this,1)'>accept</button></td><td><button value='"+id+"' style=\"cursor:pointer\" onclick='reInvite(this,2)'>refuse</button></td></tr>";
            invitelist_window.append(trHTML);
        }

        function flush_client_list(){

            var userlist_window =  $("#userlist");

            var client_list_slelect = $("#client_list");
            userlist_window.empty();
            client_list_slelect.empty();
            client_list_slelect.append('<option value="all" id="cli_all">All the people</option>');

            for(var p in client_list){
                var trHTML;
                if (client_list[p]['client_name']==name)    {
                    trHTML = "<tr><td>"+client_list[p]['client_name']+"</td><td>"+client_list[p]['client_note']+"</td><td>"+client_list[p]['client_text']+"</td><td><button  style=\"cursor:pointer\" hidden>Invite</button></td></tr>";
                }
                else{
                     trHTML = "<tr><td>"+client_list[p]['client_name']+"</td><td>"+client_list[p]['client_note']+"</td><td>"+client_list[p]['client_text']+"</td><td><button  value='"+p+"'  style=\"cursor:pointer\" onclick='onInvite(this)'>Invite</button></td></tr>";
                }

                userlist_window.append(trHTML);
                //alert("hahah");
                client_list_slelect.append('<option value="'+p+'">'+client_list[p]['client_name']+'</option>');
            }

            $("#client_list").val(select_client_id);
        }

        function onSubmit(){

            var input = document.getElementById("textarea");
            var to_client_id = $("#client_list option:selected").attr("value");
            var to_client_name = $("#client_list option:selected").text();
            ws.send('{"type":"say","to_client_id":"'+to_client_id+'","to_client_name":"'+to_client_name+'","content":"'+input.value.replace(/"/g, '\\"').replace(/\n/g,'\\n').replace(/\r/g, '\\r')+'"}');
            input.value = "";
            input.focus();

        }

        function onInvite(button){
            //alert(this);
            var to_client_id = button.value;
            var to_client_name = client_list[to_client_id]['client_name'];
            ws.send('{"type":"invite","to_client_id":"'+to_client_id+'","to_client_name":"'+to_client_name+'"}');

        }

        </script>

    <meta charset="UTF-8">
    <title>chess</title>
</head>
<body  onload="connect();">
<div align="center" >
    <p><font size="40" face="arial" color="red" >WELCOME TO GAMEROOM</font></p>
</div>

<div id="main">
    <div id="sidebarmain">
        <div id="sidebar">
            <table id = "userlist">
            </table>
        </div>
        <div id="sidebar2">
            <table id = "invitelist">
            </table>
        </div>
    </div>

    <div id="content">
        <div class="thumbnail">
            <div class="caption" id="dialog"></div>
        </div>
        <form onsubmit="onSubmit(); return false;">
            <select style="margin-bottom:8px" id="client_list">
                <option value="all">All the people</option>
            </select>
            <textarea class="textarea thumbnail" id="textarea"></textarea>
            <div class="say-btn"><input type="submit" class="btn btn-default" value="send" /></div>
        </form>

    </div>

</div>




</body>
</html>
