/**
 * ChinaChess - in html5
 * http://www.jnzo.com/chess/
 * @ author 一叶孤舟
 * @ mail itlwei@163.com
 * @ QQ 28701884
 */

var play = play||{};

play.init = function (){

	play.my				=	1;				//玩家方
	play.map 			=	com.arr2Clone (com.initMap);		//初始化棋盘
	play.nowManKey		=	false;			//现在要操作的棋子
	play.pace 			=	[];				//记录每一步
	play.isPlay 		=	true ;			//是否能走棋
	play.mans 			=	com.mans;
	play.bylaw 			= 	com.bylaw;
	play.show 			= 	com.show;
	play.showPane 		= 	com.showPane;
	play.isOffensive	=	true;			//是否先手
	play.depth			=	play.depth || 3;				//搜索深度

	play.isFoul			=	false;	//是否犯规长将



	com.pane.isShow		=	 false;			//隐藏方块

	//初始化棋子
	for (var i=0; i<play.map.length; i++){
		for (var n=0; n<play.map[i].length; n++){
			var key = play.map[i][n];
			if (key){
				com.mans[key].x=n;
				com.mans[key].y=i;
				com.mans[key].isShow = true;
			}
		}
	}
	play.show();

	//绑定点击事件
	com.canvas.addEventListener("click",play.clickCanvas)


}




//点击棋盘事件
play.clickCanvas = function (e){
	if (wait==0){

		if (!play.isPlay) return false;
		var key = play.getClickMan(e);
		var point = play.getClickPoint(e);

		var x = point.x;
		var y = point.y;

		if (key){
			play.clickMan(key,x,y);
		}else {
			play.clickPoint(x,y);
		}
		play.isFoul = play.checkFoul();//检测是不是长将

	}

}

//点击棋子，两种情况，选中或者吃子
play.clickMan = function (key,x,y){
	gameStart = 1;
	var man = com.mans[key];
	//吃子
	if (play.nowManKey&&play.nowManKey != key && man.my != com.mans[play.nowManKey ].my){
		//man为被吃掉的棋子
		if (play.indexOfPs(com.mans[play.nowManKey].ps,[x,y])){
			man.isShow = false;
			var pace=com.mans[play.nowManKey].x+""+com.mans[play.nowManKey].y
			//z(bill.createMove(play.map,man.x,man.y,x,y))
			delete play.map[com.mans[play.nowManKey].y][com.mans[play.nowManKey].x];
			play.map[y][x] = play.nowManKey;
			com.showPane(com.mans[play.nowManKey].x ,com.mans[play.nowManKey].y,x,y)
			com.mans[play.nowManKey].x = x;
			com.mans[play.nowManKey].y = y;
			com.mans[play.nowManKey].alpha = 1

			play.pace.push(pace+x+y);
			var pace_data = '{"type":"pace","adverse":"'+adverse_id+'","lastPace":"'+play.pace[play.pace.length-1]+'"}';

			ws.send(pace_data);
			wait = 1;
			play.nowManKey = false;
			com.pane.isShow = false;
			com.dot.dots = [];
			com.show()
			com.get("clickAudio").play();
			setTimeout("play.AIPlay()",120000);
			if (key == "j0") play.showWin (-1);
			if (key == "J0") play.showWin (1);
		}
		// 选中棋子
	}else{
		if (man.my===1){
			if (com.mans[play.nowManKey]) com.mans[play.nowManKey].alpha = 1 ;
			man.alpha = 0.6;
			com.pane.isShow = false;
			play.nowManKey = key;
			com.mans[key].ps = com.mans[key].bl(); //获得所有能着点
			com.dot.dots = com.mans[key].ps;
			com.show();
			//com.get("selectAudio").start(0);
		}
	}
}

//点击着点
play.clickPoint = function (x,y){
	var key=play.nowManKey;
	var man=com.mans[key];
	if (play.nowManKey){
		if (play.indexOfPs(com.mans[key].ps,[x,y])){
			var pace=man.x+""+man.y
			//z(bill.createMove(play.map,man.x,man.y,x,y))
			delete play.map[man.y][man.x];
			play.map[y][x] = key;
			com.showPane(man.x ,man.y,x,y)
			man.x = x;
			man.y = y;
			man.alpha = 1;
			play.pace.push(pace+x+y);
			var pace_data = '{"type":"pace","adverse":"'+adverse_id+'","lastPace":"'+play.pace[play.pace.length-1]+'"}';
			ws.send(pace_data);
			wait = 1;
			play.nowManKey = false;
			com.dot.dots = [];
			com.show();
			com.get("clickAudio").play();
			//setTimeout("play.AIPlay()",120000);
		}else{
			//alert("不能这么走哦！")
		}
	}

}

//Ai自动走棋
play.AIPlay = function (lastpace){
	//return
	play.my = -1 ;
	var pace=lastpace;
	if (!pace) {
		play.showWin (1);
		return ;
	}
	play.pace.push(pace.join(""));
	var key=play.map[pace[1]][pace[0]];
	play.nowManKey = key;

	var key=play.map[pace[3]][pace[2]];
	if (key){
		play.AIclickMan(key,pace[2],pace[3]);
	}else {
		play.AIclickPoint(pace[2],pace[3]);
	}
	com.get("clickAudio").play();


}

//检查是否长将
play.checkFoul = function(){
	var p=play.pace;
	var len=parseInt(p.length,10);
	if (len>11&&p[len-1] == p[len-5] &&p[len-5] == p[len-9]){
		return p[len-4].split("");
	}
	return false;
}



play.AIclickMan = function (key,x,y){
	var man = com.mans[key];
	//吃子
	man.isShow = false;
	delete play.map[com.mans[play.nowManKey].y][com.mans[play.nowManKey].x];
	play.map[y][x] = play.nowManKey;
	play.showPane(com.mans[play.nowManKey].x ,com.mans[play.nowManKey].y,x,y)

	com.mans[play.nowManKey].x = x;
	com.mans[play.nowManKey].y = y;
	play.nowManKey = false;

	com.show()
	if (key == "j0") play.showWin (-1);
	if (key == "J0") play.showWin (1);
}

play.AIclickPoint = function (x,y){
	var key=play.nowManKey;
	var man=com.mans[key];
	if (play.nowManKey){
		delete play.map[com.mans[play.nowManKey].y][com.mans[play.nowManKey].x];
		play.map[y][x] = key;

		com.showPane(man.x,man.y,x,y);


		man.x = x;
		man.y = y;
		play.nowManKey = false;

	}
	com.show();
}


play.indexOfPs = function (ps,xy){
	for (var i=0; i<ps.length; i++){
		if (ps[i][0]==xy[0]&&ps[i][1]==xy[1]) return true;
	}
	return false;

}

//获得点击的着点
play.getClickPoint = function (e){
	var domXY = com.getDomXY(com.canvas);
	var x=Math.round((e.pageX-domXY.x-com.pointStartX-20)/com.spaceX)
	var y=Math.round((e.pageY-domXY.y-com.pointStartY-20)/com.spaceY)
	return {"x":x,"y":y}
}

//获得棋子
play.getClickMan = function (e){
	var clickXY=play.getClickPoint(e);
	var x=clickXY.x;
	var y=clickXY.y;
	if (x < 0 || x>8 || y < 0 || y > 9) return false;
	return (play.map[y][x] && play.map[y][x]!="0") ? play.map[y][x] : false;
}

play.showWin = function (my){
	play.isPlay = false;
	if (my===1){
		alert("you won the game");
	}else{
		var win_data = '{"type":"win","adverse":"'+adverse_id+'"}';
		ws.send(win_data);
		if(confirm("you have lost the game, do you want to play again?")){
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
	}
}

play.AIInit = function(pace){

	if (bill.length){
		return [man.x,man.y,val.x,val.y]
	}else {
		return false;
	}
}