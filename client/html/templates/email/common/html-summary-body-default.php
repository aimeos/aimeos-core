<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2014-2016
 */

?>
<?php $this->block()->start( 'email/common/html/summary' ); ?>
<div class="common-summary content-block">
<?php echo $this->get( 'summaryBody' ); ?>
</div>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'email/common/html/summary' ); ?>
