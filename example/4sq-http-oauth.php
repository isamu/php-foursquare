<?PHP
require_once("config.php");
require_once("../foursquare.php");
require_once('HTTP/OAuth/Consumer.php');

$oauth = new HTTP_OAuth_Consumer(ConsumerKey, ConsumerKeySecret);
$http_request = new HTTP_Request2();  
$http_request->setConfig('ssl_verify_peer', false);  
$consumer_request = new HTTP_OAuth_Consumer_Request;  
$consumer_request->accept($http_request);  
$consumer_request->setAuthType(HTTP_OAuth_Consumer_Request::AUTH_HEADER);
$oauth->accept($consumer_request); 
$oauth->getRequestToken("http://foursquare.com/oauth/request_token");

$request_token = $oauth->getToken();
$request_token_secret =  $oauth->getTokenSecret();
echo "http://foursquare.com/oauth/authorize?oauth_token=". $oauth->getToken()."\n\n";

$in = fopen("php://stdin", "r");
$str = fgets($in, 255);

printf("Grabbing an access token...\n");

$oauth->setToken($request_token);
$oauth->setTokenSecret($request_token_secret);
$oauth->getAccessToken("http://foursquare.com/oauth/access_token", preg_replace("/\n|\r/", "", $str));

$oauth->setToken($oauth->getToken());
$oauth->setTokenSecret($oauth->getTokenSecret());
$forsq = new Foursquare($oauth);

$res = $forsq->findfriends_byname(array("q" => "isamu"));
var_dump($res->getBody());

$res = $forsq->venues(array("geolat" => 35.652101, 
                            "geolong" => 139.333334));

var_dump($res->getBody());

$res = $forsq->checkin(array("vid" =>1181151));
var_dump($res->getBody());
exit;
