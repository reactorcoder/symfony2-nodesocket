<?php
namespace Reactorcoder\Symfony2NodesocketBundle\Library\php\frames;

class LogoutFrame extends Authentication {

	/**
	 * @return string
	 */
	public function getType() {
		return self::TYPE_LOGOUT;
	}
}