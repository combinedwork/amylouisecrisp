<?php
/**
 * Turns the plain-text boxes in the recipe form into structured recipe data.
 *
 * Ingredients: one per line, e.g. "300g dried pasta" or "1 white onion, chopped".
 *              A line ending in ":" starts a new group, e.g. "Creamy Pesto Sauce:".
 * Method:      one step per line. Add [5 min] at the end of a step to give it a Cook Mode timer.
 * Tips:        one per line, "Heading: text".
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Units the servings scaler understands. */
function alc_units() {
	return 'kg|g|mg|ml|l|litres?|liters?|tbsp|tsp|tablespoons?|teaspoons?|cups?|jars?|tins?|cans?|packs?|packets?|bags?|pinch(?:es)?|handfuls?|cloves?|slices?|sprigs?|sheets?|oz|lb|lbs';
}

/** "1 1/2", "1½", "0.5", "1,5", "½" → float, or null. */
function alc_parse_number( $raw ) {
	$raw   = trim( str_replace( ',', '.', $raw ) );
	$fracs = array( '½' => 0.5, '¼' => 0.25, '¾' => 0.75, '⅓' => 1 / 3, '⅔' => 2 / 3, '⅛' => 0.125 );
	$total = 0.0;
	foreach ( $fracs as $sym => $val ) {
		if ( false !== strpos( $raw, $sym ) ) {
			$total += $val;
			$raw    = trim( str_replace( $sym, '', $raw ) );
		}
	}
	if ( preg_match( '/^(\d+)\s+(\d+)\s*\/\s*(\d+)$/', $raw, $m ) ) {
		return (float) $m[1] + ( $m[3] ? $m[2] / $m[3] : 0 );
	}
	if ( preg_match( '/^(\d+)\s*\/\s*(\d+)$/', $raw, $m ) ) {
		return $m[2] ? $m[1] / $m[2] : null;
	}
	if ( '' === $raw ) {
		return $total ? $total : null;
	}
	return is_numeric( $raw ) ? (float) $raw + $total : null;
}

/** Ingredient text → [ ['group' => 'Sauce', 'items' => [ [300, 'g', 'dried pasta'], ... ] ] ] */
function alc_parse_ingredients( $text ) {
	$groups  = array();
	$current = array( 'group' => '', 'items' => array() );
	$lines   = preg_split( '/\r\n|\r|\n/', (string) $text );
	$num     = '(?:\d+\s+\d+\s*\/\s*\d+|\d+\s*\/\s*\d+|\d+(?:[.,]\d+)?\s*[½¼¾⅓⅔⅛]?|[½¼¾⅓⅔⅛])';

	foreach ( $lines as $line ) {
		$line = trim( preg_replace( '/^[\-\*•]\s*/u', '', trim( $line ) ) );
		if ( '' === $line ) {
			continue;
		}
		if ( ':' === mb_substr( $line, -1 ) ) {
			if ( $current['items'] || '' !== $current['group'] ) {
				$groups[] = $current;
			}
			$current = array( 'group' => trim( mb_substr( $line, 0, -1 ) ), 'items' => array() );
			continue;
		}
		// Ranges like "2-3 garlic cloves" or "1 to 2 tbsp" are kept exactly as written.
		$is_range = preg_match( '/^\d+(?:[.,]\d+)?\s*(?:-|–|to)\s*\d/iu', $line );
		if ( ! $is_range && preg_match( '/^(' . $num . ')\s*(?:(' . alc_units() . ')\b\.?)?\s*(.+)$/iu', $line, $m ) ) {
			$amount = alc_parse_number( $m[1] );
			$unit   = isset( $m[2] ) ? strtolower( $m[2] ) : '';
			$name   = trim( $m[3] );
			if ( null !== $amount && '' !== $name ) {
				$current['items'][] = array( round( $amount, 3 ), $unit, $name );
				continue;
			}
		}
		$current['items'][] = array( null, '', $line );
	}
	if ( $current['items'] ) {
		$groups[] = $current;
	}
	return $groups;
}

/** Method text → [ 'steps' => [...], 'timers' => [ stepNumber => minutes ] ] */
function alc_parse_method( $text ) {
	$steps  = array();
	$timers = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) $text ) as $line ) {
		$line = trim( $line );
		// Remove numbering like "1." "2)" "Step 3:" (but not "1-2 minutes")
		$line = preg_replace( '/^(?:step\s*)?\d+\s*(?:[\.\)]|:(?!\d))\s*/i', '', $line );
		if ( '' === $line ) {
			continue;
		}
		if ( preg_match( '/\[\s*(\d+(?:[.,]\d+)?)\s*(m|min|mins|minutes?|h|hr|hrs|hours?)\s*\]\s*$/i', $line, $m ) ) {
			$mins = (float) str_replace( ',', '.', $m[1] );
			if ( 'h' === strtolower( $m[2][0] ) ) {
				$mins *= 60;
			}
			$line = trim( preg_replace( '/\[[^\]]*\]\s*$/', '', $line ) );
			$timers[ count( $steps ) + 1 ] = $mins;
		}
		$steps[] = $line;
	}
	return array( 'steps' => $steps, 'timers' => (object) $timers );
}

/** Tips text → [ [heading, text], ... ] */
function alc_parse_tips( $text ) {
	$tips = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) $text ) as $line ) {
		$line = trim( $line );
		if ( '' === $line ) {
			continue;
		}
		$parts  = explode( ':', $line, 2 );
		$is_heading = 2 === count( $parts ) && mb_strlen( $parts[0] ) <= 40 && 0 !== strpos( $parts[1], '//' );
		$tips[]     = $is_heading ? array( trim( $parts[0] ), trim( $parts[1] ) ) : array( 'Tip', $line );
	}
	return $tips;
}

/** Any YouTube link (watch, youtu.be, shorts, embed) → video ID. */
function alc_youtube_id( $url ) {
	$url = trim( (string) $url );
	if ( preg_match( '~(?:youtu\.be/|youtube(?:-nocookie)?\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/))([A-Za-z0-9_-]{11})~', $url, $m ) ) {
		return $m[1];
	}
	return preg_match( '/^[A-Za-z0-9_-]{11}$/', $url ) ? $url : '';
}
