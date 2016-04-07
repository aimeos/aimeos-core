<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();
$string = $this->translate( 'client', 'One or more products you are watching have been updated.' );

?>
<?php $this->block()->start( 'email/watch/html/intro' ); ?>
<p class="email-common-intro content-block">
<?php echo $enc->html( nl2br( $string ), $enc::TRUST ); ?>
<?php echo $this->get( 'introBody' ); ?>
</p>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'email/watch/html/intro' ); ?>
