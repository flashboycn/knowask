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
use \Workerman\Worker;
use \GatewayWorker\Gateway;
use \Workerman\Autoloader;

// �Զ�������
require_once __DIR__ . '/../../Workerman/Autoloader.php';
Autoloader::setRootPath(__DIR__);

// gateway ����
$gateway = new Gateway("Websocket://0.0.0.0:7272");
// �������ƣ�����statusʱ�鿴
$gateway->name = 'ChatGateway';
// ���ý�������gateway������������cpu������ͬ
$gateway->count = 4;
// �ֲ�ʽ����ʱ�����ó�����ip����127.0.0.1��
$gateway->lanIp = '127.0.0.1';
// �ڲ�ͨѶ��ʼ�˿ڣ�����$gateway->count=4����ʼ�˿�Ϊ4000
// ��һ���ʹ��4000 4001 4002 4003 4���˿���Ϊ�ڲ�ͨѶ�˿� 
$gateway->startPort = 2300;
// �������
$gateway->pingInterval = 10;
// ��������
$gateway->pingData = '{"type":"ping"}';
// ����ע���ַ
$gateway->registerAddress = '127.0.0.1:1236';

/* 
// ���ͻ�����������ʱ���������ӵ�onWebSocketConnect������websocket����ʱ�Ļص�
$gateway->onConnect = function($connection)
{
    $connection->onWebSocketConnect = function($connection , $http_header)
    {
        // �����������ж�������Դ�Ƿ�Ϸ������Ϸ��͹ص�����
        // $_SERVER['HTTP_ORIGIN']��ʶ�����ĸ�վ���ҳ�淢���websocket����
        if($_SERVER['HTTP_ORIGIN'] != 'http://chat.workerman.net')
        {
            $connection->close();
        }
        // onWebSocketConnect ����$_GET $_SERVER�ǿ��õ�
        // var_dump($_GET, $_SERVER);
    };
}; 
*/

// ��������ڸ�Ŀ¼������������runAll����
if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}

