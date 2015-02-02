<?php

namespace Reactorcoder\Symfony2NodesocketBundle\Library\php;

use Reactorcoder\Symfony2NodesocketBundle\Library\php\frames\FrameFactory as FrameFactory;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

require_once 'frames/IFrameFactory.php';

class NodeSocket
{
    /**
     * Node js server host to bind http and socket server
     * Valid values is:
     *   - valid ip address
     *   - domain name
     *
     * Domain name must be withoud http or https
     * Example:
     *
     * 'host' => 'test.com'
     * // or
     * 'host' => '84.25.159.52'
     *
     * @var string
     */
    public $host = '0.0.0.0';
    
    /**
     * If your session var name is SID or other change this value to it
     *
     * @var string
     */
    public $sessionVarName = 'PHPSESSID';

    /**
     * @var int by default is once month
     */
    public $cookieLifeTime = 2592000;

    /**
     * Port in integer type only
     *
     * @var int
     */
    public $port = 3001;

    /**
     * Can be string, every domain|ip separated by a comma
     * or array
     *
     * @var string|array
     */
    public $origin;

    /**
     * List of allowed servers
     *
     * Who can send server frames
     *
     * If is string, ip addresses should be separated by a comma
     *
     * @var string|array
     */
    public $allowedServerAddresses;

    /**
     * Default is runtime/socket-transport.server.log
     *
     * @var string
     */
    public $socketLogFile;

    /**
     * If set to false, any client can connect to websocket server
     *
     * @var bool
     */
    public $checkClientOrigin = true;

    /**
     * @var string
     */
    public $pidFile = 'socket-transport.pid';

    /**
     * @var int timeout for handshaking in miliseconds
     */
    public $handshakeTimeout = 2000;

    /**
     * @var array
     */
    public $dbConfiguration = array('driver' => 'dummy');
    
    /**
     * @var string
     */
    protected $_assetUrl;

    /**
     * @var \NodeSocket\Frames\FrameFactory
     */
    protected $_frameFactory;

    /**
     * @var \NodeSocket\Components\Db
     */
    protected $_db;

    /**
     * @var \ElephantIO\Client
     */
    protected $_client;
    
   /**
    * 
    * User SID
    * 
    * @return string
    */
    public $_userSid;
    
    
    public function init($configuration)
    {        
        foreach ($configuration as $config_key => $config_val)
        {
            $this->$config_key = $config_val;
        }
        
        require_once   __DIR__ . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, array(
			'..',
			'vendor',
			'elephant.io',
			'lib',
			'ElephantIO',
			'Client.php'
		));
        
        $this->_frameFactory = new FrameFactory($this);
        
        return $this;
    }
    
    /**
     * @return \NodeSocket\Frames\FrameFactory
     */
    public function getFrameFactory() {
        return $this->_frameFactory;
    }

    /**
     * @return \NodeSocket\Components\Db
     */
    public function getClient() {
        return $this->_client;
    }
    
    /**
     * @return string
     */
    public function getOrigin() {
        // $origin = $this->host . ':*';
        
        $origin = '';
        if ($this->origin) {
            $o = array();
            if (is_string($this->origin)) {
                $o = explode(',', $this->origin);
            }
            $o = array_map('trim', $o);
            if (in_array($origin, $o)) {
                unset($o[array_search($origin, $o)]);
            }
            if (!empty($o)) {
                $origin .= ' ' . implode(' ', $o);
            }
        }
        if (!$origin) {
            $origin = $this->host . ':*';
        }
        
        return $origin;
    }
    
    /**
     * @return array
     */
    public function getAllowedServersAddresses() {
        $allow = array();
        $serverIp = \gethostbyname($this->host);
        $allow[] = $serverIp;
        if ($this->allowedServerAddresses && !empty($this->allowedServerAddresses)) {
            if (is_string($this->allowedServerAddresses)) {
                $allow = array_merge($allow, explode(',', $this->allowedServerAddresses));
            } else if (is_array($this->allowedServerAddresses)) {
                $allow = array_merge($allow, $this->allowedServerAddresses);
            }
        }
        return array_unique($allow);
    }
}