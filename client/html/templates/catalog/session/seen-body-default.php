<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

$enc = $this->encoder();
$seenList = $this->get( 'seenItems', array() );

/** client/html/catalog/session/seen/count/enable
 * Displays the number of last seen products in the header of the last seen list
 *
 * This configuration option enables or disables displaying the total number
 * of last seen products in the header of section. This increases the usability
 * if more than the shown products are available in the list but this depends on
 * the design of the site.
 *
 * @param integer Zero to disable the counter, one to enable it
 * @since 2014.09
 * @category Developer
 * @see client/html/catalog/session/pinned/count/enable
 */
$count = $this->config( 'client/html/catalog/session/seen/count/enable', 1 );

?>
<section class="catalog-session-seen">
	<h2 class="header">
<?php echo $this->translate( 'client/html', 'Last seen' ); ?>
<?php if( $count ) : ?>
		<span class="count"><?php echo count( $seenList ); ?></span>
<?php endif; ?>
	</h2>
	<ul class="seen-items"> 
<?php	foreach( $seenList as $seen ) : ?>
		<li class="seen-item">
<?php		echo $seen; ?>
		</li>
<?php	endforeach; ?>
	</ul>
<?php echo $this->seenBody; ?>
</section>
