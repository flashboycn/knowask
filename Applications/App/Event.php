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
 * ���ڼ��ҵ�������ѭ�����߳�ʱ������������
 * �������ҵ���������Խ�����declare�򿪣�ȥ��//ע�ͣ�����ִ��php start.php reload
 * Ȼ��۲�һ��ʱ��workerman.log���Ƿ���process_timeout�쳣
 */
//declare(ticks=1);

/**
 * �������߼�
 * ��Ҫ�Ǵ��� onMessage onClose 
 */
use \GatewayWorker\Lib\Gateway;

class Event
{
   
   /**
    * ����Ϣʱ
    * @param int $client_id
    * @param mixed $message
    */
   public static function onMessage($client_id, $message)
   {
        // debug	
        echo "client:{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} gateway:{$_SERVER['GATEWAY_ADDR']}:{$_SERVER['GATEWAY_PORT']}  client_id:$client_id session:".json_encode($_SESSION)." onMessage:".$message."\n";
      
        // �ͻ��˴��ݵ���json����
        $message_data = json_decode($message, true);
        if(!$message_data)
        {
            return ;
        }
		$class 		= 	$message_data['mod'];
		$method 	=	$message_data['act'];
		$args		=	$message_data['args'];
		if(!($class&&$method&&$args))
		{
			return ;
		}
		if(file_exists(__DIR__.'/Controller/'.$class.'.class.php'))
		{
			require_once __DIR__.'/Controller/'.$class.'.class.php';
			$mod	=	new $class;
			echo call_user_func_array(array($mod,"$method"), array($args,$client_id));
		}else
		{
			return ;
		}

   }
   
   /**
    * ���ͻ��˶Ͽ�����ʱ
    * @param integer $client_id �ͻ���id
    */
   public static function onClose($client_id)
   {
       // debug
       echo "client:{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} gateway:{$_SERVER['GATEWAY_ADDR']}:{$_SERVER['GATEWAY_PORT']}  client_id:$client_id onClose:''\n";
       
       // �ӷ���Ŀͻ����б���ɾ��
       if(isset($_SESSION['room_id']))
       {
           $room_id = $_SESSION['room_id'];
           $new_message = array('type'=>'logout', 'from_client_id'=>$client_id, 'from_client_name'=>$_SESSION['client_name'], 'time'=>date('Y-m-d H:i:s'));
           Gateway::sendToGroup($room_id, json_encode($new_message));
       }
   }
  
}
