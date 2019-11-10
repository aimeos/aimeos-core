<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Rebuilds the index.
 */
class IndexRebuild extends \Aimeos\MW\Setup\Task\Base
{
	private static $execute = false;


	/**
	 * Returns if the index should be rebuilt or not.
	 *
	 * @return bool
	 */
	private static function getExecute(): bool
	{
		return self::$execute;
	}


	/**
	 * Set if the index should be rebuilt or not.
	 *
	 * @param bool $value
	 */
	private static function setExecute( bool $value ): void
	{
		self::$execute = $value;
	}


	/**
	 * Force index rebuild.
	 */
	public static function forceExecute(): void
	{
		self::setExecute(true);
	}


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return ['TablesCreateMShop', 'MShopSetLocale'];
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return [];
	}


	/**
	 * Rebuilds the index if requested before via forceExecute().
	 */
	public function migrate()
	{
		if ( self::getExecute() === true ) {
			\Aimeos\MW\Common\Base::checkClass(\Aimeos\MShop\Context\Item\Iface::class, $this->additional);
			$this->msg('Rebuilding index', 0);

			$timestamp = date('Y-m-d H:i:s');
			\Aimeos\MShop::create($this->additional, 'index')
				->rebuild()
				->cleanup($timestamp);
			self::setExecute(false);

			$this->status('done');
		}
	}
}
