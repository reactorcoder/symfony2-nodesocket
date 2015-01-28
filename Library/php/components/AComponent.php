<?php

namespace Reactorcoder\Symfony2NodesocketBundle\Library\components;

/**
 * Class AComponent
 *
 * @package NodeSocket\Components
 */
abstract class AComponent {

	/**
	 * @var \NodeSocket
	 */
	protected $_nodeSocket;

	public function __construct($nodeSocket) {
		$this->_nodeSocket = $nodeSocket;
	}
}