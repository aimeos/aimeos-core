<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

?>
<?php $this->block()->start( 'email/common/text/summary/coupon' ); ?>


<?php echo strip_tags( $this->translate( 'client', 'Coupons' ) ); ?>:
<?php foreach( $this->extOrderBaseItem->getCoupons() as $code => $products ) : ?>
- <?php echo $code; ?>

<?php endforeach; ?>
<?php echo $this->get( 'couponBody' ); ?>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'email/common/text/summary/coupon' ); ?>
