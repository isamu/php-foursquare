<?PHP

class Foursquare{
    private $base_url = "http://api.foursquare.com/v1/";
    private $auth = null;
    function __construct(&$auth=NULL){
        $this->auth = & $auth;
    }
    function fetch($url, $param, $method, $require_auth = true){
        if(get_class($this->auth) == "OAuth"){
            $this->auth->fetch($this->base_url . $url, $param, ($method=="GET") ? OAUTH_HTTP_METHOD_GET : OAUTH_HTTP_METHOD_POST);
            return $this->auth->getLastResponse();
        }else if(get_class($this->auth) == "HTTP_OAuth_Consumer"){
            return $this->auth->sendRequest($this->base_url . $url, $param, $method);
        }else if(!$require_auth){
            return "not impliment";
        }else{
            throw new Exception("This API needs authentication");
        }
    }
    function post($url, $param, $require_auth = true){
        return $this->fetch($url, $param, "POST", $require_auth);
    }
    function get($url, $param, $require_auth = true){
        return $this->fetch($url, $param, "GET", $require_auth);
    }
    function tips($params){
        return $this->get("tips", $params);
    }
    function venues($params){
        return $this->get("venues", $params, false);
    }
    function checkin($params){
        return $this->post("checkin", $params);
    }
}

?>

