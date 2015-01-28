<?php
namespace Reactorcoder\Symfony2NodesocketBundle\Library\Frame;

/**
 * todo implement setters and getters
 *
 * Class RuntimeServerConfiguration
 * @package NodeSocket\Frame
 */
class RuntimeServerConfiguration extends AFrame {

	/**
	 * @return string
	 */
	public function getType() {
		return self::TYPE_RUNTIME_CONFIGURATION;
	}

	/**
	 * @return bool
	 */
	public function isValid() {
		return false;
	}
}