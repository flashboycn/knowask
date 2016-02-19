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
use \GatewayWorker\BusinessWorker;
use \Workerman\Autoloader;

// �Զ�������
require_once __DIR__ . '/../../Workerman/Autoloader.php';
Autoloader::setRootPath(__DIR__);

// bussinessWorker ����
$worker = new BusinessWorker();
// worker����
$worker->name = 'ChatBusinessWorker';
// bussinessWorker��������
$worker->count = 4;
// ����ע���ַ
$worker->registerAddress = '127.0.0.1:1236';

// ��������ڸ�Ŀ¼������������runAll����
if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}

