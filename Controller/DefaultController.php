<?php

namespace Reactorcoder\Symfony2NodesocketBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Reactorcoder\Symfony2NodesocketBundle\Library\php\NodeSocket as NodeSocket;

class DefaultController extends Controller
{
    protected $_nodesocket;
    
    public function __construct()
    {
        $this->_nodesocket = new NodeSocket;
    }
    
    public function loginAction()
    {
        $cud = (int)1;
        
        $request = $this->get('request');
        $cookies = $request->cookies;
        
        $this->_nodesocket->_userSid = $cookies->get('PHPSESSID');
        
        $event = $this->_nodesocket->init()->getFrameFactory()->createAuthenticationFrame();
        $event->setUserId($cud);
        $event->send();
        
        return $this->render('ReactorcoderSymfony2NodesocketBundle:Default:eventlistener.html.twig');
    }
    
    public function indexAction()
    {
        
        $request = $this->get('request');
        $cookies = $request->cookies;
        
        $this->_nodesocket->_userSid = $cookies->get('PHPSESSID');
        
        $send_user = true;
        
        if ($send_user)
        {
            $event = $this->_nodesocket->init()->getFrameFactory()->createUserEventFrame();
            $event->setUserId(1); // Send to another user
            $event->setEventName('message');
            $event['url'] = "uri";
            $event['time'] = date("d.m.Y H:i");
            $event['message'] = 'Hello';
        }
        else
        {
            $event = $this->_nodesocket->init()->getFrameFactory()->createEventFrame();
            $event->setEventName('message');
            $event['url'] = "uri";
            $event['time'] = date("d.m.Y H:i");
            $event['message'] = 'Hello';        
        }

        $event->send();
        
        return $this->render('ReactorcoderSymfony2NodesocketBundle:Default:index.html.twig');
    }
    
    public function eventlistenerAction()
    {
        $request = $this->get('request');
        $cookies = $request->cookies;
        
        $this->_nodesocket->_userSid = $cookies->get('PHPSESSID');
        
        return $this->render('ReactorcoderSymfony2NodesocketBundle:Default:eventlistener.html.twig');
    }
}