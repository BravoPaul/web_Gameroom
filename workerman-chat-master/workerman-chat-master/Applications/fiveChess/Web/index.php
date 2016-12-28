<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <script type="text/javascript" src="/js/swfobject.js"></script>
    <script type="text/javascript" src="/js/web_socket.js"></script>
    <script type="text/javascript" src="/js/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="http://sandbox.runjs.cn/uploads/rs/102/r2dy3tyw/reset.css">
    <link rel="stylesheet" type="text/css" href="fiveChess.css">
    <script type="text/javascript" src="http://sandbox.runjs.cn/uploads/rs/102/r2dy3tyw/CookieHandle.js"></script>
    <script type="text/javascript" src="http://sandbox.runjs.cn/uploads/rs/102/r2dy3tyw/jquery-1.7.2.js"></script>
    <script type="text/javascript" src="fiveChess.js"></script>
    <script type="text/javascript">
        if (typeof console == "undefined") {    this.console = { log: function (msg) {  } };}
        WEB_SOCKET_SWF_LOCATION = "/swf/WebSocketMain.swf";
        WEB_SOCKET_DEBUG = true;
        var ws, name, client_id,adverse_id,ready = 0,wait = 0,ready_adverse = 0,first = 0,gameStart = 0;

        // 连接服务端
        // 创建websocket
        ws = new WebSocket("ws://"+document.domain+":9292");
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
                        wait = 1;
                    }
                    else{
                        first = 1;
                    }
                    break;

                case 'ready_adverse':
                    //{"type":"login","client_id":xxx,"client_name":"xxx","client_list":"[...]","time":"xxx"
                    if(ready!=0&&gameStart==0){
                        fiveChess.init();
                        fiveChess.gameStart();
                    }
                    ready_adverse = 1;
                    break;

                case 'pace':
                    gameStart = 1;
                    wait = 0;
                    var lastPace = data['lastPace'];
                    var myPace = lastPace.toString();
                    var shuzuPace = myPace.split(",");
                    fiveChess.AImoveChess(parseInt(shuzuPace[0]) ,parseInt(shuzuPace[1]));

                    break;
                case 'win':

                    if(confirm("you have lost the game, do you want to play again?")){

                        fiveChess.resetChessBoard();

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

<div class="wrapper">
    <div class="chessboard">
        <!-- top line -->
        <div class="chess-top"></div>
        <div class="chess-top"></div>
        <div class="chess-top"></div>
        <div class="chess-top"></div>
        <div class="chess-top"></div>
        <div class="chess-top"></div>
        <div class="chess-top"></div>
        <div class="chess-top"></div>
        <div class="chess-top"></div>
        <div class="chess-top"></div>
        <div class="chess-top"></div>
        <div class="chess-top"></div>
        <div class="chess-top"></div>
        <div class="chess-top"></div>
        <div class="chess-top chess-right"></div>
        <!-- line 1 -->
        <div class="chess-left"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-right"></div>
        <!-- line 2 -->
        <div class="chess-left"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-right"></div>
        <!-- line 3 -->
        <div class="chess-left"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-right"></div>
        <!-- line 4 -->
        <div class="chess-left"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-right"></div>
        <!-- line 5 -->
        <div class="chess-left"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-right"></div>
        <!-- line 6 -->
        <div class="chess-left"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-right"></div>
        <!-- line 7 -->
        <div class="chess-left"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-right"></div>
        <!-- line 8 -->
        <div class="chess-left"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-right"></div>
        <!-- line 9 -->
        <div class="chess-left"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-right"></div>
        <!-- line 10 -->
        <div class="chess-left"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-right"></div>
        <!-- line 11 -->
        <div class="chess-left"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-right"></div>
        <!-- line 12 -->
        <div class="chess-left"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-right"></div>
        <!-- line 13 -->
        <div class="chess-left"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-middle"></div>
        <div class="chess-right"></div>
        <!-- bottom line  -->
        <div class="chess-bottom"></div>
        <div class="chess-bottom"></div>
        <div class="chess-bottom"></div>
        <div class="chess-bottom"></div>
        <div class="chess-bottom"></div>
        <div class="chess-bottom"></div>
        <div class="chess-bottom"></div>
        <div class="chess-bottom"></div>
        <div class="chess-bottom"></div>
        <div class="chess-bottom"></div>
        <div class="chess-bottom"></div>
        <div class="chess-bottom"></div>
        <div class="chess-bottom"></div>
        <div class="chess-bottom"></div>
        <div class="chess-bottom chess-right"></div>
    </div>

    <div class="operating-panel">
        <p>
            <button id="black_btn" class="btn selected"  hidden>black</button>
            <button id="white_btn" class="btn"  hidden>white</button>
        </p>
        <p>
            <button id="first_move_btn" class="btn selected" hidden>first</button>
            <button id="second_move_btn" class="btn"  hidden>notfirst</button>
        </p>
        <a id="replay_btn" class="btn"  href="#">begin</a>
        <p id="result_info"></p>
        <p id="result_tips"></p>
    </div>

    <div style="display: none">
        <img src="http://sandbox.runjs.cn/uploads/rs/102/r2dy3tyw/black.png" alt="preload" />
        <img src="http://sandbox.runjs.cn/uploads/rs/102/r2dy3tyw/white.png" alt="preload" />
        <img src="http://sandbox.runjs.cn/uploads/rs/102/r2dy3tyw/hover.png" alt="preload" />
        <img src="http://sandbox.runjs.cn/uploads/rs/102/r2dy3tyw/hover_up.png" alt="preload" />
        <img src="http://sandbox.runjs.cn/uploads/rs/102/r2dy3tyw/hover_down.png" alt="preload" />
        <img src="http://sandbox.runjs.cn/uploads/rs/102/r2dy3tyw/hover_up_left.png" alt="preload" />
        <img src="http://sandbox.runjs.cn/uploads/rs/102/r2dy3tyw/hover_up_right.png" alt="preload" />
        <img src="http://sandbox.runjs.cn/uploads/rs/102/r2dy3tyw/hover_left.png" alt="preload" />
        <img src="http://sandbox.runjs.cn/uploads/rs/102/r2dy3tyw/hover_right.png" alt="preload" />
        <img src="http://sandbox.runjs.cn/uploads/rs/102/r2dy3tyw/hover_down_left.png" alt="preload" />
        <img src="http://sandbox.runjs.cn/uploads/rs/102/r2dy3tyw/hover_down_right.png" alt="preload" />
        <img src="http://sandbox.runjs.cn/uploads/rs/102/r2dy3tyw/black_last.png" alt="preload" />
        <img src="http://sandbox.runjs.cn/uploads/rs/102/r2dy3tyw/white_last.png" alt="preload" />
    </div>
</div>
</body>
</html>
