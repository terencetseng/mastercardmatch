<?php
require_once './vendor/autoload.php';
use WindowsAzure\Common\ServicesBuilder;
use WindowsAzure\Table\Models\Entity;

$tableName = 'wepaymcmatch';
$connString ='CUSTOMCONNSTR_MCMATCH_CONN_STRING';

$this->app = new \Slim\Slim(['table_name' => self::$tableName, 'conn_string' => getenv(self::$connString)]);
$this->app->view(new \JsonApiView());
$this->app->add(new \JsonApiMiddleware());
$this->app->get('name/:name', function ($name) {
	echo $name;
});
$this->app->run();


