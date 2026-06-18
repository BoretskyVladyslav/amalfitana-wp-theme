<?php
/**
 * Default Gutenberg block template and pattern for Experience posts.
 *
 * @package Amalfitana_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build editorial block markup from the gold-standard tour detail reference.
 *
 * @return string
 */
function amalfitana_get_experience_editorial_block_markup() {
	$reference = get_template_directory() . '/templates/page-tour-detail.html';

	if ( ! file_exists( $reference ) ) {
		return '<!-- wp:paragraph --><p></p><!-- /wp:paragraph -->';
	}

	$lines = file( $reference, FILE_IGNORE_NEW_LINES );

	if ( false === $lines ) {
		return '<!-- wp:paragraph --><p></p><!-- /wp:paragraph -->';
	}

	$start = null;
	$end   = null;

	foreach ( $lines as $index => $line ) {
		if ( null === $start && false !== strpos( $line, '<div class="tour-detail-content__prose">' ) ) {
			$start = $index;
		}

		if ( null !== $start && false !== strpos( $line, 'tour-detail-content__callout' ) ) {
			$end = $index;
			break;
		}
	}

	if ( null === $start || null === $end ) {
		return '<!-- wp:paragraph --><p></p><!-- /wp:paragraph -->';
	}

	$chunk       = array_slice( $lines, $start, $end - $start + 2 );
	$html        = implode( "\n", $chunk );
	$included_at = strpos( $html, '<section class="tour-detail-content__section">' );

	if ( false === $included_at ) {
		$included_at = strpos( $html, '<section class="tour-detail-content__section tour-detail-content__section--first">' );
	}

	if ( false !== $included_at ) {
		$before = substr( $html, 0, $included_at );
		$after  = substr( $html, $included_at );
		$after  = preg_replace(
			'#<section class="tour-detail-content__section">\s*<h2 class="tour-detail-content__heading[^"]*">\s*У вартість входить\s*</h2>.*?</section>\s*#su',
			'',
			$after,
			1
		);
		$html = $before . amalfitana_get_experience_acf_marker() . "\n" . $after;
	}

	return "<!-- wp:html -->\n" . trim( $html ) . "\n<!-- /wp:html -->";
}

/**
 * Parsed block template assigned to new Experience posts.
 *
 * @return array<int, array<string, mixed>>
 */
function amalfitana_get_experience_default_block_template() {
	$blocks = parse_blocks( amalfitana_get_experience_editorial_block_markup() );

	return array_values(
		array_filter(
			$blocks,
			static function ( $block ) {
				return ! empty( $block['blockName'] );
			}
		)
	);
}

/**
 * Register the Experience editorial pattern category and pattern.
 */
function amalfitana_register_experience_block_patterns() {
	if ( ! function_exists( 'register_block_pattern' ) ) {
		return;
	}

	register_block_pattern_category(
		'amalfitana-experiences',
		array(
			'label' => 'Досвіди',
		)
	);

	register_block_pattern(
		'amalfitana/experience-editorial-content',
		array(
			'title'       => 'Контент досвіду',
			'description' => 'Повний редакторський макет сторінки досвіду з галереями, списками та callout.',
			'categories'  => array( 'amalfitana-experiences' ),
			'postTypes'   => array( 'experience' ),
			'content'     => amalfitana_get_experience_editorial_block_markup(),
		)
	);
}
add_action( 'init', 'amalfitana_register_experience_block_patterns', 25 );
