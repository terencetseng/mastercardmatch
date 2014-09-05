<?php
require_once './vendor/autoload.php';
use WindowsAzure\Common\ServicesBuilder;
use WindowsAzure\Table\Models\Entity;

class MasterCardMatch {
	private $tableName = 'wepaymcmatch';
	private $connString ='CUSTOMCONNSTR_MCMATCH_CONN_STRING';
	
	public function __construct() {
		$this->app = new \Slim\Slim(['table_name' => self::$tableName, 'conn_string' => getenv(self::$connString)]);
		$this->app->view(new \JsonApiView());
		$this->app->add(new \JsonApiMiddleware());
		$this->get('name/:name', [$this, 'name']);
		$this->app->run();
	}
	
	public function name($name) {
		echo $name;
	}
}

$mc = new MasterCardMatch();