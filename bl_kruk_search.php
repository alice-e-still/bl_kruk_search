<?php
/**
 * @package bl_kruk_conditions_symptoms_search
 * @version 1.0.0.1
 */

/*
Plugin Name: BoldLight KRUK Conditions and Symptoms Search
Version: 1.0.0.1
Description: Plugin to provide conditions and symptoms search functionality. [conditions_and_symptoms_search placeholder_text=""]
Author: BoldLight
*/

add_action( 'wp_enqueue_scripts', 'bl_kruk_search_enqueue_script' );
function bl_kruk_search_enqueue_script() {
		wp_enqueue_script( 'bl_kruk_jquery-1.12.4.js', 'https://code.jquery.com/jquery-1.12.4.js', false );
		wp_enqueue_script( 'bl_kruk_jquery-ui.js', 'https://code.jquery.com/ui/1.12.1/jquery-ui.js', false );
		wp_enqueue_style( 'bl_kruk_jquery-ui.css', 'http://code.jquery.com/ui/1.9.1/themes/base/jquery-ui.css');
}

//-- short code --//


//-- conditions-symptoms --//
//-- make this such that the post type can be passed as a short code parameter--//
add_shortcode('conditions_and_symptoms_search','display_conditions_and_symptoms_search');

function display_conditions_and_symptoms_search($atts){
	
	$placeholder_text = '...';
	if(isset($atts['placeholder_text'])){
		$placeholder_text = $atts['placeholder_text'];
	}
	
	if(isset($atts['post_type'])){
		$post_type = $atts['post_type'];
	}else{
		$post_type = 'conditions-symptoms';
	}
	
	global $post;
	
	
	ob_start();
?>
<div id="conditions-symptoms-search" class="boldlight-custom-search">
	<form autocomplete="nope">
	<div class="ui-widget input-group mb-3">
			<input id="conditions-symptoms" type="text" class="form-control" autocomplete="nope" placeholder="<?php echo $placeholder_text ;?>" aria-label="" aria-describedby="conditions-symptoms-find"/>
			
	</div>
	</form>
</div>
<?php
		
	$available_titles = "";
	
	
	$query_params = array( 'post_type'=>$post_type,'posts_per_page'=>-1, 'order'=>'ASC'); // return all posts - no paging
	$conditions_symptoms_query = new WP_Query;
	$conditions_symptoms_query->query($query_params);
	
	if($conditions_symptoms_query->have_posts()){
		while($conditions_symptoms_query->have_posts()){
		
			$conditions_symptoms_query->the_post();
			//-- $post magically is set at this point in The Loop! --//
		
			$session_name = $post->post_title;
			
			//$session_name_lwr = strtolower($post->post_title);
			
			$session_permalink = get_permalink();
			
			$available_titles .= ' { dest: "'.$session_permalink.'", label:"'.$session_name.'"}, ';
			//echo '<div class="search-result" data-title="'.$session_name_lwr.'"><a href="'.$session_permalink.'">'.$session_name.'</a></div>';
		}	
	
?>
<script>
	$( function() {
	
	//$(".search-result").hide();

    var availableTags = [
      <?php echo $available_titles; ?>
    ];
    
    $( "#conditions-symptoms" ).autocomplete({
      source: availableTags,
      minLength: 3,
      select: function( event, ui ) { 
            window.location.href = ui.item.dest;
        }
    });
    
  	$("#conditions-symptoms-find").click(function(event){
  		
  		$('#conditions-symptoms').blur();
  		event.preventDefault(); // do not submit form
  		
  		//-- button likely to be removed --//
  		/*$(".search-result").hide();
  		
  		search_name = $("#conditions-symptoms").val().toLowerCase();
  		
  		result_sets = $('div[data-title*="'+search_name+'"]');
  		
  		if(result_sets){
  			result_sets.show();
  		}
  		*/
  		$('#conditions-symptoms').focus(); 
  	});
  } );
	</script>
<?php	
		wp_reset_postdata();		
	}else{
		$output = ob_get_clean();
		return "";
	}



	//-- return output buffer --//
	$output = ob_get_clean();
	return $output;

}

?>