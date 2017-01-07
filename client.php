<?php
	
if (isset($_SERVER['HTTP_ORIGIN'])) {
	header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
	header('Access-Control-Allow-Credentials: true');
	header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
		header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

	exit(0);
}

class Web_service
{
	public function get_data($service_url, $fields)
	{
		$fields_string = '';
		foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
		rtrim($fields_string, '&');
		
		// a. initialize
		try{
			$ch = curl_init();
			
			// b. set the options, including the url
			curl_setopt($ch, CURLOPT_URL, $service_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, count($fields));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
			
			// c. execute and fetch the resulting HTML output
			$output = curl_exec($ch);
			
			//in the case of an error save it to the database
			if ($output === FALSE) 
			{
				$response['result'] = 0;
				$response['message'] = curl_error($ch);
				
				$return = json_encode($response);
			}
			
			else
			{
				$return = $output;
			}
		}
		
		//in the case of an exceptions save them to the database
		catch(Exception $e)
		{
			$response['result'] = 0;
			$response['message'] = $e->getMessage();
			
			$return = json_encode($response);
		}
		
		return $return;
	}
}

$new = new Web_service;
// call the controller
$base_url = 'http://localhost/omnis_gateway/';
////////////////////////////////// Function Step 1: Login ////////////////////////////////////////\\

//if (isset($_POST["SendPayment"]))
//{
	$fields = array
	(
		'api_key' => urlencode("1000"),
		'phone_number' => urlencode("0720465220"),
		'amount' => urlencode("1000")
	);
	
	$service_url = $base_url.'payment-result';
    $response = $new->get_data($service_url, $fields);
    
    echo $response;
//}

if (isset($_POST["SendSMS"]))
{

	$fields = array(

		'message' => urlencode("Hi Robert"),
		'api_key' => urlencode("1000"),
		'phone_number' => urlencode("0720465220"),
		'sender_id' => urlencode("")
	);
	
	$service_url = $base_url.'sms/send_sms';
    $response = $new->get_data($service_url, $fields);
    
    echo $response;
}
////////////////////////////////// Function Step 1: Login ////////////////////////////////////////\\

if (isset($_POST["Login"]))
{
	$fields = array(
		'phone' => urlencode($_POST['phone']),
		'password' => urlencode($_POST['password'])
	);
	/*$fields = array(
		'phone' => urlencode('0727559609'),
		'password' => urlencode('123456')
	);*/
	$service_url = $base_url.'app_auth/login_member';
    $response = $new->get_data($service_url, $fields);
    
    echo $response;
}

////////////////////////////////// Function Step 2: Loan Applications ////////////////////////////////////////\\

if (isset($_POST["Application"]))
{
	$fields = array(
		'amount' => urlencode($_POST['amount']),
		'individual_id' => urlencode($_POST['individual_id']),
		'repayments' => urlencode($_POST['repayments'])
	);
	
	/*$fields = array(
		'amount' => urlencode(1000),
		'individual_id' => urlencode(15),
		'repayments' => urlencode(1)
	);*/
	$service_url = $base_url.'app_auth/request_short_loan';
    $response = $new->get_data($service_url, $fields);
    
    echo $response;
}

////////////////////////////////// Function Step 3: Disbursements ////////////////////////////////////////\\

if (isset($_POST["Disbursements"]))
{
	$fields = array(
		'individual_id' => urlencode($_POST['individual_id'])
	);
	
	/*$fields = array(
		'individual_id' => urlencode(30)
	);*/
	$service_url = $base_url.'app_auth/get_disbursements';
    $response = $new->get_data($service_url, $fields);
    
    echo $response;
}

////////////////////////////////// Function Step 4: Repayments ////////////////////////////////////////\\

if (isset($_POST["Repayments"]))
{
	$fields = array(
		'individual_id' => urlencode($_POST['individual_id'])
	);
	
	/*$fields = array(
		'individual_id' => urlencode(30)
	);*/
	$service_url = $base_url.'app_auth/get_repayments';
    $response = $new->get_data($service_url, $fields);
    
    echo $response;
}

////////////////////////////////// Function Step 5: Savings ////////////////////////////////////////\\

if (isset($_POST["Savings"]))
{
	$fields = array(
		'individual_id' => urlencode($_POST['individual_id'])
	);
	
	/*$fields = array(
		'individual_id' => urlencode(30)
	);*/
	$service_url = $base_url.'app_auth/get_savings';
    $response = $new->get_data($service_url, $fields);
    
    echo $response;
}