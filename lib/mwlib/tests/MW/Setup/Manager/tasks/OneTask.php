<?php

namespace Aimeos\MW\Setup\Task;


class OneTask extends Base
{
	public function getPreDependencies()
	{
		return array();
	}


	public function getPostDependencies()
	{
		return array( 'TwoTask');
	}


	protected function mysql()
	{
		$this->status( '' );
		$this->msg( 'Executing OneTask' );
		$this->status( 'OK' );
	}
}
