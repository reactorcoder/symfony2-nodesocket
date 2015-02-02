Installation
============

Introduction
------------

Bundle provides to emiting an events as live application and non-blocking 
javascript server and PHP emiters. Based on node.js, socket.io and elephant.io.
 Integrated packages with other vendor packages are included and binded to bundle.

Disclaimer
----------

A bundle is origin from yii-node-socket extension on [YiiNodeSocket](http://www.yiiframework.com/extension/yii-node-socket/) 
and is rewriten for Symfony bundle package. Some of components are from orignal 
 vendors such as socket.io, elephant.io, node.js and libraries from YiiNodeSocket.

Prerequisites
-------------

* PHP 5.2
* Symfony 2.6
* Nodejs package
* jQuery CDN

Install nodejs package on your system. Then find path of node and bind with node
 command. Find a node on a system:

```bash
$ which node
```

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require reactorcoder/symfony2-nodesocket "~1"
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Enable the Bundle
-------------------------

Activate a bundle by adding a follow bundle into AppKernel: `app/AppKernel.php`
file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Reactorcoder\Symfony2NodesocketBundle\ReactorcoderSymfony2NodesocketBundle(),
        );

        // ...
    }

    // ...
}
```

Step 3: Cache and logs files
----------------------------

Make sure that in app directory cache and logs files are writeable. A bundle 
requires logs directory to store node socket emit and action events.

Step 4: Add configuration
-------------------------

Add a configuration attributes inside config.yml as sample:

```php
reactorcoder_symfony2_nodesocket:
    host:   [yourhostname]                  # domain.ltd
    port:   [your port for node socket]     # 3001
    origin: [yourhostname]:*                # domain.ltd:*
    allowedServers: [127.0.0.1]             # separate with comma to add hosts
    dbOptions: null                         # 
    checkClientOrigin: null                 #
    sessionVarName: null                    #
    socketLogFile:  null                    # A log path file for process ID
    pidFile: null                           # runtime PID file
```

Make sure that your hostname is same in your project from [yourhostname].

If you add origin server it should be in a lists from hosts and listener hosts.

By default node socket uses session storage and cookie to share information
between server socket and client. A default name sessionVarName is PHPSESSID. If
 your application uses same session name change it to avoid conflicts.

When starting node socket it will store ID of system process into file. It uses
for starting and stopping node.js services.

A service uses log file to store node events, triggers, callbacks, messages and
 status of node service. By default node starts as background service inside 
server.js file.

Step 5: Command event service
-----------------------------

To staring a node server use console to activate service:

Get a console help intro:

```bash
$ app/console nodesocket
```

Starting a service:

```bash
$ app/console nodesocket start
```

By default if service has already running it will show process ID of running 
service. You can stop or restart a service.

Terminating a service:

```bash
$ app/console nodesocket stop
```
Restarting a service:

```bash
$ app/console nodesocket restart
```

Restarting service will kill previously process if started and starting a new
node service.

Check status and process ID of service:

```bash
$ app/console nodesocket getpid
```

If you encountering or if you prefer to manually start you can check your 
process id by using ps command to find node service:

```bash
$ ps -eaf | grep node
```

Then find a symfony node service `node server.js` and manually kill it using

```bash
$ kill -9 [PID]
```

Step 6: Put assets to your template resource
--------------------------------------------

For loading socket.io and emiting events you need to put a template code inside
 head tag. Remove a jquery CDN if you already have in your template:

```php
<head>
    <script type="text/javascript" src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
    {{ codereactor_nodesocket_css() }}
    {{ codereactor_nodesocket_header_js() }}
</head>
```

Before body tag put socket.io listener for incoming emits and status of socket:

```php
{{ codereactor_nodesocket_body_js() }}

    {{ codereactor_nodesocket_body_js() }}

    <script type="text/javascript">

        var socket = new NodeSocket();
        socket.debug(true);

        socket.onConnect(function () {
                console.log('Connection to socket successfully');
        });

        socket.onDisconnect(function () {
                console.log('On lost connection');
        });

        socket.on('message', function (data) {
                console.log('An event emit. Input data:');
                // Here you receive data from emits
                console.log(data);
        });

    </script>
```

A bundle requires in a web directory to include JS and CSS file you should check 
 and include your files into web/bundles/reactorcodersymfony2nodesocket directory.
 To publish bundle into web directory and loading automaticaly use:

```bash
    app/console assets:install web --symlink --relative
```

A public resource is located under Reactorcoder/Symfony2NodesocketBundle/Resources/public 
 directory.


Step 7: Base class in controllers
---------------------------------

In your controller (sending events):

Append a code after login function, load nodesocket class to register session and
  authenticate user into node socket. This should be done only once on login:

```php
use Reactorcoder\Symfony2NodesocketBundle\Library\php\NodeSocket as NodeSocket;

class DefaultController extends Controller
{
    $nodesocket = new NodeSocket;

    $event = $this->get('service_nodesocket')->getFrameFactory()->createAuthenticationFrame();
    $event->setUserId((int)1);  // Current UserID after login
    $event->send();

    return $this->render(...);  // This should be load assets from Step 6
}
```

For receiving events use template.

Step 8: Emit global event
-------------------------

To send event message via socket using event name use:

```php
    $event = $this->get('service_nodesocket')->getFrameFactory()->createEventFrame();
    $event->setEventName('message');
    $event['url'] = "uri";
    $event['time'] = date("d.m.Y H:i");
    $event['message'] = 'Hello';
    $event->send();
```

Step 9: Emit user event
-----------------------

To send event message via socket using user ID and if you previously set SetUserId()
 on login use:

```php
    $nodesocket = new NodeSocket;

    $event = $this->get('service_nodesocket')->getFrameFactory()->createEventFrame();
    $event->setUserId((int)2); // Send to another user
    $event->setEventName('message');
    $event['url'] = "uri";
    $event['time'] = date("d.m.Y H:i");
    $event['message'] = 'Hello';
    $event->send();
```

User will receive if is previously logged using createAuthenticationFrame() as user ID 2. 
 A code sample is for authentication on Step 6.

To receive message just call template from Step 6.

Example
-------

Full working sample demo is on Reactorcoder/Symfony2NodesocketBundle/Controller and
 Reactorcoder/Symfony2NodesocketBundle/Resources/views folder.


Contributing
------------

Thank you for contributing, suggestions, coding and maintenances package that 
 will helps others contributors, developers and end users. Feel free if you have
 suggestions, contributing or recommendations.
