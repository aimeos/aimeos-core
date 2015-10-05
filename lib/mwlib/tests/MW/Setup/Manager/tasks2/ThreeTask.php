<?php

namespace Aimeos\MW\Setup\Task;


class ThreeTask extends Base
{
	public function getPreDependencies()
	{
		return array( 'TwoTask' );
	}


	public function getPostDependencies()
	{
		return array();
	}


	protected function mysql()
	{
		$this->msg( 'Executing ThreeTask' );
		$this->status( 'OK' );
	}
}
