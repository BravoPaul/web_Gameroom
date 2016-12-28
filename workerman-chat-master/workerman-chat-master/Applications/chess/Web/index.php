<html><head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>workerman-chat PHP聊天室 Websocket(HTLM5/Flash)+PHP多进程socket实时推送技术</title>
  <script type="text/javascript">
  //WebSocket = null;
  </script>

  <script type="text/javascript" src="/js/swfobject.js"></script>
  <script type="text/javascript" src="/js/web_socket.js"></script>
  <script type="text/javascript" src="/js/jquery.min.js"></script>

  <script type="text/javascript">
    if (typeof console == "undefined") {    this.console = { log: function (msg) {  } };}
    WEB_SOCKET_SWF_LOCATION = "/swf/WebSocketMain.swf";
    WEB_SOCKET_DEBUG = true;
    var ws, name, client_id,adverse_id,ready = 0,wait = 0,ready_adverse = 0,first = 0,gameStart = 0;

    // 连接服务端
       // 创建websocket
    ws = new WebSocket("ws://"+document.domain+":8282");
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
                //{"type":"login","client_id":xxx,"client_name":"xxx","client_list":"[...]","time":"xxx"
                adverse_id = data['adverse'];

                if(adverse_id>client_id){
                    wait = 1;
                }
                else{
                    first = 1;
                }
                break;
            case 'ready_adverse':
                //{"type":"login","client_id":xxx,"client_name":"xxx","client_list":"[...]","time":"xxx"
                if(ready!=0&&gameStart==0){
                    play.isPlay=true ;
                    com.get("chessRight").style.display = "none";
                    com.get("moveInfo").style.display = "block";
                    com.get("moveInfo").innerHTML="";
                    play.init();
                    com.get("tyroPlay").hide();
                }
                ready_adverse = 1;
                break;

            case 'pace':

                wait = 0;
                gameStart = 1;
                var lastPace = data['lastPace'];
                var myPace = lastPace.toString();
                var shuzuPace = myPace.split("");

                play.AIPlay([shuzuPace[0],9-shuzuPace[1],shuzuPace[2],9-shuzuPace[3]]);

                break;
            case 'win':

                if(confirm("you have won the game, do you want to play again?")){
                    play.isPlay=true ;
                    com.get("chessRight").style.display = "none";
                    com.get("moveInfo").style.display = "block";
                    com.get("moveInfo").innerHTML="";
                    play.depth = 3;
                    play.init();
                    if(first==1){
                        wait = 1;
                    }
                    else{
                        wait = 0;
                    }
                }



                break;
        }
    }

    // 输入姓名
    function show_prompt(){  
        name = prompt('输入你的名字：', '');
        if(!name || name=='null'){  
            alert("输入名字为空或者为'null'，请重新输入！");  
            show_prompt();
        }
    }  


  </script>
    <link href="../css/chess.css" rel="stylesheet" type="text/css">
</head>
<body onload="connect()">
<div class="box" id="box">
    <div class="chess_left">
        <canvas id="chess">对不起，您的浏览器不支持HTML5，请升级浏览器至IE9、firefox或者谷歌浏览器！</canvas>

        <div>
            <div class="bn_box" id="bnBox">
                <input type="button" name="offensivePlay" id="tyroPlay" value="ready" />
                <input type="button" name="offensivePlay" id="superPlay" value="中级水平"   hidden/>
                <!--<input type="button" name="button" id="" value="大师水平" disabled />

			<input type="button" name="offensivePlay" id="offensivePlay" value="先手开始" />
			<input type="button" name="defensivePlay" id="defensivePlay" value="后手开始" />
			-->
                <input type="button" name="regret" id="regretBn" value="悔棋"   hidden/>
                <input type="button" name="billBn" id="billBn" value="棋谱" class="bn_box" hidden/>
                <input type="button" name="stypeBn" id="stypeBn" value="skin"   /><input type="button" name="download" id="download" value=""  hidden/>
            </div>
        </div>


    </div>
    <div class="chess_right" id="chessRight">
        <select name="billList" id="billList">
        </select>
        <ol id="billBox" class="bill_box">
        </ol>
    </div>
    <div id="moveInfo" class="move_info"> </div>
</div>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/play.js"></script>

<div style="display:none">
    <script src="http://s25.cnzz.com/stat.php?id=98945&web_id=98945" language="JavaScript"></script>
</div>

</body>
</html>
