<?php

namespace Reactorcoder\Symfony2NodesocketBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Reactorcoder\Symfony2NodesocketBundle\Library\php\NodeSocket as NodeSocket;
use Reactorcoder\Symfony2NodesocketBundle\DependencyInjection as DependecyInjection;

use Symfony\Component\DependencyInjection\Container;

class Loader
{
    protected $configuration, $nodesocket, $_userSid;
    
    public function __construct($container, $config)
    {
        if (!is_array($config))
        {
            trigger_error("No configuration in config.yml. Please check your configuration and add 'reactorcoder_symfony2_nodesocket' config declarations");
        }
        
        $this->configuration = $config;
        
        $request = $container->get('request');
        
        $this->nodesocket = new Nodesocket;
        
        $this->checkConfiguration($config, $request);
        
        return $this->nodesocket->init($this->configuration);
    }
    
    private function checkConfiguration($config, $request)
    {        
        if (is_null($config['host']))
        {
            $this->configuration['host'] = $request->getHost();
        }
        
        if (is_null($config['port']))
        {
            $this->configuration['port'] = (int)3001;
        }
        
        if (is_null($config['origin']))
        {
            $this->configuration['origin'] = $this->configuration['host'].':*';
        }
        else
        {
            $this->nodesocket->host = $this->configuration['host'];
            $this->nodesocket->origin = null;
            $this->configuration['origin'] = $this->nodesocket->getOrigin();
        }
        
        if (is_null($config['allowedServers']))
        {
            $this->configuration['allowedServers'] = '127.0.0.1:*';
        }
        else
        {
            $this->configuration['allowedServers'] = $this->nodesocket->getOrigin();
        }
        
        if (is_null($config['checkClientOrigin']))
        {
            $this->configuration['checkClientOrigin'] = true;
        }
        else
        {
            $this->configuration['checkClientOrigin'] = $this->nodesocket->getOrigin();
        }
        
        if (is_null($config['sessionVarName']))
        {
            $this->configuration['sessionVarName'] = (string)'PHPSESSID';
        }
        else
        {
            $this->configuration['sessionVarName'] = (string)$this->nodesocket->sessionVarName;
        }
        
        if (is_null($config['sessionVarName']))
        {
            $this->configuration['pidFile'] = 'socket-transport.pid';
        }
        else
        {
            $this->configuration['pidFile'] = $this->nodesocket->pidFile;
        }
        
        if (!is_array($config['allowedServers']))
        {
            $this->configuration['allowedServers'] = array('127.0.0.1');
        }
        else
        {
            $this->nodesocket->allowedServerAddresses = $config['allowedServers'];            
            $this->configuration['allowedServers'] = $this->nodesocket->getAllowedServersAddresses();
        }
        
        $this->_userSid = $request->cookies->get($this->configuration['sessionVarName']);
        $this->nodesocket->_userSid = $this->_userSid;
    }
    
    public function setRequest($request)
    {
        
    }
    
    public function getFrameFactory()
    {
        return $this->nodesocket->getFrameFactory();
    }
    
    public function setUser($sid)
    {
        $this->_userSid = (int)$sid;
    }
}