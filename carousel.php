<script>
// debounce from underscore.js
function debounce(func, wait, immediate) {
	var timeout;
	return function() {
		var context = this, args = arguments;
		var later = function() {
			timeout = null;
			if (!immediate) func.apply(context, args);
		};
		var callNow = immediate && !timeout;
		clearTimeout(timeout);
		timeout = setTimeout(later, wait);
		if (callNow) func.apply(context, args);
	};
};

// use x and y mousewheel event data to navigate flickity
function slick_handle_wheel_event(e, slick_instance, slick_is_animating) {
  // do not trigger a slide change if another is being animated
  if (!slick_is_animating) {
    // pick the larger of the two delta magnitudes (x or y) to determine nav direction
    var direction =
      Math.abs(e.deltaX) > Math.abs(e.deltaY) ? e.deltaX : e.deltaY;

    console.log("wheel scroll ", e.deltaX, e.deltaY, direction);

    if (direction > 0) {
      // next slide
      slick_instance.slick("slickNext");
    } else {
      // prev slide
      slick_instance.slick("slickPrev");
    }
  }
}

// debounce the wheel event handling since trackpads can have a lot of inertia
var slick_handle_wheel_event_debounced = debounce( 
  slick_handle_wheel_event
  , 100, true
);

// init slider 
const slick_2 = jQuery(".slides");
slick_2.slick({
  dots: false,
  vertical: true,
  slidesToShow: 1,
  // verticalSwiping: true,
  arrows: false,
// 	centerMode: true,
// 	centerPadding: '200px',
});
var slick_2_is_animating = false;

slick_2.on("afterChange", function(index) {
  console.log("Slide after change " + index);
  slick_2_is_animating = false;
	 jQuery('.slick-active').each(function() {
		 var activeClass = jQuery(this).data('ref');
		 jQuery('.'+activeClass).addClass('show');
		 jQuery('.'+activeClass).siblings().removeClass('show');
		 jQuery('.'+activeClass).siblings().addClass('hide');
      });
		
});

slick_2.on("beforeChange", function(index) {
//   console.log("Slide before change " + index);
  slick_2_is_animating = true;
	jQuery('iframe').each(function(index) {
        jQuery(this).attr('src', jQuery(this).attr('src'));
        return false;
      });
});

slick_2.on("wheel", function(e) {
  slick_handle_wheel_event_debounced(e.originalEvent, slick_2, slick_2_is_animating);  
});
jQuery().ready(function(){
jQuery('.gallery-view h2').click(function(){
    jQuery('.cstm-shortcode').hide();
    jQuery('.custom-postss').show();
	jQuery('.list-view').show();
	jQuery('.gallery-view').hide();
	jQuery('.recent').show();
	jQuery('.recent-rotate').hide();
	
});
jQuery('.list-view h3').click(function(){
    jQuery('.custom-postss').hide();
    jQuery('.cstm-shortcode').show();
	jQuery('.list-view').hide();
	jQuery('.gallery-view').show();
	jQuery('.recent').hide();
	jQuery('.recent-rotate').show();
});
});
</script>
<?php
}
add_action( 'wp_footer', 'myscript' );



function wpb_catlist() { 
    $_terms = get_terms( array('category') );
    echo '<div class="main_container">';
	echo '<div class="videobox">';
    foreach ($_terms as $term) :
        $term_slug = $term->slug;
        $_posts = new WP_Query( array(
                    'post_type'         => 'post',
                    'posts_per_page'    => 10, //important for a PHP memory limit warning
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'category',
                            'field'    => 'slug',
                            'terms'    => $term_slug,
                        ),
                    ),
                ));
   		
        if( $_posts->have_posts() ) :
			$count = 1;
            while ( $_posts->have_posts() ) : $_posts->the_post();
            ?>
								<div class = "hide video<?php echo $count ?>"><?php the_field('youtube-video'); ?></div>        
            <?php
	$count++;
            endwhile;

           
    
        endif;
        wp_reset_postdata();
    
    endforeach;
	echo '</div>';
	echo '<div class="container">';
	echo '<div class="slides">';
    foreach ($_terms as $term) :
        $term_slug = $term->slug;
        $_posts = new WP_Query( array(
                    'post_type'         => 'post',
                    'posts_per_page'    => 10, //important for a PHP memory limit warning
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'category',
                            'field'    => 'slug',
                            'terms'    => $term_slug,
                        ),
                    ),
                ));
   		
        if( $_posts->have_posts() ) :
			$count = 1;
            while ( $_posts->have_posts() ) : $_posts->the_post();
            ?>
                            	
									<div class="slide" data-ref="video<?php echo $count ?>">
										<div class="box">
											<p class = "date"><?php echo get_the_date();?></p>
											<h2><?php echo get_the_title(); ?></h2>
											<h3><?php echo $term->name ?></h3>
											<p class = "content"><?php echo get_the_content(); ?></p>
											<div class="youtube-link"><a class="watch-btn" href ='<?php the_field('youtube_link'); ?>' target = "_blank"><i class='fas fa-external-link-alt'></i>Watch on YouTube </a></div>
										</div>
									</div>         
            <?php
	$count++;
            endwhile;

           
    
        endif;
        wp_reset_postdata();
    
    endforeach;
	 echo '</div>';
	 echo '</div>';
	 echo '</div>';
	
    }
    add_shortcode('category', 'wpb_catlist');
