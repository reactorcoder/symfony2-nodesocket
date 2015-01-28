<?php

namespace Reactorcoder\Symfony2NodesocketBundle\Twig;

use Symfony\Component\HttpFoundation\Response;

class TwigExtension extends \Twig_Extension
{      
    /**
     * @var ContainerInterface
     */
    protected $container;
    
    /**
     * @var Config
     */
    private $config;
    
    /**
     * Constructor
     * 
     * @param ContainerInterface $container
     */
    public function __construct($container)
    {
        $this->container = $container;

        $this->config = $this->container->getParameter("node_config");
    }
    
    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('codereactor_nodesocket_header_js', array($this, 'renderHeaderJavascript'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('codereactor_nodesocket_css', array($this, 'renderHeaderStylesheet'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('codereactor_nodesocket_body_js', array($this, 'renderBodyJs'), array('is_safe' => array('html'))),
        );
    }
    
    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'codereactor_nodesocket';
    }
    
    public function renderHeaderJavascript()
    {        
        $out = '<script src="/bundles/reactorcodersymfony2nodesocket/js/jquery.gritter.min.js" type="text/javascript"></script>';
        
        return $out;
    }
    
    public function renderHeaderStylesheet()
    {
        $out = '<link rel="stylesheet" href="/bundles/reactorcodersymfony2nodesocket/css/jquery.gritter.min.css" type="text/css">';
        
        return $out;
    }
    
    public function renderBodyJs()
    {
        $out = '<script src="/bundles/reactorcodersymfony2nodesocket/js/client.js" type="text/javascript"></script>';
        $out .= sprintf('<script type="text/javascript" src="http://%s:%s/socket.io/socket.io.js"></script>', $this->config['host'], (string)$this->config['port']);
        
        return $out;
    }
}