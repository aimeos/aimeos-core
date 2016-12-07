<?php

/**
 * @copyright Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package Controller
 * @subpackage Jobs
 */


namespace Aimeos\Controller\Jobs;


/**
 * ExtJS controller interface.
 *
 * @package Controller
 * @subpackage Jobs
 */
interface Iface
{
	/**
	 * Returns the localized name of the job.
	 *
	 * @return string Name of the job
	 */
	public function getName();

	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription();

	/**
	 * Executes the job.
	 *
	 * @return void
	 * @throws \Aimeos\Controller\Jobs\Exception If an error occurs
	 */
	public function run();
}
