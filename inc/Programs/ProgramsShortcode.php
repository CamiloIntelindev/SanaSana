<?php
/**
 * @package Programs
 */

namespace SanasanaInit\Programs;

use SanasanaInit\General\BaseController;
use WP_Query;

class ProgramsShortcode extends BaseController

{
    public function register()
    {
        add_action('init', [$this, 'load_my_textdomain']);
        add_action('plugins_loaded', [$this, 'load_plugin_textdomain_custom_fonts']);
        add_shortcode('price_table', [$this, 'render_programs']);
		add_shortcode('toggle_button', [$this, 'render_toggle_button']);
        add_shortcode('price_table_cards', [$this, 'render_programs_cards']);
		add_shortcode('price_table_cards_nosotros', [$this, 'render_programs_cards_nosotros']);
		
		add_shortcode('price_table_details', [$this, 'render_programs_details']);
		//Single price table benefits
		add_shortcode('get_program_details', [$this, 'render_program_benefits']);
		//Ahorros Section
		add_shortcode('get_render_program_ahorros', [$this, 'render_program_ahorros']);
		
    }

    function load_plugin_textdomain_custom_fonts() {
        load_plugin_textdomain('custom-fonts', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    function load_my_textdomain() {
        load_theme_textdomain('custom-fonts', get_template_directory() . '/languages');
    }

	public function render_programs(){
		$args = array(
			'post_type'      => 'programa',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		);

		$query = new \WP_Query($args);

		if (!$query->have_posts()) {
			return '<p>' . esc_html__('No price plans available.', 'pricetable') . '</p>';
		}

		ob_start(); ?>

		<div class="price-annual-button">
			<div class="toggle-container">
				<span class="toggle-label"><?php _e('Mensual', 'pricetable'); ?></span>
				<div class="toggle" id="toggleSwitch">
					<div class="slider"></div>
				</div>
				<span class="toggle-label"><?php _e('Anual', 'pricetable'); ?></span>
			</div>
		</div>

		<div class="price-slider-container">
			<div class="price-prev"></div>
			<div class="price-container">
				<?php while ($query->have_posts()) : $query->the_post();
					$visible = get_post_meta(get_the_ID(), '_price_table_visible', true);
					$title = get_the_title();
					$description = get_post_meta(get_the_ID(), '_price_table_description', true);
					$price = get_post_meta(get_the_ID(), '_price_table_price', true);
					$annual_text = get_post_meta(get_the_ID(), '_price_table_annual_text', true);
					$plan_crm_id = get_post_meta(get_the_ID(), '_price_table_plan_crm_id', true);
					$plan_url = $this->get_affiliation_url($plan_crm_id);
					$programs_prices = get_post_meta(get_the_ID(), '_programs_prices', true);
					$recommended = get_post_meta(get_the_ID(), '_price_table_recommended', true);

					$recommended_class = $recommended ? ' recommended' : '';
					$recommended_label = $recommended ? '<div class="recommended-label">' . esc_html__('Recomendado', 'sanasana') . '</div>' : '';
				?>
				<?php if (in_array($visible, ['true', '1', 'yes', 'on'], true)) : ?>
					<div class="price-card<?php echo $recommended !== 'false' ? esc_attr($recommended_class) : ''; ?>">
						<?php if ($recommended !== 'false') echo $recommended_label; ?>
						<div class="price-title"><?php echo esc_html($title); ?></div>
						<p class="price-decription"><?php echo esc_html($description); ?></p>
						<div class="price-value"><span>$</span><?php echo esc_html($price); ?> <span>+<?php esc_html_e('IVA Incluido', 'sanasana-price'); ?></span></div>
						<div class="price-annual" style="display:block;"><?php echo esc_html($annual_text); ?></div>
						<a href="<?php echo esc_url($plan_url); ?>" class="price-button"><?php esc_html_e('Afiliarme ahora', 'sanasana'); ?></a>
						<ul class="price-benefits">
							<?php if (!empty($programs_prices)) :
								foreach ((array) $programs_prices as $programs_price) : ?>
									<li><?php echo esc_html($programs_price); ?></li>
								<?php endforeach;
							endif; ?>
						</ul>
						<div class="view_details_container">
							<a href="<?php the_permalink(); ?>" class="view_details-link"><?php echo esc_html__('Ver detalle', 'sanasana'); ?></a>
						</div>
					</div>
				<?php endif; ?>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
			<div class="price-next"></div>
		</div>

		<?php
		return ob_get_clean();
	}
	
	public function render_toggle_button() {
		ob_start(); ?>
		<div class="container">
			<div class="row mb-2">
				<div class="col-12">
					<div class="price-annual-button">
						<div class="toggle-container">
							<span class="toggle-label"><?php _e('Mensual', 'sanasana'); ?></span>
							<div class="toggle" id="toggleSwitch-programs">
								<div class="slider"></div>
							</div>
							<span class="toggle-label"><?php _e('Anual', 'sanasana'); ?></span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function render_programs_cards() {
		$args = array(
			'post_type'      => 'programa',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		);

		$query = new \WP_Query($args);

		if (!$query->have_posts()) {
			return '<p>' . esc_html__('No price plans available.', 'pricetable') . '</p>';
		}

		$main_image = get_option('price_table_main_image', '');
		$program_details = get_option('price_table_program_details', []);
		$visibility = get_option('price_table_visibility', []);

		ob_start(); ?>

		<div class="container sanasana-accordion-container">
			<div class="row mb-2">
				<div class="col price-table-details-image">
					<?php if (!empty($main_image)): ?>
						<img src="<?php echo esc_url($main_image); ?>" alt="<?php esc_attr_e('Main Image', 'pricetable'); ?>" class="main-image" style="max-width: 100%; margin: auto; max-height: 310px;" />
					<?php else: ?>
						<p><?php esc_html_e('No image available', 'pricetable'); ?></p>
					<?php endif; ?>
				</div>
				<?php while ($query->have_posts()) : $query->the_post();
					$description = get_post_meta(get_the_ID(), '_price_table_description', true);
					$price = get_post_meta(get_the_ID(), '_price_table_price', true);
					$annual_text = get_post_meta(get_the_ID(), '_price_table_annual_text', true);
					$plan_crm_id = get_post_meta(get_the_ID(), '_price_table_plan_crm_id', true);
					$plan_url = $this->get_affiliation_url($plan_crm_id);
					$visible = get_post_meta(get_the_ID(), '_price_table_visible', true);
				?>
					<?php if ($visible === "true"): ?>
						<div class="col sanasana-accordion-col-item">
							<h2 class="sanasana-accordion-col-item_title"><?php echo esc_html(get_the_title()); ?></h2>
							<p class="sanasana-accordion-col-item_description"><?php echo esc_html($description); ?></p>
							<div class="sanasana-accordion-col-item_price" data-original="<?php echo esc_html($price); ?>">
								<div>$</div><?php echo esc_html($price); ?><span>+<?php esc_html_e('IVA', 'sanasana'); ?></span>
							</div>
							<p class="sanasana-accordion-col-item_annual"><?php echo esc_html($annual_text); ?></p>
							<a href="<?php echo esc_url($plan_url); ?>" class="price-button price-button-accordion"><?php esc_html_e('Afiliarme ahora', 'pricetable'); ?></a>
						</div>
					<?php endif; ?>
				<?php endwhile; ?>
			</div>
		</div>

		<style>
			.post-visible-status {
				font-size: 12px;
				color: #666;
			}
		</style>

		<?php
		wp_reset_postdata();
		return ob_get_clean();
	}

	public function render_programs_cards_nosotros() {
		$args = array(
			'post_type'      => 'programa',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		);

		$query = new \WP_Query($args);

		if (!$query->have_posts()) {
			return '<p>' . esc_html__('No price plans available.', 'pricetable') . '</p>';
		}

		$main_image = get_option('price_table_main_image', '');

		ob_start(); ?>

		<div class="price-slider-container">
			<div class="price-prev price-prev-nosotros"></div>
			<div class="price-container price-container-nosotros">
				<?php while ($query->have_posts()) : $query->the_post();
					$visible = get_post_meta(get_the_ID(), '_price_table_visible', true);
					$title = get_the_title();
					$description = get_post_meta(get_the_ID(), '_price_table_description', true);
					$price = get_post_meta(get_the_ID(), '_price_table_price', true);
					$annual_text = get_post_meta(get_the_ID(), '_price_table_annual_text', true);
					$plan_crm_id = get_post_meta(get_the_ID(), '_price_table_plan_crm_id', true);
					$plan_url = $this->get_affiliation_url($plan_crm_id);
					$recommended = get_post_meta(get_the_ID(), '_price_table_recommended', true);
					$recommended_class = $recommended ? ' recommended' : '';
					$recommended_label = $recommended ? '<div class="recommended-label">' . esc_html__('Recomendado', 'pricetable') . '</div>' : '';
				?>
				<?php if ($visible === "true") : ?>
					<div class="price-card<?php echo $recommended !== 'false' ? esc_attr($recommended_class) : ''; ?> price-card-nosotros">
						<?php if ($recommended !== 'false') echo $recommended_label; ?>
						<div class="price-title"><?php echo esc_html($title); ?></div>
						<p class="price-decription"><?php echo esc_html($description); ?></p>
						<div class="price-value"><span>$</span><?php echo esc_html($price); ?> <span>+<?php esc_html_e('IVA Incluido', 'sanasana-price'); ?></span></div>
						<div class="price-annual"><?php echo esc_html($annual_text); ?></div>
						<a href="<?php echo esc_url($plan_url); ?>" class="price-button price-button-nosotros"><?php esc_html_e('Afiliarme ahora', 'sanasana'); ?></a>
					</div>
				<?php endif; ?>
				<?php endwhile; ?>
			</div>
			<div class="price-next price-next-nosotros"></div>
		</div>

		<style>
			.post-visible-status {
				font-size: 12px;
				color: #666;
			}
		</style>

		<?php
		wp_reset_postdata();
		return ob_get_clean();
	}

	public function render_programs_details() {
		$args = array(
			'post_type'      => 'programa',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		);

		$query = new \WP_Query($args);

		if (!$query->have_posts()) {
			return '<p>' . esc_html__('No price plans available.', 'pricetable') . '</p>';
		}

		$program_details = get_option('price_table_program_details', []);
		$visibility = get_option('price_table_visibility', []);

		ob_start(); ?>

		<div class="container sanasana-accordion-container sanasana-accordion-container_details">
			<div class="row">
				<div class="sanasana-accordion" id="sanasana-accordionPrograms">
					<?php foreach ($program_details as $program_detail_index => $program_detail): ?>
						<div class="sanasana-accordion-item">
							<div class="row">
								<div class="col-12">
									<h2 class="sanasana-accordion-header" id="heading<?php echo $program_detail_index; ?>">
										<button class="sanasana-accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#sanasana-collapse-<?php echo $program_detail_index; ?>" aria-expanded="true" aria-controls="sanasana-collapse-<?php echo $program_detail_index; ?>">
											<?php echo esc_html($program_detail['title'] ?? ''); ?>
											<img src="<?php echo esc_url($this->plugin_url . 'assets/images/arrow-right.png'); ?>" class="regular-arrow" alt="Arrow Icon">
										</button>
									</h2>
								</div>
							</div>

							<div id="sanasana-collapse-<?php echo $program_detail_index; ?>" class="collapse" aria-labelledby="heading<?php echo $program_detail_index; ?>" data-bs-parent="#sanasana-accordionPrograms">
								<div class="sanasana-accordion-body">
									<?php foreach ($program_detail['items'] as $index_detail => $item): ?>
										<div class="row sanasana-accordion-row">
											<div class="col sanasana-accordion-col-title">
												<div class="item-name"><?php echo esc_html($item['name'] ?? ''); ?></div>
												<div class="item-info">
													<div class="item-info-icon">
														<img src="<?php echo esc_url($this->plugin_url . 'assets/images/Info-icon.png'); ?>" alt="Info Icon" width="32" height="32"/>
													</div>
													<div class="item-info-content"><?php echo esc_html($item['info'] ?? ''); ?></div>
												</div>
											</div>

											<?php foreach ($query->posts as $post): ?>
												<?php $visible = get_post_meta($post->ID, '_price_table_visible', true); ?>
												<?php if ($visible === "true"): ?>
													<div class="col sanasana-accordion-col-item sanasana-accordion-col-item-check">
														<?php if (isset($visibility[$program_detail_index])): ?>
															<?php foreach ($visibility[$program_detail_index] as $item_index => $posts): ?>
																<?php if (isset($posts[$post->ID]) && $posts[$post->ID]): ?>
																	<img src="<?php echo esc_url($this->plugin_url . 'assets/images/checksana.png'); ?>" alt="Check Icon" width="39" height="30"/>
																	<?php break; ?>
																<?php endif; ?>
															<?php endforeach; ?>
														<?php endif; ?>
													</div>
												<?php endif; ?>
											<?php endforeach; ?>
										</div>
									<?php endforeach; ?>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<?php
		return ob_get_clean();
	}

	public function render_program_benefits() {
		global $wp;

		$current_url = home_url($wp->request);
		$slug = basename($current_url);

		$post = get_page_by_path($slug, OBJECT, 'programa');

		if (!$post) {
			return '';
		}

		$program_benefits = get_post_meta($post->ID, '_program_benefits', true);

		if (empty($program_benefits)) {
			return '';
		}

		ob_start();
		?>
		<div class="program-benefits-wrapper">
			<?php foreach ($program_benefits as $benefit) :
				$benefit_title = $benefit['benefit'] ?? '';
				$benefit_items = $benefit['items'] ?? [];

				$title_parts = explode(' ', $benefit_title, 2);
				$first_word = $title_parts[0] ?? '';
				$remaining_title = $title_parts[1] ?? '';
			?>
				<div class="program-benefit-block">
					<h3 class="program-benefit-title">
						<span style="color: #5166EC;"><?php echo esc_html($first_word); ?></span>
						<span style="color: #5166EC;"><?php echo esc_html($remaining_title); ?></span>
					</h3>

					<?php if (!empty($benefit_items)) : ?>
						<ul class="price-benefits">
							<?php foreach ($benefit_items as $item) : ?>
								<li><?php echo esc_html($item); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
			<?php echo $this->render_program_ahorros(); ?>
		</div>
		<?php

		return ob_get_clean();
	}

	public function render_program_ahorros() {
		global $wp;

		$current_url = home_url($wp->request);
		$slug = basename($current_url);

		$post = get_page_by_path($slug, OBJECT, 'programa');

		if (!$post) {
			return '';
		}

		$ahorros_data = get_post_meta($post->ID, '_price_table_ahorros', true);

		if (empty($ahorros_data) || empty($ahorros_data['ahorros'])) {
			return '';
		}

		ob_start();
		$ahorro_title = $ahorros_data['titulo'] ?? '';

		$ahorro_parts = explode(' ', $ahorro_title, 2);
		$first_word = $ahorro_parts[0] ?? '';
		$remaining_title = $ahorro_parts[1] ?? '';
		?>
		<div class="program-ahorros-wrapper">
			<?php if (!empty($ahorro_title)) : ?>
				<div class="program-benefit-title-container">
					<h3 class="program-benefit-title">
						<span style="color: #5166EC;"><?php echo esc_html($first_word); ?></span>
						<span style="color: #5166EC;"><?php echo esc_html($remaining_title); ?></span>
					</h3>

					<h3 class="program-benefit-title-save">
						<?php esc_html_e('Ahorro', 'sanasana'); ?>
					</h3>
				</div>
			<?php endif; ?>

			<div class="program-ahorros-list">
				<?php foreach ($ahorros_data['ahorros'] as $ahorro) :
					$texto = $ahorro['texto'] ?? '';
					$valor = $ahorro['valor'] ?? '';
				?>
					<div class="program-ahorro-item">
						<div class="program-ahorro-text"><?php echo esc_html($texto); ?></div>
						<div class="program-ahorro-valor"><?php echo esc_html($valor); ?></div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php

		return ob_get_clean();
	}

}