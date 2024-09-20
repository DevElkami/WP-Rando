<?php
/*
	Plugin Name: Rando writer
	Plugin URI: https://blog.elkami.fr/
	Description: Un plugin me permettant de générer mes articles de randonnée plus rapidement.
	Version: 2.0
	Requires at least: 5.2
	Requires PHP: 7.2
	License: LGPL
	Author: Elkami
	Author URI: https://blog.elkami.fr/
*/
	
function rando_writer_box() 
{    
	$screens = array('post'); //get_post_types();

    foreach ( $screens as $screen ) {
        add_meta_box(
            'rando_writer_custom',
			'Rando writer', // Title
			'rando_writer_input', // Callback function that will echo the box content
            $screen
        );
    }
}

function rando_writer_content(string $content)
{
    if (is_single()) 
	{        
		$monte_rando_writer = get_post_meta(get_the_ID(),'_rando_writer_monte',true);
		$horaire_rando_writer = get_post_meta(get_the_ID(),'_rando_writer_horaire',true);	
		$niveau_rando_writer = get_post_meta(get_the_ID(),'_rando_writer_niveau',true);	
      	$parcours_rando_writer = get_post_meta(get_the_ID(),'_rando_writer_parcours',true);
      	$hyper_rando_writer = get_post_meta(get_the_ID(),'_rando_writer_hyper',true);
      
      	if(!empty($parcours_rando_writer) && is_single() && empty($hyper_rando_writer) )
        {
            $parcours_relative_path = substr($parcours_rando_writer, strlen("https://blog.elkami.fr"));
            $gpx_file = dirname($parcours_relative_path) . "/parcours.gpx";

            if (@file_exists(getcwd() . $parcours_relative_path))
            {		
				$result = "";
				$result .=  '<div align="center">';                
				$result .=  '<div class="top-bar" style="display: inline-block; padding: 5px; border-radius: 5px;">';
                
				//$result .=  '<div style="display: inline-block; padding: 1px; border-radius: 5px; background-color: #ffffff; border: 2px solid #64a7d1;"><a style="border-bottom: medium none;" title="Dénivelé" href="https://blog.elkami.fr/a-propos/" rel="nofollow" target="_blank"><div style="width: 120px; height: 50px; text-align: center; vertical-align: middle; line-height: 50px;">Montée: '.$monte_rando_writer.'</div></a></div>&nbsp;';                
				//$result .=  '<div style="display: inline-block; padding: 1px; border-radius: 5px; background-color: #ffffff; border: 2px solid #64a7d1;"><a style="border-bottom: medium none;" title="Horaire" href="https://blog.elkami.fr/a-propos/" rel="nofollow" target="_blank"><div style="width: 120px; height: 50px; text-align: center; vertical-align: middle; line-height: 50px;">Durée: '.$horaire_rando_writer.'</div></a></div>&nbsp;';                
				//$result .=  '<div style="display: inline-block; padding: 1px; border-radius: 5px; background-color: #ffffff; border: 2px solid #64a7d1;"><a style="border-bottom: medium none;" title="Cotation" href="https://blog.elkami.fr/cotations/" rel="nofollow" target="_blank"><div style="width: 120px; height: 50px; text-align: center; vertical-align: middle; line-height: 50px;">Cotation: '.$niveau_rando_writer.'</div></a></div>&nbsp;';                
				$result .=  '<div style="display: inline-block; padding: 1px; border-radius: 5px; background-color: #ffffff; border: 2px solid #64a7d1;"><a style="border-bottom: medium none;" title="Carte" href="'.$parcours_rando_writer.'" rel="nofollow" target="_blank"><div style="width: 120px; height: 50px; text-align: center; vertical-align: middle; line-height: 50px;">La carte</div></a></div>&nbsp;';                
				$result .=  '<div style="display: inline-block; padding: 1px; border-radius: 5px; background-color: #ffffff; border: 2px solid #64a7d1;"><a style="border-bottom: medium none;" title="Trace GPS" href="'.$gpx_file.'" rel="nofollow" download><div style="width: 120px; height: 50px; text-align: center; vertical-align: middle; line-height: 50px;">La trace GPS</div></a></div>&nbsp;';

				$result .=  '</div>';
				$result .=  '</div>';
				$result .=  '</br>';
            }			
        }
        $content = $result . $content;
    }
    return $content;
}

function rando_writer_date(string|int $the_date, string $format, WP_Post $post)
{    
	$monte_rando_writer = get_post_meta(get_the_ID(),'_rando_writer_monte',true);
	$horaire_rando_writer = get_post_meta(get_the_ID(),'_rando_writer_horaire',true);	
	$niveau_rando_writer = get_post_meta(get_the_ID(),'_rando_writer_niveau',true);	
    $parcours_rando_writer = get_post_meta(get_the_ID(),'_rando_writer_parcours',true);
    $hyper_rando_writer = get_post_meta(get_the_ID(),'_rando_writer_hyper',true);
      
    if(empty($hyper_rando_writer) && !empty($parcours_rando_writer))
    {
		$result = " - ";
		$result .=  ''.$monte_rando_writer.'';
		$result .=  ' - ';
        $result .=  ''.$horaire_rando_writer.'';
		$result .=  ' - ';
        $result .=  ''.$niveau_rando_writer.'';
			
		$the_date = $the_date . $result;
	}	

    return $the_date;
}

if (is_admin())
    add_action('admin_menu', 'rando_writer_box');
else
{
	// Insert my meta in the content
	add_filter('the_content', 'rando_writer_content');

	// Insert some meta after the date / time field (not working in some theme)
	add_filter('get_the_date', 'rando_writer_date', 10, 3);
	add_filter('get_the_modified_date', 'rando_writer_date', 10, 3);
	add_filter('get_the_time', 'rando_writer_date', 10, 3);
	add_filter('get_the_modified_time', 'rando_writer_date', 10, 3);

	// Pour le thème neve, il faut modifier post_meta.php (inc/views/partials/post_meta.php - function render_meta_list)
	// car les créateurs force le formatage de la date empêchant ainsi toutes insertions d'info à la suite
	// Peut être faudrait-il voir les custom metadata: méthode plus générique évitant de modifier le thème
}

/* Set up the rating entry for the admin area */
function rando_writer_input() 
{
	global $post;	
	
	// Montée cumulée
	$monte_rando_writer = get_post_meta($post->ID,'_rando_writer_monte',true);	
	if(empty($monte_rando_writer))
		$monte_rando_writer = "???m";
	
	// Horaire
	$horaire_rando_writer = get_post_meta($post->ID,'_rando_writer_horaire',true);		
	if(empty($horaire_rando_writer))
		$horaire_rando_writer = "?H??";
	
	// Niveau
	$niveau_rando_writer = get_post_meta($post->ID,'_rando_writer_niveau',true);
	if(empty($niveau_rando_writer))
		$niveau_rando_writer = "T2";
	
	// Parcours
	$parcours_rando_writer = get_post_meta($post->ID,'_rando_writer_parcours',true);
	
	// Surfréquentation
	$hyper_rando_writer = get_post_meta($post->ID,'_rando_writer_hyper',true);

	// Affichage des champs	
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
		$setting = (isset($_POST["txt_rando_writer_montee"])) ? $_POST["txt_rando_writer_montee"] : null;
		add_post_meta($id, '_rando_writer_monte', $setting);
			
		delete_post_meta($id, '_rando_writer_horaire');
		$setting = (isset($_POST["txt_rando_writer_horaire"])) ? $_POST["txt_rando_writer_horaire"] : null;
		add_post_meta($id, '_rando_writer_horaire', $setting);
			
		delete_post_meta($id, '_rando_writer_niveau');
		$setting = (isset($_POST["txt_rando_writer_niveau"])) ? $_POST["txt_rando_writer_niveau"] : null;
		add_post_meta($id, '_rando_writer_niveau', $setting);
			
		delete_post_meta($id, '_rando_writer_parcours');
		$setting = (isset($_POST["txt_rando_writer_parcours"])) ? $_POST["txt_rando_writer_parcours"] : null;
		add_post_meta($id, '_rando_writer_parcours', $setting);
			
		delete_post_meta($id, '_rando_writer_difficulte');
			
		delete_post_meta($id, '_rando_writer_hyper');
		$setting = (isset($_POST["chk_rando_writer_hyper"])) ? $_POST["chk_rando_writer_hyper"] : null;
		add_post_meta($id, '_rando_writer_hyper', $setting);
	}
}

add_action('save_post', 'rando_writer_update_post');
add_action('edit_post', 'rando_writer_update_post');
add_action('publish_post', 'rando_writer_update_post');

?>