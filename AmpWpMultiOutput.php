<?php
namespace WpMultiOutput;
/*
Plugin Name: AMP WPMO
Plugin URI:  http://www.github.com/chrishutchinson/wp-multi-outputs
Description: Framework for supporting multiple content outputs
Version:     0.0.1
Author:      Chris Hutchinson
Author URI:  http://www.github.com/chrishutchinson
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: amp-wpmo
*/

// Include libraries
include( dirname( __FILE__ ) . '/lib/amp-wp/class-amp-post.php' );

class AmpWpMultiOutput extends WpMultiOutput {

	public $configuration;

	function __construct( $post = null )
	{
		$this->configuration = [
			'slug' => 'amp',
			'name' => 'AMP',
			'description' => 'Converts a WordPress article into AMP HTML format.',
			'action' => 'View AMP version',
			'type' => 'render'
 		];

 		// Actions
 		add_action( 'template_redirect', [ $this, 'prepareOutput' ] );

		// Don't delete this!
    	parent::__construct( $post );
    }

	function prepare()
	{
		$this->ampPost = $this->getAmpPost($this->post->ID);
	}
	
	function publish()
	{
		$amp_post = $this->ampPost;

		ob_start();
		include( dirname( __FILE__ ) . '/template.php' );
		$this->html = ob_get_contents();
    	ob_end_clean();
	}
	
	function teardown()
	{	
		$permalink = get_permalink( $this->post->ID ) . '?wpmo_format=amp';
		return $permalink;
	}

	private function getAmpPost( $id )
	{
		return new \Amp_Post( $id );
	}

	function prepareOutput() {
		global $post;
		$this->post = $post;

		if( isset( $_REQUEST['wpmo_format'] ) && $_REQUEST['wpmo_format'] === 'amp' ) {
			$this->prepare();
			$this->publish();
			echo $this->html;
			die();
		}
	}

}

$AmpWpMultiOutput = new AmpWpMultiOutput();