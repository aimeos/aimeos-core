<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

$enc = $this->encoder();

$operators = $this->get( 'operators', array() );
$operators = ( isset( $operators['compare'] ) ? $operators['compare'] : array() );

$operatorMap = array(
	'=~' => array( 'string' ),
	'~=' => array( 'string' ),
	'>' => array( 'date', 'datetime', 'integer', 'float' ),
	'>=' => array( 'date', 'datetime', 'integer', 'float' ),
	'<' => array( 'date', 'datetime', 'integer', 'float' ),
	'<=' => array( 'date', 'datetime', 'integer', 'float' ),
	'==' => array( 'boolean', 'date', 'datetime', 'integer', 'float', 'string' ),
	'!=' => array( 'boolean', 'date', 'datetime', 'integer', 'float', 'string' ),
);

$filter = $this->param( 'filter' );

if( !isset( $filter['key'][0] ) ) {
	$filter['key'][0] = $this->get( 'default', '' );
}

$cnt = count( (array) $filter['key'] );

?>
<table class="filter-items search-item">
<?php for( $pos = 0; $pos < $cnt; $pos++ ) : ?>
	<tr class="input-group filter-item">
		<td>
<?php	if( $pos < $cnt - 1 ) : ?>
			<div class="fa fa-minus" aria-label="<?php echo $enc->attr( $this->translate( 'admin', 'Remove filter' ) ); ?>"></div>
<?php	else : ?>
			<div class="fa fa-plus" aria-label="<?php echo $enc->attr( $this->translate( 'admin', 'Add filter' ) ); ?>"></div>
<?php	endif; ?>
		</td>
		<td>
			<fieldset>
				<select name="filter[key][]" class="filter-key form-control" data-selected="<?php echo $filter['key'][$pos]; ?>">
				</select><!--
				--><select name="filter[op][]" class="filter-operator form-control c-select">
<?php foreach( $operators as $code ) : ?>
					<option value="<?php echo $enc->attr( $code ); ?>"
						class="<?php echo ( isset( $operatorMap[$code] ) ? implode( ' ', $operatorMap[$code] ) : '' ); ?>"
						<?php echo ( isset( $filter['op'][$pos] ) && $filter['op'][$pos] === $code ? 'selected' : '' ); ?>
					><?php echo $enc->html( $this->translate( 'admin/ext', $code ) ); ?></option>
<?php endforeach; ?>
				</select><!--
				--><input name="filter[val][]" class="filter-value form-control" type="text" value="<?php echo $enc->attr( ( isset( $filter['val'][$pos] ) ? $filter['val'][$pos] : '' ) ); ?>" />
			</fieldset>
		</td>
	</tr>
<?php endfor; ?>
	<tr class="input-group prototype">
		<td>
			<div class="fa fa-plus" aria-label="<?php echo $enc->attr( $this->translate( 'admin', 'Add filter' ) ); ?>"></div>
		</td>
		<td>
			<fieldset>
				<select name="filter[key][]" class="filter-key form-control" data-selected="<?php echo $this->get( 'default' ); ?>" disabled="disabled">
				</select><!--
				--><select name="filter[op][]" class="filter-operator form-control c-select" disabled="disabled">
<?php foreach( $operators as $code ) : ?>
					<option value="<?php echo $enc->attr( $code ); ?>"
						class="<?php echo ( isset( $operatorMap[$code] ) ? implode( ' ', $operatorMap[$code] ) : '' ); ?>"
					><?php echo $enc->html( $this->translate( 'admin/ext', $code ) ); ?></option>
<?php endforeach; ?>
				</select><!--
				--><input name="filter[val][]" class="filter-value form-control" type="text" disabled="disabled" />
			</fieldset>
		</td>
	</tr>
</table>
