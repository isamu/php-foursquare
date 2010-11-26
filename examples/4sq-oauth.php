<?PHP
require_once("config.php");
require_once("Services/Foursquare.php");
$oauth = new OAuth(ConsumerKey, ConsumerKeySecret,
                   OAUTH_SIG_METHOD_HMACSHA1,OAUTH_AUTH_TYPE_URI);

$request_token_info = $oauth->getRequestToken("http://foursquare.com/oauth/request_token");

printf("I think I got a valid request token, navigate your www client to:\n\n%s?oauth_token=%s\n\nOnce you finish authorizing, hit ENTER or INTERRUPT to exit\n\n", "http://foursquare.com/oauth/authorize", $request_token_info["oauth_token"]);

$in = fopen("php://stdin", "r");
fgets($in, 255);

printf("Grabbing an access token...\n");

$oauth->setToken($request_token_info["oauth_token"],$request_token_info["oauth_token_secret"]);
$access_token_info = $oauth->getAccessToken("http://foursquare.com/oauth/access_token");

printf("Access token: %s\n",$access_token_info["oauth_token"]);
printf("Access token secret: %s\n",$access_token_info["oauth_token_secret"]);

$oauth->setToken($access_token_info["oauth_token"],$access_token_info["oauth_token_secret"]);

$forsq = new Services_Foursquare($oauth);
$res = $forsq->venues(array("geolat" => 43.068982, 
                            "geolong" => 141.350613));

var_dump($res);


