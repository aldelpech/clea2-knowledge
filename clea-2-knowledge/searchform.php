<?php
/**
 * Search Form Template
 *
 * The search form template displays the search form.
 *
 * @package Unique
 * @subpackage Template
 * @since 0.1.0
 * @author Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2012, Justin Tadlock
 * @link http://themehybrid.com/themes/unique
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
?>
<div class="search">

	<form method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<div class="search-wrap">
			<input class="search-text" type="text" name="s" value="" placeholder="Recherche...">
			<input class="search-submit" name="submit" type="submit">		
		</div>
	</form>

</div><!-- .search -->