<?php
require_once './vendor/autoload.php';
use WindowsAzure\Common\ServicesBuilder;
use WindowsAzure\Common\ServiceException;
use WindowsAzure\Table\Models\Entity;

$API_STAGE = 'https://sandbox.api.mastercard.com/fraud/merchant/v1/termination-inquiry';
$API_PROD = 'https://api.mastercard.com/fraud/merchant/v1/termination-inquiry';
/*
$app = new \Slim\Slim([
	'table_name' => 'wepaymcmatch',
	'conn_string' => getenv('CUSTOMCONNSTR_MCMATCH_CONN_STRING')
]);

$app->view(new \JsonApiView());
$app->add(new \JsonApiMiddleware());

// Custom methods
$app->timer = function() {
	list($usec, $sec) = explode(" ", microtime());
	return ((float)$usec + (float)$sec);
};
$app->container->singleton('tableClient', function () use ($app) {
	return ServicesBuilder::getInstance()->createTableService($app->config('conn_string'));
});

// Name route
$app->get('/name/:name', function ($name) use ($app) {
	
	
	
	$app->redirect('/metaphone/' . metaphone($name));
});

// Metaphone route
$app->get('/metaphone/:name', function ($name) use ($app) {
	// Metaphone only has the following characters, according to wiki
	$name = preg_replace("/[^0BFHJKLMNPRSTWXYAEIOU]/", '', $name);
	$filter = "PartitionKey eq '" . $name . "' ";
	$filter.= "and (RowKey eq 'org:" . $name . "'";
	$filter.= "or RowKey eq 'name:" . $name . "')";


});
*/

class MasterCardMatch {
	private $slimConfig = [
		'table_name' => 'wepaymcmatch',
		'conn_string' => getenv('CUSTOMCONNSTR_MCMATCH_CONN_STRING')
	];
	
	public function __construct() {
		$this->app = new \Slim\Slim(self::$selfConfig);
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