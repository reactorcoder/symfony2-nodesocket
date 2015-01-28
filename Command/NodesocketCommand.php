<?php

namespace Reactorcoder\Symfony2NodesocketBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use \Reactorcoder\Symfony2NodesocketBundle\Library\php\console as Phpnodeconsole;
use \Reactorcoder\Symfony2NodesocketBundle\Library\php\NodeSocket as NodeSocket;

class NodesocketCommand extends ContainerAwareCommand
{
    /**
     * @var string
     */
    public $componentName = 'nodeSocket';
    
    private $_console;
    
    private $runtime_writeable = false;
    
    private $runtime_path;
    
    public $pathToNodeJs = 'node';
    
    private $params;
    
    protected function getRuntimePath()
    {
        return getcwd().DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'runtime_node';
    }
    
    protected function getRuntimeLogPath()
    {
        return getcwd().DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'logs';
    }
    
    protected function configure()
    {        
        $this
            ->setName('nodesocket')
            ->addArgument('arguments', InputArgument::OPTIONAL)
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {        
        $this->params = $this->getContainer()->getParameter("node_config");
        
        $name = $input->getArgument('arguments');
        if ($name) {
            
            switch ($name)
            {
                case 'start':
                    $response = $this->actionStart();                    
                    $output->writeln($response);
                    break;
                
                case 'stop':
                    $response = $this->actionStop();
                    $output->writeln($response);
                    break;
                
                case 'restart':
                    $response = $this->actionRestart();
                    $output->writeln($response);
                    break;
                
                case 'getpid':
                    $response = $this->actionGetPid();
                    $output->writeln($response);
                    break;
                
                default:
                    $response = $this->render_usage();
                    $output->writeln($response);
            }
            
        } else {
            
            $response = $this->render_usage();
            $output->writeln($response);
            
        }
    }
    
    protected function render_usage()
    {
        
        $text = <<<EOD
             
    Log file: {$this->getLogFile()}
                
    USAGE
      app/console {$this->getName()} [action] [parameter]

    DESCRIPTION
      This command provides support for loading node socket extension

    EXAMPLES
     * app/console {$this->getName()} start
       Start socket server, check is started

     * app/console {$this->getName()} stop
       Stop socket server

     * app/console {$this->getName()} restart
       Restart socket server

     * app/console {$this->getName()} getpid
       Display socket pid
     
    MANUALLY CHECK
      You can check or kill process in address:
          ps -eaf | grep node
          kill -9 [PID]
                        
EOD;

     return $text;
     
        
    }
    
    /**
     * @return string
     */
    protected function makeCommand() {
        
           $server =  implode(DIRECTORY_SEPARATOR, array(
                   __DIR__,
                   '..',
                   'Library',
                   'js',
                   'server',
                   'server.js'
           ));
           
           return 'node' . ' ' . $server;
    }
    
    /**
     * @return string
     */
    protected function getLogFile() {
           $logFile = $this->getComponent()->socketLogFile;
           if ($logFile) {
                   return $logFile;
           }
           return $this->getRuntimeLogPath() . DIRECTORY_SEPARATOR . 'socket-transport.server.log';
    }
    
    protected function actionStart()
    {
        if ($this->getPid() || $this->isInProgress()) {
                printf("Server already started\n");
                printf("Server started on PID: %s \n", $this->getPid());
                return true;
                
        }
        
        $this->compileServer();
        $this->compileClient();
        printf("Starting server\n");
        
        printf($this->getRuntimeInfo());
        
        $runtime_command = $this->makeCommand();
        $runtime_log_file = $this->getLogFile();
        
        printf("Starting command:\n$runtime_command\n");
        printf("Log file:\n$runtime_log_file\n");
        
        $this->_pid = $this->getConsole()->startServer($this->makeCommand(), $this->getLogFile());
        printf("PID:".$this->_pid."\n");
        if ($this->_pid) {
                printf("Server successfully started\n");
                return $this->writePid();
        }
        return false;
    }
    
    protected function actionStop()
    {
        $pid = $this->getPid();
        
        if ($pid && $this->isInProgress()) {
                printf("Stopping socket server\n");
                $this->getConsole()->stopServer($this->getPid());
                printf("Killing a PID: %s\n", $this->getPid());
                if (!$this->isInProgress()) {
                        printf("Server successfully stopped on PID: %s \n", $pid);
                        $this->_pid = 0;
                        printf("Set PID to 0 \n");
                        return $this->writePid();
                }
                printf("Stopping server error\n");
                return false;
        }
        printf("Server is stopped\n");
        return true;
    }
    
    protected function actionRestart()
    {
        if ($this->actionStop()) {
                if (!$this->actionStart()) {
                        printf("Cannot start server");
                }
                exit(1);
        } else {
                printf('Cannot stop server');
        }
    }
    
    protected function actionGetPid()
    {
        return (int)$this->getPid() . "\n";
    }

    protected function compileServer() {
            
        printf("Compile server\n");
            
        $server_js_config = implode(DIRECTORY_SEPARATOR, array(
                __DIR__,
                '..',
                'Library',
                'js',
                'server',
                'server.config.js.php'
        ));

        if (file_exists($server_js_config))
        {
            echo('Configuration file found "server.config.js.php"').PHP_EOL;
        }

        ob_start();
        
        $nodeSocket = $this->getComponent();

        include($server_js_config);

        $js = ob_get_clean();

        return file_put_contents(__DIR__ . '/../Library/js/server/server.config.js', $js);
    }
    
    protected function compileClient() {
            
        printf("Compile client\n");
            
        $server_js_config = implode(DIRECTORY_SEPARATOR, array(
                __DIR__,
                '..',
                'Library',
                'js',
                'client',
                'client.template.js.php'
        ));

        if (file_exists($server_js_config))
        {
            echo('Configuration file found "client.template.js"').PHP_EOL;
        }
        
        ob_start();
        
        $nodeSocket = $this->getComponent();

        include($server_js_config);

        $js = ob_get_clean();

        return file_put_contents(__DIR__ . '/../Resources/public/js/client.js', $js);
    }
    
    /**
     * @return NodeSocket
     */
    protected function getComponent() {
          
        $thisComponentName = $this->componentName;
        $this->$thisComponentName = new NodeSocket;
        
        if (!is_null($this->params['host']))
        {
            $this->$thisComponentName->host = $this->params['host'];
        }
        
        if (!is_null($this->params['port']))
        {
            $this->$thisComponentName->port = $this->params['port'];
        }
        
        if (!is_null($this->params['origin']))
        {
            $this->$thisComponentName->origin = $this->params['origin'];
        }
        else
        {
            $this->$thisComponentName->origin = $this->getOrigin();
        }
        
        if (!is_null($this->params['checkClientOrigin']))
        {
            $this->$thisComponentName->checkClientOrigin = $this->params['checkClientOrigin'];
        }
        
        if (!is_null($this->params['sessionVarName']))
        {
            $this->$thisComponentName->sessionVarName = $this->params['sessionVarName'];
        }
        
        if (!is_null($this->params['socketLogFile']))
        {
            $this->$thisComponentName->socketLogFile = $this->params['socketLogFile'];
        }
        
        if (!is_null($this->params['pidFile']))
        {
            $this->$thisComponentName->pidFile = $this->params['pidFile'];
        }
        
        if (!is_null($this->params['allowedServers']))
        {
            if (count($this->params['allowedServers']))
            {
                $this->$thisComponentName->allowedServerAddresses = $this->params['allowedServers'];
            }
        }
        
        // If not found load default from class NodeSocket
        
        return $this->$thisComponentName;
    }
    
    /**
     * @return int
     */
    protected function writePid() {
           printf("Update pid in file %s\n", $this->getPidFile());
           return file_put_contents($this->getPidFile(), $this->getPid());
    }
    
    /**
     * @return int|null
     */
    protected function getPid($update = false) {
        
            if (isset($this->_pid) && !$update) {
                    return $this->_pid;
            }
            if ($update || !isset($this->_pid)) {
                    $this->updatePid();
            }
            return $this->_pid;
    }
    
    /**
     * Update process pid
     */
    protected function updatePid() {
            $this->_pid = 0;
            $pidFile = $this->getPidFile();
            if (file_exists($pidFile)) {
                    $this->_pid = (int)file_get_contents($pidFile);
            }
    }
    
    /**
     * @return string
     */
    protected function getPidFile() {
            $path = $this->getRuntimePath() . DIRECTORY_SEPARATOR . $this->getComponent()->pidFile;
            printf("Getting a PID file: %s \n", $path);
            return $path;
    }
    
    /**
     * @return bool
     */
    protected function isInProgress() {
            $pid = $this->getPid();
            if ($pid == 0) {
                    return false;
            }
            return $this->getConsole()->isInProgress($pid);
    }
    
    protected function getRuntimeInfo()
    {
        $return = "Checking runtime directory".PHP_EOL;
        
        $root_path = $this->getRuntimePath();
        
        if (is_dir($root_path))
        {
            if (is_writable(getcwd()))
            {
                $return .= "Directory runtime found and writeable: ".$root_path.PHP_EOL;
                
                $this->runtime_writeable = true;
                $this->runtime_path = $root_path;
            }
            else
            {
                $return .= "ERROR: Cannot write to file :".$root_path.PHP_EOL;
            }
        }
        else
        {
            $return .= "Creating directory: ".$root_path.PHP_EOL;
            
            if (mkdir($root_path))
            {
                $return .= "Creating directory: ".$root_path.PHP_EOL;
            }
            else
            {
                $return .= "ERROR: Directory cannnot create: ".$root_path.PHP_EOL;
                
                if (is_writable(getcwd()))
                {
                    $return .= "ERROR: Directory root path is writeble: ".$root_path.PHP_EOL;
                }
                else
                {
                    $return .= "ERROR: Directory cannnot create: ".$root_path.PHP_EOL;
                }
            }
        }
        
        return $return;
    }
    
    /**
     * @return \ConsoleInterface
     */
    private function getConsole() {
            if ($this->_console) {
                    return $this->_console;
            }
            if (strpos(PHP_OS, 'WIN') !== false) {
                    $this->_console = new Phpnodeconsole\WinConsole();
            } else {
                    $this->_console = new Phpnodeconsole\UnixConsole();
            }
            return $this->_console;
    }
}
