<?php
/**
 * @package PriceTable
 */

namespace SanasanaInit\Seo;

class SeoOverrideController 
{
    public function register()
    {
		add_shortcode( 'video-testimonio', [$this, 'embed_video_testimonio']);
		add_shortcode( 'site-name', [$this, 'site_name']);
		add_action('wp_head', [$this, 'preload_fonts'], 1);
    }
	
	public function embed_video_testimonio(){
		ob_start(); ?>
		<div class="video-container">
			<img src="https://sanasanastoragews.blob.core.windows.net/blobwsdev/2025/06/Foto-testimonio.png" name="video-testimonio" id="video-testimonio">
			</div>
		<script>
			jQuery(document).ready(function(){
				console.log('ok load');
			});
		</script>
		<?php
		return ob_get_clean();
	}
	
	public function site_name() {
		return '<h1 class="site-name">
			' . __( "Salud como nunca antes",  "sanasana-home" )  . '
			
			</h1>
			<style>
				.site-name{
					color: #f9de42;
					font-family: \'Moranga Light\', Verdana, Arial, sans-serif;
					font-weight: 600;
					font-size: 48px;
					line-height: 48px;
					text-align: left;
					text-shadow: none;
				}
				@media(max-width: 768px){
					.site-name{
						font-size: 36px;
					}
					.fl-node-i0tb54ruh9a3.fl-row > .fl-row-content-wrap{
						width: 100%;
					}
				}
			</style>
			
			
			';
		
	}
	// functions.php
	public function preload_fonts() {
		// Usa tu ruta real al WOFF2
		$font = get_stylesheet_directory_uri() . '/fonts/Moranga-Light.woff2';
		echo '<link rel="preload" href="'.esc_url($font).'" as="font" type="font/woff2" crossorigin>';
	}


	
}
