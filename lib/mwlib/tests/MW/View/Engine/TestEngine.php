<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017
 */


namespace Aimeos\MW\View\Engine;


class TestEngine implements \Aimeos\MW\View\Engine\Iface
{
	public function render( \Aimeos\MW\View\Iface $view, $filename, array $values = [] )
	{
		ob_start();
		include $filename;
		return ob_get_clean();
	}
}
