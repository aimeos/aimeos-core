<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

$enc = $this->encoder();
$string = $this->translate( 'client', 'An account has been created for you.' );

?>
<p class="email-common-intro content-block">
<?php echo $enc->html( nl2br( $string ), $enc::TRUST ); ?>
<?php echo $this->get( 'introBody' ); ?>
</p>
