<?php
/***
 * hu.wordpress.org
 */

//$wp_default_secret_key = 'írj ide valami nagyon bonyolultat';
 
function magyar_dashboard()
{  
  $widget_options = get_option('dashboard_widget_options');
  
  //Ha az alap fejlesztői blog van, akkor átírjuk
  if (empty($widget_options['dashboard_primary']) || $widget_options['dashboard_primary']['link'] == 'http://wordpress.org/development/')
  {
    $widget_options['dashboard_primary'] = array(
    	'link' => 'http://napsugar.net/',
    	'url' => 'http://feeds.feedburner.com/idezetek/',
    	'title' => 'A napi lélekmelegítőd',
    	'items' => 1,
    	'show_summary' => 1,
    	'show_author' => 1,
    	'show_date' => 1
    );
  }
  
  //Ha az alap wordpress planet van, akkor is átírjuk
  if (empty($widget_options['dashboard_secondary']) || $widget_options['dashboard_secondary']['link'] == 'http://word-press.hu/' || $widget_options['dashboard_secondary']['link'] == 'http://planet.wordpress.org/')
  {
    $widget_options['dashboard_secondary'] = array(
    	'link' => 'http://wphu.org/',
    	'url' => 'http://wphu.org/feed/',
    	'title' => 'Magyar WordPress Hírek',
    	'items' => 4,
    	'show_summary' => 1,
    	'show_author' => 1,
    	'show_date' => 1
    );
  }
  
  update_option('dashboard_widget_options', $widget_options);
}

/**
 * Magyar WordPress widget
 *
 * Megjelenítjük a magyar WordPress közösség URL-ét
 *
 */
class HU_Widget_Meta extends WP_Widget_Meta 
{
	function widget( $args, $instance ) 
	{
		extract($args);
		$title = apply_filters('widget_title', empty($instance['title']) ? __('Meta') : $instance['title'], $instance, $this->id_base);

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;
?>
			<ul>
			<?php wp_register(); ?>
			<li><?php wp_loginout(); ?></li>
			<li><a href="<?php bloginfo('rss2_url'); ?>" title="<?php echo esc_attr(__('Syndicate this site using RSS 2.0')); ?>"><?php _e('Entries <abbr title="Really Simple Syndication">RSS</abbr>'); ?></a></li>
			<li><a href="<?php bloginfo('comments_rss2_url'); ?>" title="<?php echo esc_attr(__('The latest comments to all posts in RSS')); ?>"><?php _e('Comments <abbr title="Really Simple Syndication">RSS</abbr>'); ?></a></li>
			<li><a href="http://wphu.org/" title="<?php echo esc_attr(__('Powered by WordPress, state-of-the-art semantic personal publishing platform.')); ?>">Magyar WordPress közösség</a></li>
			<li><a href="http://forum.wpm.hu/" title="<?php echo esc_attr(__('Powered by WordPress, state-of-the-art semantic personal publishing platform.')); ?>">Magyar WordPress fórum</a></li>
			<?php wp_meta(); ?>
			</ul>
<?php
		echo $after_widget;
	}
}

function hu_widget_init()
{
	if ( !is_blog_installed() ) return;
	unregister_widget('WP_Widget_Meta');
	register_widget('HU_Widget_Meta');
}

function schema_hu() 
{
	add_option( 'start_of_week', 1 ); 
	add_option( 'rss_language',	'hu' );
	add_option( 'timezone_string',	'Europe/Budapest' );
}


add_action('widgets_init', 'hu_widget_init');
add_action('upgrader_post_install','magyar_dashboard');
add_action('wp_dashboard_setup','magyar_dashboard');
add_action( 'populate_options', 'schema_hu' );
