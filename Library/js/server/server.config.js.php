<?php
/**
 * @var NodeSocket $nodeSocket
 * @var NodeSocketCommand $this
 */
?>
module.exports = {
    host : '<?php echo $nodeSocket->host; ?>',
    port : parseInt('<?php echo $nodeSocket->port; ?>'),
    origin : '<?php echo $nodeSocket->getOrigin(); ?>',
    allowedServers : <?php echo json_encode($nodeSocket->getAllowedServersAddresses()); ?>,
    checkClientOrigin : <?php echo (int) $nodeSocket->checkClientOrigin; ?>,
    sessionVarName : '<?php echo $nodeSocket->sessionVarName; ?>',
    socketLogFile : '<?php echo $nodeSocket->socketLogFile; ?>'
};
