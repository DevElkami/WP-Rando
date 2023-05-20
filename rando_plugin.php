<?php
/*
	Plugin Name: Rando writer
	Plugin URI: https://blog.elkami.fr/
	Description: Un plugin me permettant de générer mes articles de randonnée plus rapidement.
	Version: 1.5
	License: LGPL
	Author: Elkami
	Author URI: https://blog.elkami.fr/
*/
	
function rando_writer_box() 
{
    add_meta_box(
        'ozh', // id of the <div> we'll add
        'Rando writer', //title
        'rando_writer_input', // callback function that will echo the box content
        'post' // where to add the box: on "post", "page", or "link" page
    );
}
 
// Hook things in, late enough so that add_meta_box() is defined
if (is_admin())
    add_action('admin_menu', 'rando_writer_box');

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
		if (function_exists('wp_verify_nonce'))
		{
			if (wp_verify_nonce($_POST['rando_writer-verification'], 'rando_writer'))
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
		else 
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
}

add_action('save_post', 'rando_writer_update_post');
add_action('edit_post', 'rando_writer_update_post');
add_action('publish_post', 'rando_writer_update_post');

?>
