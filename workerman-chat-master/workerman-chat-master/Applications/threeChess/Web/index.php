<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <script type="text/javascript" src="/js/swfobject.js"></script>
  <script type="text/javascript" src="/js/web_socket.js"></script>
  <script type="text/javascript" src="/js/jquery.min.js"></script>
    <link rel="stylesheet" href="css/Morpion.css" />
  <script type="text/javascript">
    if (typeof console == "undefined") {    this.console = { log: function (msg) {  } };}
    WEB_SOCKET_SWF_LOCATION = "/swf/WebSocketMain.swf";
    WEB_SOCKET_DEBUG = true;
    var ws, name, client_id,adverse_id,ready = 0,wait = 1,ready_adverse = 0,first = 0;

    // 连接服务端
       // 创建websocket
    ws = new WebSocket("ws://"+document.domain+":4242");
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

    // 连接建立时发送登录信息
    function onopen()
    {
        if(!name)
        {
            show_prompt();
        }
        // 登录
        var login_data = '{"type":"login","client_name":"'+name.replace(/"/g, '\\"')+'"}';
        console.log("websocket握手成功，发送登录数据:"+login_data);
        ws.send(login_data);
    }

    // 服务端发来消息时
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
                client_id = data['client_id'];
                break;
            case 'ready':
                adverse_id = data['adverse'];
                if(adverse_id>client_id){
                   // afficheur.sendMessage("attent l'autre joueur");
                }
                else{
                    if(ready_adverse==1){
                       // afficheur.sendMessage("vous pouvez commencer");
                        wait = 0;
                    }
                    first = 1;
                }
                break;

            case 'ready_adverse':
                //{"type":"login","client_id":xxx,"client_name":"xxx","client_list":"[...]","time":"xxx"
                if(ready!=0&&first==1){
                    wait = 0;
                }
                ready_adverse = 1;
                break;

            case 'pace':
                wait = 0;
                var lastPace = data['lastPace'];
                setSymbol(document.getElementById(lastPace),'O')

                break;
            case 'win':

                if(confirm("you have lost the game, do you want to play again?")){

                    var pions = document.querySelectorAll("#Jeu button");
                    resetJeu(pions);

                }

                if(first==1){
                    wait = 1;
                }
                else{
                    wait = 0;
                }



                break;
        }
    }

    // 输入姓名
    function show_prompt() {
        name = prompt('输入你的名字：', '');
        if (!name || name == 'null') {
            alert("输入名字为空或者为'null'，请重新输入！");
            show_prompt();
        }
    }
  </script>
    <meta name="viewport" content="width=device-width">
</head>
<body>
<h1>Le jeu du morpion</h1>
<div id="Jeu">
    <div>
        <button id="1" value="1"></button>
        <button id="2" value="2"></button>
        <button id="3" value="3"></button>
    </div>
    <div>
        <button id="4" value="4"></button>
        <button id="5" value="5"></button>
        <button id="6" value="6"></button>
    </div>
    <div>
        <button id="7" value="7"></button>
        <button id="8" value="8"></button>
        <button id="9" value="9"></button>
    </div>
    <div id="StatutJeu"></div>
    <p></p>
    <div >
        <input type="button" href="###" value="ready" onclick="getReady()">
    </div>
</div>
<script src="js/Morpion.js" type="text/javascript"></script>

</body>
</html>
