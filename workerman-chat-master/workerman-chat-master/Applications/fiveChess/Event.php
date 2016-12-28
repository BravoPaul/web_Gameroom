<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

/**
 * 聊天主逻辑
 * 主要是处理 onMessage onClose
 */
use \GatewayWorker\Lib\Gateway;
use \GatewayWorker\Lib\Db;

class Event
{

    /**
     * 有消息时
     * @param int $client_id
     * @param mixed $message
     */

    private static  $name;

    public static function onMessage($client_id, $message)
    {

        // debug
        echo "client:{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} gateway:{$_SERVER['GATEWAY_ADDR']}:{$_SERVER['GATEWAY_PORT']}  client_id:$client_id session:".json_encode($_SESSION)." onMessage:".$message."\n";

        $ready = [];


        // 客户端传递的是json数据
        $db1 = Db::instance('db1');

        $message_data = json_decode($message, true);
        if(!$message_data)
        {
            return ;
        }

        // 根据类型执行不同的业务
        switch($message_data['type'])
        {
            // 客户端回应服务端的心跳
            case 'pong':
                return;
            // 客户端登录 message格式: {type:login, name:xx, room_id:1} ，添加到客户端，广播给所有客户端xx进入聊天室
            case 'login':
                // 判断是否有房间号

                $client_name = htmlspecialchars($message_data['client_name']);

                $_SESSION['client_name'] = $client_name;

                // 获取房间内所有用户列表
                Gateway::joinGroup($client_id, 1);
                self::$name[$client_id] = $client_name;

                $new_message = array('type'=>$message_data['type'], 'client_id'=>$client_id, 'client_name'=>$client_name);
                Gateway::sendToCurrentClient(json_encode($new_message));
                // 客户端发言 message: {type:say, to_client_id:xx, content:xx}
                return;

            case 'ready':

                $clients_list = Gateway::getClientInfoByGroup(1);
                foreach($clients_list as $tmp_client_id=>$item)
                {
                    if($tmp_client_id!=$client_id){
                        $new_message = array('type'=>$message_data['type'],'adverse'=>$tmp_client_id,'client_id'=>$client_id);
                        Gateway::sendToCurrentClient(json_encode($new_message));
                        $new_message = array('type'=>"ready_adverse");
                        Gateway::sendToClient($tmp_client_id,json_encode($new_message));
                    }

                }

                return;

            case 'pace':

                $lastPace = $message_data['lastPace'];
                $to_id = $message_data['adverse'];
                $new_message = array('type'=>$message_data['type'], 'lastPace'=>$lastPace);
                Gateway::sendToClient($to_id,json_encode($new_message));
                // 客户端发言 message: {type:say, to_client_id:xx, content:xx}
                return;

            case 'win':

                $to_id = $message_data['adverse'];
                $new_message = array('type'=>$message_data['type']);
                Gateway::sendToClient($to_id,json_encode($new_message));
                // 客户端发言 message: {type:say, to_client_id:xx, content:xx}
                $client_note = $db1->select('note')->from("fiveChess")->where("username= '".self::$name[$client_id]."'")->single();
                $client_note = $client_note+20;

                $db1->update('fiveChess')->cols(array('note'=>''.$client_note.''))->where("username= '".self::$name[$client_id]."'")->query();
                return;







            // else{
                //    Gateway::sendToClient(self::$player1,json_encode($new_message));
              //  }


        }
    }

    /**
     * 当客户端断开连接时
     * @param integer $client_id 客户端id
     */
    public static function onClose($client_id)
    {
        // debug
        echo "client:{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} gateway:{$_SERVER['GATEWAY_ADDR']}:{$_SERVER['GATEWAY_PORT']}  client_id:$client_id onClose:''\n";

        // 从房间的客户端列表中删除
        if(isset($_SESSION['room_id']))
        {
            $room_id = $_SESSION['room_id'];
            $new_message = array('type'=>'logout', 'from_client_id'=>$client_id, 'from_client_name'=>$_SESSION['client_name'], 'time'=>date('Y-m-d H:i:s'));
            Gateway::sendToGroup($room_id, json_encode($new_message));
        }
    }



}

