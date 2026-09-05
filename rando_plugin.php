<?php
/*
	Plugin Name: Rando writer
	Plugin URI: https://blog.elkami.fr/
	Description: Un plugin me permettant de générer mes articles de randonnée plus rapidement.
	Version: 3.0
	Requires at least: 7.1
	Requires PHP: 8.0
	License: LGPL
	Author: Elkami
	Author URI: https://blog.elkami.fr/
*/

function rando_writer_box()
{
	$screens = array('post');
	foreach ( $screens as $screen ) {
		add_meta_box(
			'rando_writer_custom',
			'Rando writer',
			'rando_writer_input',
			$screen
		);
	}
}

function rando_writer_content(string $content)
{
	$result = '';

	if (is_single())
	{
		$monte_rando_writer    = get_post_meta(get_the_ID(),'_rando_writer_monte',true);
		$horaire_rando_writer  = get_post_meta(get_the_ID(),'_rando_writer_horaire',true);
		$niveau_rando_writer   = get_post_meta(get_the_ID(),'_rando_writer_niveau',true);
		$parcours_rando_writer = get_post_meta(get_the_ID(),'_rando_writer_parcours',true);
		$hyper_rando_writer    = get_post_meta(get_the_ID(),'_rando_writer_hyper',true);

		if(!empty($parcours_rando_writer) && is_single() && empty($hyper_rando_writer) )
		{
			$parcours_relative_path = parse_url($parcours_rando_writer, PHP_URL_PATH);
			$gpx_file = dirname($parcours_relative_path) . "/parcours.gpx";

			if (!empty($parcours_relative_path) && @file_exists(ABSPATH . $parcours_relative_path))
			{
				$result .=  '<div align="center">';
				$result .=  '<div class="top-bar" style="display: inline-block; padding: 5px; border-radius: 5px;">';
				$result .=  '<div style="display: inline-block; padding: 1px; border-radius: 5px; background-color: #ffffff; border: 2px solid #000000;"><a style="border-bottom: medium none;" title="Carte" href="'.$parcours_rando_writer.'" rel="nofollow" target="_blank"><img src="/wp-content/plugins/rando_wp/assets/img/ico-carte.webp" alt="La carte"/></a></div>&nbsp;';
				$result .=  '<div style="display: inline-block; padding: 1px; border-radius: 5px; background-color: #ffffff; border: 2px solid #000000;"><a style="border-bottom: medium none;" title="Trace GPS" href="'.$gpx_file.'" rel="nofollow" download><img src="/wp-content/plugins/rando_wp/assets/img/ico-gpx.webp" alt="Le parcours gpx"/></a></div>&nbsp;';
				$result .=  '</div>';
				$result .=  '</div>';
				$result .=  '</br>';
			}
		}
		$content = $result . $content;
	}
	return $content;
}

/**
 * Ancienne méthode par filtre get_the_date : conservée pour les thèmes
 * autres que Neve, où le filtre fonctionne correctement.
 */
function rando_writer_date(string|int $the_date, string $format, WP_Post $post)
{
	$monte_rando_writer    = get_post_meta($post->ID,'_rando_writer_monte',true);
	$horaire_rando_writer  = get_post_meta($post->ID,'_rando_writer_horaire',true);
	$niveau_rando_writer   = get_post_meta($post->ID,'_rando_writer_niveau',true);
	$parcours_rando_writer = get_post_meta($post->ID,'_rando_writer_parcours',true);
	$hyper_rando_writer    = get_post_meta($post->ID,'_rando_writer_hyper',true);

	if(empty($hyper_rando_writer) && !empty($parcours_rando_writer))
	{
		$the_date .= ' - ' . $monte_rando_writer;
		$the_date .= ' - ' . $horaire_rando_writer;
		$the_date .= ' - ' . $niveau_rando_writer;
	}

	return $the_date;
}

/**
 * Méthode dédiée au thème Neve : on ajoute nous-mêmes une ligne
 * "date + infos rando" et on masque celle générée par le thème,
 * sans jamais modifier les fichiers du thème.
 */
function rando_writer_render_date_with_info( $order, $as_list = true, $post_id = null ) {

	$pid = $post_id ? $post_id : get_the_ID();

	// On construit nous-mêmes la balise <time> avec uniquement la date de
	// publication (on n'utilise pas get_time_tags() du thème, qui ajoute
	// aussi la date de dernière modification à la suite).
	$created     = get_the_time( 'U', $pid );
	$date_format = get_option( 'date_format' );
	$date_markup = '<time class="entry-date published" datetime="' . esc_attr( date_i18n( 'c', $created ) ) . '">'
		. esc_html( date_i18n( $date_format, $created ) )
		. '</time>';

	$hyper    = get_post_meta( $pid, '_rando_writer_hyper', true );
	$parcours = get_post_meta( $pid, '_rando_writer_parcours', true );

	$parts = array( $date_markup );

	if ( empty( $hyper ) && ! empty( $parcours ) ) {
		$parts[] = esc_html( get_post_meta( $pid, '_rando_writer_monte', true ) );
		$parts[] = esc_html( get_post_meta( $pid, '_rando_writer_horaire', true ) );
		$parts[] = esc_html( get_post_meta( $pid, '_rando_writer_niveau', true ) );
	}

	// Toujours un <span> (jamais un <li>) : notre élément est ajouté
	// en dehors du <ul> du thème, un <li> isolé afficherait une puce.
	printf(
		'<span class="meta date posted-on rando-date-item">%s</span>',
		implode( ' - ', $parts )
	);
}

function rando_writer_hide_theme_date() {
	?>
	<style id="rando-writer-hide-theme-date">
		/* Cache la date native du thème */
		.nv-meta-list .meta.date:not(.rando-date-item) {
			display: none !important;
		}
		/* Si la date native était le seul élément de la liste, on cache
		   la liste entière : sinon son padding/margin/gap propre reste
		   visible même vide, ce qui crée un espace vide en trop. */
		.nv-meta-list:has(> .meta.date:not(.rando-date-item):only-child) {
			display: none !important;
		}
		.rando-date-item {
			display: inline-block;
			list-style: none;
			font-size: 0.85em;
			color: inherit;
			opacity: 0.85;
			margin: 0 0 1em 0 !important;
			padding: 0 !important;
		}
	</style>
	<?php
}

if (is_admin()) {
	add_action('admin_menu', 'rando_writer_box');
} else {
	add_filter('the_content', 'rando_writer_content');

	$my_theme = wp_get_theme( 'neve' );
	if ( $my_theme->exists() ) {
		// Thème Neve : notre propre date + infos, date native masquée en CSS.
		add_action( 'neve_post_meta_single', 'rando_writer_render_date_with_info', 20, 3 );
		add_action( 'neve_post_meta_archive', 'rando_writer_render_date_with_info', 20, 3 );
		add_action( 'wp_head', 'rando_writer_hide_theme_date' );
	} else {
		// Autres thèmes : ancienne méthode par filtre.
		add_filter('get_the_date', 'rando_writer_date', 10, 3);
	}
}

function rando_writer_input()
{
	global $post;

	$monte_rando_writer = get_post_meta($post->ID,'_rando_writer_monte',true);
	if(empty($monte_rando_writer)) $monte_rando_writer = "???m";

	$horaire_rando_writer = get_post_meta($post->ID,'_rando_writer_horaire',true);
	if(empty($horaire_rando_writer)) $horaire_rando_writer = "?H??";

	$niveau_rando_writer = get_post_meta($post->ID,'_rando_writer_niveau',true);
	if(empty($niveau_rando_writer)) $niveau_rando_writer = "T2";

	$parcours_rando_writer = get_post_meta($post->ID,'_rando_writer_parcours',true);
	$hyper_rando_writer = get_post_meta($post->ID,'_rando_writer_hyper',true);

	echo '<fieldset id="rando_writer-post">';
	?>
	<input type="hidden" name="rando_writer-verification" id="rando_writer-verification" value="<?php if(function_exists('wp_create_nonce')){ echo wp_create_nonce('rando_writer'); } ?>" />
	<?php
	echo '<table style="text-align: left; width: 100%;" border="0" cellpadding="0" cellspacing="4">';
	echo '<tbody>';
	echo '<tr>';
	echo '<td style="width: 91px;">Mont&eacute;e</td>';
	echo '<td style="width: 150px;"><input type="text" name="txt_rando_writer_montee" value="'. $monte_rando_writer . '" size="5"/></td>';
	echo '<td style="width: 120px;">Parcours</td>';
	echo '<td style="width: 150px;"><input type="text" name="txt_rando_writer_parcours" value="'. $parcours_rando_writer . '" size="70"/></td>';
	echo '<td style="width: 100px;">';
	echo '		<div>';
	if(empty($hyper_rando_writer))
		echo '			<input type="checkbox" name="chk_rando_writer_hyper"/>';
	else
		echo '			<input type="checkbox" name="chk_rando_writer_hyper" checked/>';
	echo '			<label for="chk_rando_writer_hyper">Surfr&eacute;quent&eacute;</label>';
	echo '		</div>';
	echo '	</td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td style="width: 91px;">Horaire</td>';
	echo '<td style="width: 74px;"><input type="text" name="txt_rando_writer_horaire" value="'. $horaire_rando_writer . '" size="5"/><br />';
	echo '        <span style="font-weight: bold;"></span></td>';
	echo '<td style="width: 66px;">Niveau</td>';
	echo '<td colspan="3" rowspan="1" style="width: 55px;"><input type="text" name="txt_rando_writer_niveau" value="'. $niveau_rando_writer . '" size="2"/>';
	echo '        </td>';
	echo '</tr>';
	echo '</tbody>';
	echo '</table>';
	echo '</fieldset>';
}

function rando_writer_update_post($id)
{
	if (current_user_can('edit_post', $id))
	{
		delete_post_meta($id, '_rando_writer_monte');
		add_post_meta($id, '_rando_writer_monte', $_POST["txt_rando_writer_montee"] ?? null);

		delete_post_meta($id, '_rando_writer_horaire');
		add_post_meta($id, '_rando_writer_horaire', $_POST["txt_rando_writer_horaire"] ?? null);

		delete_post_meta($id, '_rando_writer_niveau');
		add_post_meta($id, '_rando_writer_niveau', $_POST["txt_rando_writer_niveau"] ?? null);

		delete_post_meta($id, '_rando_writer_parcours');
		add_post_meta($id, '_rando_writer_parcours', $_POST["txt_rando_writer_parcours"] ?? null);

		delete_post_meta($id, '_rando_writer_difficulte');

		delete_post_meta($id, '_rando_writer_hyper');
		add_post_meta($id, '_rando_writer_hyper', $_POST["chk_rando_writer_hyper"] ?? null);
	}
}

add_action('save_post', 'rando_writer_update_post');
add_action('edit_post', 'rando_writer_update_post');
add_action('publish_post', 'rando_writer_update_post');
