<?php
/**
 * Single Experience template.
 *
 * Dynamic tour detail layout based on page-tour-detail.html (Dolce Vita gold standard).
 *
 * @package Amalfitana_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

remove_filter( 'the_content', 'amalfitana_inject_experience_acf_sections', 12 );
remove_filter( 'the_content', 'amalfitana_normalize_experience_content_wrappers', 11 );

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php block_template_part( 'header' ); ?>

<?php while ( have_posts() ) : the_post(); ?>

<?php
$post_id        = get_the_ID();
$hero_image     = get_the_post_thumbnail_url( $post_id, 'full' );
$duration       = amalfitana_get_experience_field( $post_id, 'experience_duration' );
$price          = amalfitana_get_experience_field( $post_id, 'experience_price' );
$highlights     = function_exists( 'get_field' ) ? (string) get_field( 'experience_highlights', $post_id ) : '';
$included_raw   = function_exists( 'get_field' ) ? (string) get_field( 'experience_included', $post_id ) : '';
$not_included   = function_exists( 'get_field' ) ? trim( (string) get_field( 'experience_not_included', $post_id ) ) : '';
$what_to_take   = function_exists( 'get_field' ) ? (string) get_field( 'experience_what_to_take_list', $post_id ) : '';
$recommendation = function_exists( 'get_field' ) ? (string) get_field( 'experience_recommendation', $post_id ) : '';
$callout        = function_exists( 'get_field' ) ? trim( (string) get_field( 'experience_callout', $post_id ) ) : '';
$check_icon_svg = amalfitana_get_tour_detail_check_icon_svg();

$highlights   = str_replace( array( "\r\n", "\r" ), "\n", $highlights );
$included_raw = str_replace( array( "\r\n", "\r" ), "\n", $included_raw );
$what_to_take = str_replace( array( "\r\n", "\r" ), "\n", $what_to_take );

if ( ! $hero_image ) {
	$hero_image = amalfitana_get_media_url_by_filename( 'Tours-hero__img.png' );
}
?>

<section
	class="tour-detail-hero"
	aria-label="Tour detail hero"
	style="--tour-detail-hero-background-image: url('<?php echo esc_url( $hero_image ); ?>');"
>
	<div class="tour-detail-hero__container">
		<div class="tour-detail-hero__content">
			<span class="tour-detail-hero__subtitle animate-on-scroll"><?php the_title(); ?></span>
			<h1 class="tour-detail-hero__title animate-on-scroll animate-on-scroll--delay-200"><?php the_field( 'experience_hero_subtitle' ); ?></h1>
		</div>
	</div>
</section>
<section class="tour-detail-content" aria-label="Tour detail content">
	<div class="tour-detail-content__container">
		<nav class="tours-grid-section__breadcrumbs tour-detail-content__breadcrumbs" aria-label="Breadcrumb">
			<ol class="tours-grid-section__breadcrumbs-list">
				<li class="tours-grid-section__breadcrumbs-item">
					<a class="tours-grid-section__breadcrumbs-link" href="<?php echo esc_url( home_url( '/' ) ); ?>">Головна</a>
				</li>
				<li class="tours-grid-section__breadcrumbs-item tours-grid-section__breadcrumbs-separator" aria-hidden="true">
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none"><mask id="mask0_tour_detail_content_breadcrumb" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="16" height="16"><rect width="16" height="16" fill="#D9D9D9"/></mask><g mask="url(#mask0_tour_detail_content_breadcrumb)"><path d="M8.39992 8L5.33325 4.93333L6.26659 4L10.2666 8L6.26659 12L5.33325 11.0667L8.39992 8Z" fill="#C3C3C3"/></g></svg>
				</li>
				<li class="tours-grid-section__breadcrumbs-item">
					<a class="tours-grid-section__breadcrumbs-link" href="<?php echo esc_url( amalfitana_get_tours_page_url() ); ?>">Досвіди</a>
				</li>
				<li class="tours-grid-section__breadcrumbs-item tours-grid-section__breadcrumbs-separator" aria-hidden="true">
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none"><mask id="mask1_tour_detail_content_breadcrumb" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="16" height="16"><rect width="16" height="16" fill="#D9D9D9"/></mask><g mask="url(#mask1_tour_detail_content_breadcrumb)"><path d="M8.39992 8L5.33325 4.93333L6.26659 4L10.2666 8L6.26659 12L5.33325 11.0667L8.39992 8Z" fill="#C3C3C3"/></g></svg>
				</li>
				<li class="tours-grid-section__breadcrumbs-item">
					<span class="tours-grid-section__breadcrumbs-current" aria-current="page"><?php the_title(); ?></span>
				</li>
			</ol>
		</nav>

		<div class="tour-detail-content__grid">
			<div class="tour-detail-content__main">
				<div class="tour-detail-content__metrics animate-on-scroll" role="list" aria-label="Tour details">
					<div class="tour-detail-content__metric" role="listitem">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
							<mask id="mask0_14_609" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="24">
							<rect width="24" height="24" fill="#D9D9D9"/>
							</mask>
							<g mask="url(#mask0_14_609)">
							<path d="M5.30775 21.5C4.80258 21.5 4.375 21.325 4.025 20.975C3.675 20.625 3.5 20.1974 3.5 19.6923V6.30777C3.5 5.8026 3.675 5.37502 4.025 5.02502C4.375 4.67502 4.80258 4.50002 5.30775 4.50002H6.69225V2.38477H8.23075V4.50002H15.8077V2.38477H17.3077V4.50002H18.6923C19.1974 4.50002 19.625 4.67502 19.975 5.02502C20.325 5.37502 20.5 5.8026 20.5 6.30777V19.6923C20.5 20.1974 20.325 20.625 19.975 20.975C19.625 21.325 19.1974 21.5 18.6923 21.5H5.30775ZM5.30775 20H18.6923C18.7692 20 18.8398 19.9679 18.9038 19.9038C18.9679 19.8398 19 19.7693 19 19.6923V10.3078H5V19.6923C5 19.7693 5.03208 19.8398 5.09625 19.9038C5.16025 19.9679 5.23075 20 5.30775 20ZM5 8.80777H19V6.30777C19 6.23077 18.9679 6.16026 18.9038 6.09626C18.8398 6.0321 18.7692 6.00002 18.6923 6.00002H5.30775C5.23075 6.00002 5.16025 6.0321 5.09625 6.09626C5.03208 6.16026 5 6.23077 5 6.30777V8.80777ZM12 14.077C11.7552 14.077 11.5465 13.9908 11.374 13.8183C11.2017 13.6459 11.1155 13.4373 11.1155 13.1923C11.1155 12.9474 11.2017 12.7388 11.374 12.5663C11.5465 12.3939 11.7552 12.3078 12 12.3078C12.2448 12.3078 12.4535 12.3939 12.626 12.5663C12.7983 12.7388 12.8845 12.9474 12.8845 13.1923C12.8845 13.4373 12.7983 13.6459 12.626 13.8183C12.4535 13.9908 12.2448 14.077 12 14.077ZM7.374 13.8183C7.20167 13.6459 7.1155 13.4373 7.1155 13.1923C7.1155 12.9474 7.20167 12.7388 7.374 12.5663C7.5465 12.3939 7.75517 12.3078 8 12.3078C8.24483 12.3078 8.4535 12.3939 8.626 12.5663C8.79833 12.7388 8.8845 12.9474 8.8845 13.1923C8.8845 13.4373 8.79833 13.6459 8.626 13.8183C8.4535 13.9908 8.24483 14.077 8 14.077C7.75517 14.077 7.5465 13.9908 7.374 13.8183ZM16 14.077C15.7552 14.077 15.5465 13.9908 15.374 13.8183C15.2017 13.6459 15.1155 13.4373 15.1155 13.1923C15.1155 12.9474 15.2017 12.7388 15.374 12.5663C15.5465 12.3939 15.7552 12.3078 16 12.3078C16.2448 12.3078 16.4535 12.3939 16.626 12.5663C16.7983 12.7388 16.8845 12.9474 16.8845 13.1923C16.8845 13.4373 16.7983 13.6459 16.626 13.8183C16.4535 13.9908 16.2448 14.077 16 14.077ZM12 18C11.7552 18 11.5465 17.9138 11.374 17.7413C11.2017 17.5689 11.1155 17.3603 11.1155 17.1155C11.1155 16.8705 11.2017 16.6618 11.374 16.4895C11.5465 16.317 11.7552 16.2308 12 16.2308C12.2448 16.2308 12.4535 16.317 12.626 16.4895C12.7983 16.6618 12.8845 16.8705 12.8845 17.1155C12.8845 17.3603 12.7983 17.5689 12.626 17.7413C12.4535 17.9138 12.2448 18 12 18ZM7.374 17.7413C7.20167 17.5689 7.1155 17.3603 7.1155 17.1155C7.1155 16.8705 7.20167 16.6618 7.374 16.4895C7.5465 16.317 7.75517 16.2308 8 16.2308C8.24483 16.2308 8.4535 16.317 8.626 16.4895C8.79833 16.6618 8.8845 16.8705 8.8845 17.1155C8.8845 17.3603 8.79833 17.5689 8.626 17.7413C8.4535 17.9138 8.24483 18 8 18C7.75517 18 7.5465 17.9138 7.374 17.7413ZM16 18C15.7552 18 15.5465 17.9138 15.374 17.7413C15.2017 17.5689 15.1155 17.3603 15.1155 17.1155C15.1155 16.8705 15.2017 16.6618 15.374 16.4895C15.5465 16.317 15.7552 16.2308 16 16.2308C16.2448 16.2308 16.4535 16.317 16.626 16.4895C16.7983 16.6618 16.8845 16.8705 16.8845 17.1155C16.8845 17.3603 16.7983 17.5689 16.626 17.7413C16.4535 17.9138 16.2448 18 16 18Z" fill="#E87F24"/>
							</g>
						</svg>
						<div class="tour-detail-content__metric-body">
							<p class="tour-detail-content__metric-label">Тривалість</p>
							<p class="tour-detail-content__metric-value"><?php echo esc_html( $duration ); ?></p>
						</div>
					</div>
					<div class="tour-detail-content__metric" role="listitem">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
							<mask id="mask0_14_615" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="24">
							<rect width="24" height="24" fill="#D9D9D9"/>
							</mask>
							<g mask="url(#mask0_14_615)">
							<path d="M15 21C13.0333 21 11.2833 20.4417 9.75 19.325C8.21667 18.2083 7.13333 16.7667 6.5 15H3V13H6.05C6 12.6 5.97917 12.2292 5.9875 11.8875C5.99583 11.5458 6.01667 11.25 6.05 11H3V9H6.5C7.13333 7.23333 8.21667 5.79167 9.75 4.675C11.2833 3.55833 13.0333 3 15 3C16.15 3 17.2375 3.20417 18.2625 3.6125C19.2875 4.02083 20.2 4.58333 21 5.3L19.575 6.7C18.9583 6.16667 18.2625 5.75 17.4875 5.45C16.7125 5.15 15.8833 5 15 5C13.5833 5 12.3167 5.37083 11.2 6.1125C10.0833 6.85417 9.24167 7.81667 8.675 9H15V11H8.075C8.00833 11.45 7.98333 11.8458 8 12.1875C8.01667 12.5292 8.04167 12.8 8.075 13H15V15H8.675C9.24167 16.1833 10.0833 17.1458 11.2 17.8875C12.3167 18.6292 13.5833 19 15 19C15.8833 19 16.7125 18.85 17.4875 18.55C18.2625 18.25 18.9583 17.8333 19.575 17.3L21 18.7C20.2 19.4167 19.2875 19.9792 18.2625 20.3875C17.2375 20.7958 16.15 21 15 21Z" fill="#E87F24"/>
							</g>
						</svg>
						<div class="tour-detail-content__metric-body">
							<p class="tour-detail-content__metric-label">Вартість</p>
							<p class="tour-detail-content__metric-value"><?php echo esc_html( $price ); ?></p>
						</div>
					</div>
					<div class="tour-detail-content__metric" role="listitem">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
							<mask id="mask0_14_621" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="24">
							<rect width="24" height="24" fill="#D9D9D9"/>
							</mask>
							<g mask="url(#mask0_14_621)">
							<path d="M7 23L9.8 8.9L8 9.6V13H6V8.3L11.05 6.15C11.2833 6.05 11.5292 5.99167 11.7875 5.975C12.0458 5.95833 12.2917 5.99167 12.525 6.075C12.7583 6.15833 12.9792 6.275 13.1875 6.425C13.3958 6.575 13.5667 6.76667 13.7 7L14.7 8.6C15.1333 9.3 15.7208 9.875 16.4625 10.325C17.2042 10.775 18.05 11 19 11V13C17.8333 13 16.7917 12.7583 15.875 12.275C14.9583 11.7917 14.175 11.175 13.525 10.425L12.9 13.5L15 15.5V23H13V16.5L10.9 14.9L9.1 23H7ZM12.0875 4.9125C11.6958 4.52083 11.5 4.05 11.5 3.5C11.5 2.95 11.6958 2.47917 12.0875 2.0875C12.4792 1.69583 12.95 1.5 13.5 1.5C14.05 1.5 14.5208 1.69583 14.9125 2.0875C15.3042 2.47917 15.5 2.95 15.5 3.5C15.5 4.05 15.3042 4.52083 14.9125 4.9125C14.5208 5.30417 14.05 5.5 13.5 5.5C12.95 5.5 12.4792 5.30417 12.0875 4.9125Z" fill="#E87F24"/>
							</g>
						</svg>
						<div class="tour-detail-content__metric-body">
							<p class="tour-detail-content__metric-label">Формат</p>
							<?php echo amalfitana_render_experience_format_metric( $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in renderer. ?>
						</div>
					</div>
				</div>

				<h2 class="tour-detail-content__heading animate-on-scroll animate-on-scroll--delay-100">Про подорож</h2>

				<div class="tour-detail-content__prose">
					<?php echo get_field( 'experience_main_text' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- ACF WYSIWYG field. ?>
				</div>

				<?php if ( $highlights ) : ?>
				<section class="tour-detail-content__section tour-detail-content__section--first">
					<h2 class="tour-detail-content__heading animate-on-scroll">Що вас чекає</h2>
					<ul class="tour-detail-list animate-on-scroll">
						<?php
						foreach ( explode( "\n", $highlights ) as $highlight_line ) :
							$highlight_line = trim( $highlight_line );
							if ( '' === $highlight_line ) {
								continue;
							}
							?>
						<li class="tour-detail-list__item">
							<?php echo $check_icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG markup. ?>
							<span class="tour-detail-list__text"><?php echo esc_html( $highlight_line ); ?></span>
						</li>
						<?php endforeach; ?>
					</ul>
				</section>
				<?php endif; ?>

				<?php if ( $included_raw ) : ?>
				<section class="tour-detail-content__section">
					<h2 class="tour-detail-content__heading animate-on-scroll">У вартість входить</h2>
					<ul class="tour-detail-list tour-detail-list--included animate-on-scroll">
						<?php
						foreach ( explode( "\n", $included_raw ) as $included_line ) :
							$included_line = trim( $included_line );
							if ( '' === $included_line ) {
								continue;
							}
							?>
						<li class="tour-detail-list__item">
							<span class="tour-detail-list__dash" aria-hidden="true"></span>
							<span class="tour-detail-list__text"><?php echo esc_html( $included_line ); ?></span>
						</li>
						<?php endforeach; ?>
					</ul>
					<?php if ( $not_included ) : ?>
					<p class="tour-detail-content__note animate-on-scroll">Не включено: <?php echo esc_html( $not_included ); ?></p>
					<?php endif; ?>
				</section>
				<?php elseif ( $not_included ) : ?>
				<section class="tour-detail-content__section">
					<p class="tour-detail-content__note animate-on-scroll">Не включено: <?php echo esc_html( $not_included ); ?></p>
				</section>
				<?php endif; ?>

				<?php if ( $what_to_take ) : ?>
				<section class="tour-detail-content__section">
					<h2 class="tour-detail-content__heading animate-on-scroll">Що взяти з собою</h2>
					<ul class="tour-detail-list animate-on-scroll">
						<?php
						foreach ( explode( "\n", $what_to_take ) as $take_line ) :
							$take_line = trim( $take_line );
							if ( '' === $take_line ) {
								continue;
							}
							?>
						<li class="tour-detail-list__item">
							<?php echo $check_icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG markup. ?>
							<span class="tour-detail-list__text"><?php echo esc_html( $take_line ); ?></span>
						</li>
						<?php endforeach; ?>
					</ul>
				</section>
				<?php endif; ?>

				<?php if ( trim( wp_strip_all_tags( $recommendation ) ) ) : ?>
				<section class="tour-detail-content__section">
					<h2 class="tour-detail-content__heading animate-on-scroll">Рекомендація</h2>
					<div class="tour-detail-content__section-text animate-on-scroll">
						<?php echo wp_kses_post( $recommendation ); ?>
					</div>
				</section>
				<?php endif; ?>

				<?php if ( $callout ) : ?>
				<blockquote class="tour-detail-content__callout animate-on-scroll"><?php echo nl2br( esc_html( $callout ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- nl2br after esc_html. ?></blockquote>
				<?php endif; ?>
			</div>

			<aside class="tour-detail-content__sidebar animate-on-scroll" aria-label="Booking sidebar">
				<div class="tour-detail-content__sidebar-inner">
					<div class="tour-checkout-card">
						<header class="tour-checkout-card__header">
							<p class="tour-checkout-card__subtitle">checkout</p>
							<h2 class="tour-checkout-card__title" id="tour-checkout-title">Створіть мій день на узбережжі</h2>
						</header>

						<p class="tour-checkout-card__desc">І якраз у подорожі — це те, де все відчувається легко</p>

						<form class="tour-checkout-form subscribe-section__form" action="#" method="post" novalidate aria-labelledby="tour-checkout-title">
							<div class="tour-checkout-form__fields">
								<div class="tour-checkout-form__field subscribe-section__field">
									<label class="screen-reader-text" for="tour-checkout-name">Ваше ім'я</label>
									<div class="tour-checkout-form__control">
										<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
											<mask id="mask0_14_726" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="24">
											  <rect width="24" height="24" fill="#D9D9D9"/>
											</mask>
											<g mask="url(#mask0_14_726)">
											  <path d="M6.023 17.2923C6.873 16.6616 7.799 16.1635 8.801 15.798C9.80283 15.4327 10.8692 15.25 12 15.25C13.1308 15.25 14.1972 15.4327 15.199 15.798C16.201 16.1635 17.127 16.6616 17.977 17.2923C18.5987 16.6089 19.0912 15.8179 19.4548 14.9192C19.8183 14.0206 20 13.0475 20 12C20 9.78333 19.2208 7.89583 17.6625 6.3375C16.1042 4.77917 14.2167 4 12 4C9.78333 4 7.89583 4.77917 6.3375 6.3375C4.77917 7.89583 4 9.78333 4 12C4 13.0475 4.18175 14.0206 4.54525 14.9192C4.90875 15.8179 5.40133 16.6089 6.023 17.2923ZM9.6905 11.8095C9.0635 11.1827 8.75 10.4128 8.75 9.5C8.75 8.58717 9.0635 7.81733 9.6905 7.1905C10.3173 6.5635 11.0872 6.25 12 6.25C12.9128 6.25 13.6827 6.5635 14.3095 7.1905C14.9365 7.81733 15.25 8.58717 15.25 9.5C15.25 10.4128 14.9365 11.1827 14.3095 11.8095C13.6827 12.4365 12.9128 12.75 12 12.75C11.0872 12.75 10.3173 12.4365 9.6905 11.8095ZM12 21.5C10.6808 21.5 9.44333 21.2519 8.2875 20.7558C7.13167 20.2596 6.12625 19.5839 5.27125 18.7288C4.41608 17.8738 3.74042 16.8683 3.24425 15.7125C2.74808 14.5567 2.5 13.3192 2.5 12C2.5 10.6808 2.74808 9.44333 3.24425 8.2875C3.74042 7.13167 4.41608 6.12625 5.27125 5.27125C6.12625 4.41608 7.13167 3.74042 8.2875 3.24425C9.44333 2.74808 10.6808 2.5 12 2.5C13.3192 2.5 14.5567 2.74808 15.7125 3.24425C16.8683 3.74042 17.8738 4.41608 18.7288 5.27125C19.5839 6.12625 20.2596 7.13167 20.7558 8.2875C21.2519 9.44333 21.5 10.6808 21.5 12C21.5 13.3192 21.2519 14.5567 20.7558 15.7125C20.2596 16.8683 19.5839 17.8738 18.7288 18.7288C17.8738 19.5839 16.8683 20.2596 15.7125 20.7558C14.5567 21.2519 13.3192 21.5 12 21.5ZM14.6105 19.5645C15.4483 19.274 16.1923 18.8679 16.8422 18.3462C16.1923 17.8436 15.458 17.4519 14.6395 17.1712C13.8208 16.8904 12.941 16.75 12 16.75C11.059 16.75 10.1776 16.8888 9.35575 17.1663C8.53392 17.4439 7.80125 17.8372 7.15775 18.3462C7.80775 18.8679 8.55167 19.274 9.3895 19.5645C10.2273 19.8548 11.0975 20 12 20C12.9025 20 13.7727 19.8548 14.6105 19.5645ZM13.248 10.748C13.5827 10.4135 13.75 9.9975 13.75 9.5C13.75 9.0025 13.5827 8.5865 13.248 8.252C12.9135 7.91733 12.4975 7.75 12 7.75C11.5025 7.75 11.0865 7.91733 10.752 8.252C10.4173 8.5865 10.25 9.0025 10.25 9.5C10.25 9.9975 10.4173 10.4135 10.752 10.748C11.0865 11.0827 11.5025 11.25 12 11.25C12.4975 11.25 12.9135 11.0827 13.248 10.748Z" fill="#C3C3C3"/>
											</g>
										  </svg>
										<input
											type="text"
											class="subscribe-section__input tour-checkout-form__input"
											id="tour-checkout-name"
											name="name"
											placeholder="Ваше ім'я"
											autocomplete="name"
											minlength="2"
											required
										>
									</div>
								</div>

								<div class="tour-checkout-form__field subscribe-section__field">
									<label class="screen-reader-text" for="tour-checkout-email">Email</label>
									<div class="tour-checkout-form__control">
										<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
											<mask id="mask0_14_730" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="24">
											  <rect width="24" height="24" fill="#D9D9D9"/>
											</mask>
											<g mask="url(#mask0_14_730)">
											  <path d="M7 23L9.8 8.9L8 9.6V13H6V8.3L11.05 6.15C11.2833 6.05 11.5292 5.99167 11.7875 5.975C12.0458 5.95833 12.2917 5.99167 12.525 6.075C12.7583 6.15833 12.9792 6.275 13.1875 6.425C13.3958 6.575 13.5667 6.76667 13.7 7L14.7 8.6C15.1333 9.3 15.7208 9.875 16.4625 10.325C17.2042 10.775 18.05 11 19 11V13C17.8333 13 16.7917 12.7583 15.875 12.275C14.9583 11.7917 14.175 11.175 13.525 10.425L12.9 13.5L15 15.5V23H13V16.5L10.9 14.9L9.1 23H7ZM12.0875 4.9125C11.6958 4.52083 11.5 4.05 11.5 3.5C11.5 2.95 11.6958 2.47917 12.0875 2.0875C12.4792 1.69583 12.95 1.5 13.5 1.5C14.05 1.5 14.5208 1.69583 14.9125 2.0875C15.3042 2.47917 15.5 2.95 15.5 3.5C15.5 4.05 15.3042 4.52083 14.9125 4.9125C14.5208 5.30417 14.05 5.5 13.5 5.5C12.95 5.5 12.4792 5.30417 12.0875 4.9125Z" fill="#C3C3C3"/>
											</g>
										  </svg>
										<input
											type="email"
											class="subscribe-section__input tour-checkout-form__input"
											id="tour-checkout-email"
											name="email"
											placeholder="Ваш найкращий e-mail"
											autocomplete="email"
											required
										>
									</div>
								</div>

								<div class="tour-checkout-form__field subscribe-section__field">
									<label class="screen-reader-text" for="tour-checkout-date">Дата</label>
									<div class="tour-checkout-form__control">
										<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
											<mask id="mask0_14_734" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="24">
											  <rect width="24" height="24" fill="#D9D9D9"/>
											</mask>
											<g mask="url(#mask0_14_734)">
											  <path d="M5.30775 21.5C4.80258 21.5 4.375 21.325 4.025 20.975C3.675 20.625 3.5 20.1974 3.5 19.6923V6.30777C3.5 5.8026 3.675 5.37502 4.025 5.02502C4.375 4.67502 4.80258 4.50002 5.30775 4.50002H6.69225V2.38477H8.23075V4.50002H15.8077V2.38477H17.3077V4.50002H18.6923C19.1974 4.50002 19.625 4.67502 19.975 5.02502C20.325 5.37502 20.5 5.8026 20.5 6.30777V19.6923C20.5 20.1974 20.325 20.625 19.975 20.975C19.625 21.325 19.1974 21.5 18.6923 21.5H5.30775ZM5.30775 20H18.6923C18.7692 20 18.8398 19.9679 18.9038 19.9038C18.9679 19.8398 19 19.7693 19 19.6923V10.3078H5V19.6923C5 19.7693 5.03208 19.8398 5.09625 19.9038C5.16025 19.9679 5.23075 20 5.30775 20ZM5 8.80777H19V6.30777C19 6.23077 18.9679 6.16026 18.9038 6.09626C18.8398 6.0321 18.7692 6.00002 18.6923 6.00002H5.30775C5.23075 6.00002 5.16025 6.0321 5.09625 6.09626C5.03208 6.16026 5 6.23077 5 6.30777V8.80777ZM12 14.077C11.7552 14.077 11.5465 13.9908 11.374 13.8183C11.2017 13.6459 11.1155 13.4373 11.1155 13.1923C11.1155 12.9474 11.2017 12.7388 11.374 12.5663C11.5465 12.3939 11.7552 12.3078 12 12.3078C12.2448 12.3078 12.4535 12.3939 12.626 12.5663C12.7983 12.7388 12.8845 12.9474 12.8845 13.1923C12.8845 13.4373 12.7983 13.6459 12.626 13.8183C12.4535 13.9908 12.2448 14.077 12 14.077ZM7.374 13.8183C7.20167 13.6459 7.1155 13.4373 7.1155 13.1923C7.1155 12.9474 7.20167 12.7388 7.374 12.5663C7.5465 12.3939 7.75517 12.3078 8 12.3078C8.24483 12.3078 8.4535 12.3939 8.626 12.5663C8.79833 12.7388 8.8845 12.9474 8.8845 13.1923C8.8845 13.4373 8.79833 13.6459 8.626 13.8183C8.4535 13.9908 8.24483 14.077 8 14.077C7.75517 14.077 7.5465 13.9908 7.374 13.8183ZM16 14.077C15.7552 14.077 15.5465 13.9908 15.374 13.8183C15.2017 13.6459 15.1155 13.4373 15.1155 13.1923C15.1155 12.9474 15.2017 12.7388 15.374 12.5663C15.5465 12.3939 15.7552 12.3078 16 12.3078C16.2448 12.3078 16.4535 12.3939 16.626 12.5663C16.7983 12.7388 16.8845 12.9474 16.8845 13.1923C16.8845 13.4373 16.7983 13.6459 16.626 13.8183C16.4535 13.9908 16.2448 14.077 16 14.077ZM12 18C11.7552 18 11.5465 17.9138 11.374 17.7413C11.2017 17.5689 11.1155 17.3603 11.1155 17.1155C11.1155 16.8705 11.2017 16.6618 11.374 16.4895C11.5465 16.317 11.7552 16.2308 12 16.2308C12.2448 16.2308 12.4535 16.317 12.626 16.4895C12.7983 16.6618 12.8845 16.8705 12.8845 17.1155C12.8845 17.3603 12.7983 17.5689 12.626 17.7413C12.4535 17.9138 12.2448 18 12 18ZM7.374 17.7413C7.20167 17.5689 7.1155 17.3603 7.1155 17.1155C7.1155 16.8705 7.20167 16.6618 7.374 16.4895C7.5465 16.317 7.75517 16.2308 8 16.2308C8.24483 16.2308 8.4535 16.317 8.626 16.4895C8.79833 16.6618 8.8845 16.8705 8.8845 17.1155C8.8845 17.3603 8.79833 17.5689 8.626 17.7413C8.4535 17.9138 8.24483 18 8 18C7.75517 18 7.5465 17.9138 7.374 17.7413ZM16 18C15.7552 18 15.5465 17.9138 15.374 17.7413C15.2017 17.5689 15.1155 17.3603 15.1155 17.1155C15.1155 16.8705 15.2017 16.6618 15.374 16.4895C15.5465 16.317 15.7552 16.2308 16 16.2308C16.2448 16.2308 16.4535 16.317 16.626 16.4895C16.7983 16.6618 16.8845 16.8705 16.8845 17.1155C16.8845 17.3603 16.7983 17.5689 16.626 17.7413C16.4535 17.9138 16.2448 18 16 18Z" fill="#C3C3C3"/>
											</g>
										  </svg>
										<input
											type="text"
											class="subscribe-section__input tour-checkout-form__input tour-checkout-form__input--date"
											id="tour-checkout-date"
											name="date"
											placeholder="Оберіть дату"
											required
										>
									</div>
								</div>
							</div>

							<div class="subscribe-section__message tour-checkout-form__message" role="status" aria-live="polite"></div>

							<button type="submit" class="btn btn--primary tour-checkout-form__submit" id="tour-checkout-submit">
								<span class="subscribe-section__btn-text">Створити мій день</span>
								<svg class="btn__icon tour-checkout-form__btn-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 15 11.3075" fill="none" aria-hidden="true"><path d="M9.34625 11.3075L8.29225 10.223L12.1115 6.40375H0V4.90375H12.1115L8.29225 1.0845L9.34625 0L15 5.65375L9.34625 11.3075Z" fill="#FFFFFF"/></svg>
							</button>
						</form>
					</div>
				</div>
			</aside>
		</div>
	</div>
</section>
<section class="subscribe-section animate-on-scroll" id="subscribe" aria-labelledby="subscribe-title">
	<header class="subscribe-section__header">
		<p class="subscribe-section__subtitle">subscribe</p>
		<h2 class="subscribe-section__title" id="subscribe-title">Підпишіться на розсилку</h2>
	</header>

	<p class="subscribe-section__desc">Отримуйте актуальні пропозиції, новини та корисні поради про подорожі на узбережжя Амальфі.</p>

	<form class="subscribe-section__form" action="#" method="post" novalidate>
		<div class="subscribe-section__field">
			<label class="screen-reader-text" for="subscribe-email">Email</label>
			<input
				type="email"
				class="subscribe-section__input"
				id="subscribe-email"
				name="email"
				placeholder="info@gmail.com"
				autocomplete="email"
				required
			>
			<div class="subscribe-section__message" role="status" aria-live="polite"></div>
		</div>
		<button type="submit" class="btn btn--primary" id="subscribe-submit">
			<span class="subscribe-section__btn-text">Підписатися</span>
			<svg class="btn__icon" xmlns="http://www.w3.org/2000/svg" width="13" height="10" viewBox="0 0 13 10" fill="none" aria-hidden="true"><path d="M7.78854 9.42292L6.91021 8.51917L10.0929 5.33646H0V4.08646H10.0929L6.91021 0.90375L7.78854 0L12.5 4.71146L7.78854 9.42292Z" /></svg>
		</button>
	</form>
</section>

<?php endwhile; ?>

<?php block_template_part( 'footer' ); ?>
<?php wp_footer(); ?>
</body>
</html>
