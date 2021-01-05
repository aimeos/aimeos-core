<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MAdmin
 * @subpackage Job
 */


namespace Aimeos\MAdmin\Job\Item;


/**
 * MAdmin job item Interface.
 *
 * @package MAdmin
 * @subpackage Job
 */
interface Iface extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\Status\Iface
{
	/**
	 * Returns the name of the attribute item.
	 *
	 * @return string Label of the attribute item
	 */
	public function getLabel() : string;

	/**
	 * Sets the new label of the attribute item.
	 *
	 * @param string|null $label Type label of the attribute item
	 * @return \Aimeos\MAdmin\Job\Item\Iface Job item for chaining method calls
	 */
	public function setLabel( ?string $label ) : \Aimeos\MAdmin\Job\Item\Iface;

	/**
	 * Returns the generated file path of the job.
	 *
	 * @return string Relative filesystem path to the generated file
	 */
	public function getPath() : string;

	/**
	 * Sets the new generated file path of the job.
	 *
	 * @param string|null $path Relative filesystem path to the generated file
	 * @return \Aimeos\MAdmin\Job\Item\Iface Job item for chaining method calls
	 */
	public function setPath( ?string $path ) : \Aimeos\MAdmin\Job\Item\Iface;
}
