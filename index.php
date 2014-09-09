<?php
require_once './vendor/autoload.php';
use WindowsAzure\Common\ServicesBuilder;
use WindowsAzure\Table\Models\Entity;

$app = new \Slim\Slim([
	'table_name' => 'wepaymcmatch',
	'conn_string' => getenv('CUSTOMCONNSTR_MCMATCH_CONN_STRING')
]);

$app->get('/name', function() use ($app) {
	$URL_SANDBOX = 'https://sandbox.api.mastercard.com/fraud/merchant/v1/termination-inquiry';
	$URL_PROD =  'https://api.mastercard.com/fraud/merchant/v1/termination-inquiry';
	
	$environment = $app->request()->params('environment');
	$debug = $app->request()->params("debug");
	
	$url = $URL_SANDBOX;
	if($environment == 'prod') {
		$url = $URL_PROD;
	}
	

	$request = $app->request()->params('request');
	
	if(!$debug) {
		$obj = json_decode(urldecode($request));
		print_r($obj);
		return;
	}
	
	echo $request;
	return;
	
	$xml_request = sprintf('
		<ns2:TerminationInquiryRequest xmlns:ns2="http://mastercard.com/termination">
	        <AcquirerId>%s</AcquirerId>
			<Merchant>
				<Name>%s</Name>
				<Address>
					<Line1>%s</Line1>
					<City>%s</City>
					<CountrySubdivision>%s</CountrySubdivision>
					<PostalCode>%s</PostalCode>
					<Country>%s</Country>
				</Address>
				<Principal>
					<FirstName>%s</FirstName>
					<LastName>%s</LastName>
					<Address>
						<CountrySubdivision>%s</CountrySubdivision>
						<PostalCode>%s</PostalCode>
						<Country>%s</Country>
					</Address>
				</Principal>
			</Merchant>
		</ns2:TerminationInquiryRequest>
        ',
		$obj['acquirer_id'],
		$obj['entity_name'],
		$obj['entity_address']['line1'],
		$obj['entity_address']['city'],
		$obj['entity_address']['countrysubdivision'],
		$obj['entity_address']['postalcode'],
		$obj['entity_address']['country'],
		$obj['principal']['first_name'],
		$obj['principal']['last_name'],
		$obj['principal']['address'],
		$obj['principal']['address']['countrysubdivision'],
		$obj['principal']['address']['postalcode'],
		$obj['principal']['address']['country']
	);
	
	$xml = new SimpleXMLElement($xml_request);
	
	$headers = [
			"Content-Type: application/xml",
			"Content-Length: " . strlen($xml)
	];
	
	$url_params = "?Format=XML&PageOffset=0&PageLength=10";
	
	try {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL, $url . $url_params);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, urlencode($xml));
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP + CURLPROTO_HTTPS);
		curl_setopt($ch, CURLOPT_REDIR_PROTOCOLS, CURLPROTO_HTTP + CURLPROTO_HTTPS);
		curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		$result = curl_exec($ch);
	} catch(ServiceException $e) {
		$app->render(500, [
				'code' => $e->getCode(),
				'msg' => $e->getMessage()
				]);
		return;
	}

	curl_close($ch);
	
	echo $result;
	
	return $result;
});

$app->run();
