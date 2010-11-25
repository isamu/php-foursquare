<?PHP
/**
 * php-foursquare
 *
 * Implementation of foursquare API
 *
 * @category   HTTP
 * @package    php-foursquare
 * @author     Isamu Arimoto <@gmail.com>
 * @copyright  2010 Isamu Arimoto <@gmail.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       https://github.com/isamu/php-foursquare/
 *
 */

class Foursquare
{
    
    private $auth;
    /**
     * Base url
     *
     * @var string $base_url of foursquare API
     */
    private $base_url = 'http://api.foursquare.com/v1/';
    private $method = array('checkins' => array('get', true),
                            'checkin' => array('post', true),
                            'history' => array('get', true),
                            'user' => array('get', true),
                            'friends' => array('get', true),
                            'venues' => array('get', false),
                            'venue' => array('get', false),
                            'categories' => array('get', false),
                            'addvenue' => array('post', true),
                            'venue/proposeedit' => array('post', true),
                            'venue/flagclosed' => array('post', true),
                            'tips' => array('get', false),
                            'addtip' => array('post', true),
                            'tip/marktodo' => array('post', true),
                            'tip/markdone' => array('post', true),
                            'tip/unmark' => array('post', true),
                            'friend/requests' => array('get', true),
                            'friend/approve' => array('post', true),
                            'friend/deny' => array('post', true),
                            'findfriends/byname' => array('get', true),
                            'findfriends/byphone' => array('get', true),
                            'findfriends/bytwitter' => array('get', true),
                            'settings/setpings'  => array('post', true),
                            'test' => array('get', false));

    public function __construct(&$auth=NULL)
    {
        $this->auth = & $auth;
    }

    private function fetch($url, $param, $method, $require_auth = true)
    {
        if (get_class($this->auth) == 'OAuth'){
            if($this->auth->fetch($this->base_url . $url, $param, ($method=='GET') ? OAUTH_HTTP_METHOD_GET : OAUTH_HTTP_METHOD_POST)){
                return $this->auth->getLastResponse();
            }
        } elseif (get_class($this->auth) == 'HTTP_OAuth_Consumer'){
            return $this->fetch_http_oauth($url, $param, $method);
        } elseif (!$require_auth){
            return 'not impliment';
        } else {
            throw new Exception('This API needs authentication');
        }
    }

    private function fetch_http_oauth($url, $param, $method)
    {
        $res = $this->auth->sendRequest($this->base_url . $url, $param, $method);
        switch ($res->response->getStatus()) {
        case 200:
            return $res->getBody();
        case 501:
            throw new Exception('Invalid Attribute Value');
        case 400:
            throw new Exception('Quota exceeded');
        case 401:
            throw new Exception('Authentication failed');
        default:
            return $res->getBody();
        }
    }

    private function post($url, $param, $require_auth = true)
    {
        return $this->fetch($url, $param, 'POST', $require_auth);
    }

    private function get($url, $param, $require_auth = true)
    {
        return $this->fetch($url, $param, 'GET', $require_auth);
    }

    public function __call($methodName, $args)
    {
        $methodName = preg_replace('/\_/', '/', $methodName);
        if ($this->method[$methodName]){
            return $this->{$this->method[$methodName][0]}($methodName, (array) $args[0], $this->method[$methodName][1]);
        } else {
            throw new Exception('No method exists');
        }
    }
}

?>

