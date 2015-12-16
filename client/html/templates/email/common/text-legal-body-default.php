<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

/// Payment e-mail legal information
$string = $this->translate( 'client', 
'All deliveries shipped are subject to our terms and conditions, which are available on our website and which you accepted when placing your order.

This email contains confidential information and is exclusively for the use of the person addressed. Should you not be that person then please reply to this e-mail and delete the e-mail and attachments afterwards.' );

?>



<?php echo wordwrap( strip_tags( $string ) ); ?>
<?php echo $this->get( 'legalBody' ); ?>
