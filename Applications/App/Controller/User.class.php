<?php
use \Applications\App\Controller\MyWm;
use \GatewayWorker\Lib\Db;
use \GatewayWorker\Lib\Gateway;
//require_once __DIR__.'/../Function/functions.php';  
class User extends MyWm{
	public function reg()
	{
		$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
		var_dump($managersx);
	}
	public function login($args,$client_id)
	{
		$message_data=$args;
		if(!isset($message_data['room_id']))
		{
			throw new \Exception("\$message_data['room_id'] not set. client_ip:{$_SERVER['REMOTE_ADDR']} \$message:$message");
		}
		
		// 把房间号昵称放到session中
		$room_id = $message_data['room_id'];
		$client_name = htmlspecialchars($message_data['client_name']);
		$_SESSION['room_id'] = $room_id;
		$_SESSION['client_name'] = $client_name;
	  
		// 获取房间内所有用户列表 
		$clients_list = Gateway::getClientInfoByGroup($room_id);
		foreach($clients_list as $tmp_client_id=>$item)
		{
			$clients_list[$tmp_client_id] = $item['client_name'];
		}
		$clients_list[$client_id] = $client_name;
		
		// 转播给当前房间的所有客户端，xx进入聊天室 message {type:login, client_id:xx, name:xx} 
		$new_message = array('type'=>'login', 'client_id'=>$client_id, 'client_name'=>htmlspecialchars($client_name), 'time'=>date('Y-m-d H:i:s'));
		Gateway::sendToGroup($room_id, json_encode($new_message));
		Gateway::joinGroup($client_id, $room_id);
	   
		// 给当前用户发送用户列表 
		$new_message['client_list'] = $clients_list;
		Gateway::sendToCurrentClient(json_encode($new_message));
		return;
		
	}
	public function pong()
	{
		return;
	}
	public function chat($args,$client_id)
	{
		$message_data=$args;
		// 非法请求
		if(!isset($_SESSION['room_id']))
		{
			throw new \Exception("\$_SESSION['room_id'] not set. client_ip:{$_SERVER['REMOTE_ADDR']}");
		}
		$room_id = $_SESSION['room_id'];
		$client_name = $_SESSION['client_name'];
		
		// 私聊
		if($message_data['to_client_id'] != 'all')
		{
			$new_message = array(
				'type'=>'say',
				'from_client_id'=>$client_id, 
				'from_client_name' =>$client_name,
				'to_client_id'=>$message_data['to_client_id'],
				'content'=>"<b>对你说: </b>".nl2br(htmlspecialchars($message_data['content'])),
				'time'=>date('Y-m-d H:i:s'),
				'class'=>'it',
			);
			Gateway::sendToClient($message_data['to_client_id'], json_encode($new_message));
			$new_message['content'] = "<b>你对".htmlspecialchars($message_data['to_client_name'])."说: </b>".nl2br(htmlspecialchars($message_data['content']));
			$new_message['class']='me';
			return Gateway::sendToCurrentClient(json_encode($new_message));
		}
		
		$new_message = array(
			'type'=>'say', 
			'from_client_id'=>$client_id,
			'from_client_name' =>$client_name,
			'to_client_id'=>'all',
			'content'=>nl2br(htmlspecialchars($message_data['content'])),
			'time'=>date('Y-m-d H:i:s'),
			'class'=>'it',
		);
		Gateway::leaveGroup($client_id, $room_id);
		Gateway::sendToGroup($room_id ,json_encode($new_message));
		Gateway::joinGroup($client_id, $room_id);
		$new_message['class']='me';
		return Gateway::sendToCurrentClient(json_encode($new_message));
	}
}
