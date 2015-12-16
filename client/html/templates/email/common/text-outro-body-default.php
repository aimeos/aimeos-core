<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

/// E-mail outro
$string = $this->translate( 'client', 'If you have any questions, please reply to this e-mail' );

?>


<?php echo wordwrap( strip_tags( $string ) ); ?>
<?php echo $this->get( 'outroBody' ); ?>
