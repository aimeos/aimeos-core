<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage Jobs
 */


/**
 * ExtJS controller interface.
 *
 * @package Controller
 * @subpackage Jobs
 */
interface Controller_Jobs_Interface
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
	 * @throws Controller_Jobs_Exception If an error occurs
	 */
	public function run();
}
