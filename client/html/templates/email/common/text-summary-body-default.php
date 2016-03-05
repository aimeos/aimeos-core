<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

?>
<?php $this->block()->start( 'email/common/text/summary' ); ?>

<?php echo $this->get( 'summaryBody' ); ?>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'email/common/text/summary' ); ?>
