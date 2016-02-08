<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

?>



<?php try { ?>
<?php	$service = $this->extOrderBaseItem->getService( 'delivery' ); ?>
<?php	echo strip_tags( $this->translate( 'client', 'delivery' ) ); ?>: <?php echo strip_tags( $service->getName() ); ?>

<?php	foreach( $service->getAttributes() as $attribute ) : ?>
<?php		if( $attribute->getType() === 'delivery' ) : ?>
<?php
				$name = ( $attribute->getName() != '' ? $attribute->getName() : $this->translate( 'client/code', $attribute->getCode() ) );

				switch( $attribute->getValue() )
				{
					case 'array':
					case 'object':
						$value = join( ', ', (array) $attribute->getValue() );
						break;
					default:
						$value = $attribute->getValue();
				}
?>
- <?php echo strip_tags( $name ); ?>: <?php echo strip_tags( $value ); ?>

<?php		endif; ?>
<?php	endforeach; ?>
<?php } catch( Exception $e ) { ; } ?>

<?php try { ?>
<?php	$service = $this->extOrderBaseItem->getService( 'payment' ); ?>
<?php	echo strip_tags( $this->translate( 'client', 'payment' ) ); ?>: <?php echo strip_tags( $service->getName() ); ?>

<?php	foreach( $service->getAttributes() as $attribute ) : ?>
<?php		if( $attribute->getType() === 'payment' ) : ?>
<?php
				$name = ( $attribute->getName() != '' ? $attribute->getName() : $this->translate( 'client/code', $attribute->getCode() ) );

				switch( $attribute->getValue() )
				{
					case 'array':
					case 'object':
						$value = join( ', ', (array) $attribute->getValue() );
						break;
					default:
						$value = $attribute->getValue();
				}
?>
- <?php echo strip_tags( $name ); ?>: <?php echo strip_tags( $value ); ?>

<?php		endif; ?>
<?php	endforeach; ?>
<?php } catch( Exception $e ) { ; } ?>
<?php echo $this->get( 'serviceBody' ); ?>
