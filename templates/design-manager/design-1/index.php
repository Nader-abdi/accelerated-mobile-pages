<?php use AMPforWP\AMPVendor\AMP_HTML_Utils;?>
<?php global $redux_builder_amp; 
		$is_full_content = false;
		if(isset($redux_builder_amp['ampforwp-full-post-in-loop']) && $redux_builder_amp['ampforwp-full-post-in-loop']){
			$is_full_content = true;
		} ?>
<!doctype html>
<html amp <?php echo AMP_HTML_Utils::build_attributes_string( $this->get( 'html_tag_attributes' ) ); ?>>
<head>
	<meta charset="utf-8">
	<?php do_action('amp_experiment_meta', $this); ?>
    <link rel="dns-prefetch" href="https://cdn.ampproject.org">
	<?php do_action( 'amp_post_template_head', $this ); ?>
	<style amp-custom>
		<?php $this->load_parts( array( 'style' ) ); ?>
		<?php do_action( 'amp_post_template_css', $this ); ?>
	</style>
</head>

<body <?php ampforwp_body_class('amp_home_body design_1_wrapper');?>>
<?php do_action('ampforwp_body_beginning', $this); ?>
<?php $this->load_parts( array( 'header-bar' ) ); ?>
<?php do_action( 'below_the_header_design_1', $this ); ?>


<?php do_action('ampforwp_home_above_loop') ?>

<article class="amp-wp-article ampforwp-custom-index amp-wp-home <?php if( $redux_builder_amp['ampforwp-full-post-in-loop'] == 1 ){ ?>full-post<?php } ?>">

	<?php do_action('ampforwp_post_before_loop') ?>
	
		<?php
			$count = 1;
			if ( get_query_var( 'paged' ) ) {
		        $paged = get_query_var('paged');
		    } elseif ( get_query_var( 'page' ) ) {
		        $paged = get_query_var('page');
		    } else {
		        $paged = 1;
		    }

		    $exclude_ids = get_option('ampforwp_exclude_post');

			$args = array(
				'post_type'           => 'post',
				'orderby'             => 'date',
				'paged'               => esc_attr($paged),
				'post__not_in' 		  => $exclude_ids,
                'has_password' => false ,
                'post_status'=> 'publish'
			);
			$filtered_args = apply_filters('ampforwp_query_args', $args);
			$q = new WP_Query( $filtered_args ); 
			$blog_title = ampforwp_get_blog_details('title');
			if( ampforwp_is_blog() && $blog_title){  ?>
				<h1 class="page-title"><?php echo $blog_title ?></h1>
			<?php }
			
				
			 if ( $q->have_posts() ) : while ( $q->have_posts() ) : $q->the_post(); ?>
		        <div class="amp-wp-content amp-wp-article-header amp-loop-list">
		        	<h1 class="amp-wp-title"><?php  $ampforwp_post_url = get_permalink(); ?><a href="<?php echo ampforwp_url_controller( $ampforwp_post_url ); ?>"><?php the_title() ?></a></h1>
					<?php 
						if( $is_full_content ){
							ampforwp_loop_full_content_featured_image();
						}
					?>
					<div class="amp-wp-content-loop">
						<?php if( $redux_builder_amp['ampforwp-full-post-in-loop'] == 0 ){ ?>
						<div class="amp-wp-meta">
			              <?php  $this->load_parts( apply_filters( 'amp_post_template_meta_parts', array( 'meta-author') ) );
			              global $redux_builder_amp;
			              		if($redux_builder_amp['amp-design-selector'] == '1' && $redux_builder_amp['amp-design-1-featured-time'] == '1'){
			               ?>
			              <time> <?php
                          		$post_date =  human_time_diff( get_the_time('U', get_the_ID() ), current_time('timestamp') ) .' '. ampforwp_translation( $redux_builder_amp['amp-translator-ago-date-text'],'ago' );
                   				 $post_date = apply_filters('ampforwp_modify_post_date',$post_date);
                    			echo  $post_date ;?>
                    		</time> <?php
			 		 		}
			 		 	if( isset($redux_builder_amp['ampforwp-design1-cats-home']) && $redux_builder_amp['ampforwp-design1-cats-home'] ) {
			 		 		foreach((get_the_category()) as $category) { ?>
			 		 		<ul class="amp-wp-tags">
					   			<li class="amp-cat-<?php echo $category->term_id;?>"> <?php echo  '  ' . $category->cat_name ?> </li> </ul>
							<?php }
						} ?>
						</div>
						<?php } ?>

						<?php if ( ampforwp_has_post_thumbnail() && !$is_full_content ) {
						$width = 100;
						$height = 75;
						if ( true == $redux_builder_amp['ampforwp-homepage-posts-image-modify-size'] ) {
							$width = $redux_builder_amp['ampforwp-homepage-posts-design-1-2-width'];
							$height = $redux_builder_amp['ampforwp-homepage-posts-design-1-2-height'];
						}
						$image_args = array("tag"=>'div',"tag_class"=>'home-post-image','image_size'=>'full','image_crop'=>'true','image_crop_width'=>$width,'image_crop_height'=>$height); ?>
								<?php amp_loop_image($image_args); ?>
							<?php }
														
							if(has_excerpt()){
								$content = get_the_excerpt();
							}else{
								$content = get_the_content();
							} ?>
						<p><?php global $redux_builder_amp;
							if( ampforwp_check_excerpt() && !$is_full_content ) {
								$excerpt_length = $redux_builder_amp['amp-design-1-excerpt'];
								$final_content = ""; 					
								$final_content  = apply_filters('ampforwp_modify_index_content', $content,  $excerpt_length );

								if ( false === has_filter('ampforwp_modify_index_content' ) ) {
									$final_content = wp_trim_words( strip_shortcodes( $content ) ,  $excerpt_length );
								}
								echo $final_content;
							}?></p>
							<?php
							if($is_full_content){
					ob_start();
					the_content();
					$content = ob_get_clean();
		            $sanitizer_obj = new AMPFORWP_Content( $content,
		                  array(
          				    'AMP_Twitter_Embed_Handler'     => array(),
          				    'AMP_YouTube_Embed_Handler'     => array(),
			                  'AMP_DailyMotion_Embed_Handler' => array(),
			                  'AMP_Vimeo_Embed_Handler'       => array(),
			                  'AMP_SoundCloud_Embed_Handler'  => array(),
          				    'AMP_Instagram_Embed_Handler'   => array(),
          				    'AMP_Vine_Embed_Handler'        => array(),
          				    'AMP_Facebook_Embed_Handler'    => array(),
			                  'AMP_Pinterest_Embed_Handler'   => array(),
          				    'AMP_Gallery_Embed_Handler'     => array(),
              			), 
		                  apply_filters( 'ampforwp_content_sanitizers', 
		                    array( 'AMP_Img_Sanitizer' => array(), 
		                      'AMP_Blacklist_Sanitizer' => array(),
		                      'AMP_Style_Sanitizer' => array(), 
		                      'AMP_Video_Sanitizer' => array(),
		                       'AMP_Audio_Sanitizer' => array(),
		                       'AMP_Iframe_Sanitizer' => array(
		                         'add_placeholder' => true,
		                       ),
		                    ) 
		                  ) 
		                );
		        		$content =  $sanitizer_obj->get_amp_content();
		        	$final_content = apply_filters( 'ampforwp_loop_content', $content );
		        	echo $final_content;
				}?>
					</div>
		        </div>
		         <?php 
		         do_action('ampforwp_between_loop',$count,$this);
		         $count++;
		    	
		    endwhile;  ?>
		    <?php do_action('ampforwp_loop_before_pagination') ?>

		    <div class="amp-wp-content pagination-holder">

		        <div id="pagination">
		            <div class="next"><?php echo apply_filters('ampforwp_next_posts_link', get_next_posts_link( ampforwp_translation($redux_builder_amp['amp-translator-next-text'], 'Next' ).'&raquo;', 0), $paged); ?></div>
		            <div class="prev"><?php echo apply_filters( 'ampforwp_previous_posts_link', get_previous_posts_link( '&laquo; '. ampforwp_translation($redux_builder_amp['amp-translator-previous-text'], 'Previous' )), $paged ); ?></div>
		            <div class="clearfix"></div>
		        </div>

		    </div>

		<?php endif; ?>

	<?php do_action('ampforwp_post_after_loop') ?>

</article>

<?php do_action('ampforwp_home_below_loop') ?>
<?php do_action( 'amp_post_template_above_footer', $this ); ?>
<?php $this->load_parts( array( 'footer' ) ); ?>
<?php do_action( 'amp_post_template_footer', $this ); ?>

</body>
</html>
