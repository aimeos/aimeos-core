<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2022
 * @package Controller
 * @subpackage Jobs
 */


namespace Aimeos\Controller\Jobs;


/**
 * Job controller interface
 *
 * @package Controller
 * @subpackage Jobs
 */
interface Iface
{
	/**
	 * Returns the localized name of the job
	 *
	 * @return string Name of the job
	 */
	public function getName() : string;

	/**
	 * Returns the localized description of the job
	 *
	 * @return string Description of the job
	 */
	public function getDescription() : string;

	/**
	 * Executes the job
	 *
	 * @return void
	 * @throws \Aimeos\Controller\Jobs\Exception If an error occurs
	 */
	public function run();
}
