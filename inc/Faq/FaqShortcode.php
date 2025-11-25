<?php
/**
 * @package Faq
 *
 */

namespace SanasanaInit\Faq;

use SanasanaInit\General\BaseController;
use WP_Query;

class FaqShortcode extends BaseController
{    
    public function register()
    {
        add_shortcode('faq_tabs', [$this, 'render_faq_tabs_shortcode']);
    }

    public function render_faq_tabs_shortcode($atts) {
        $atts = shortcode_atts(['title' => ''], $atts, 'faq_tabs');

        if (empty($atts['title'])) return '<p>No FAQ Tab title provided.</p>';

        $args = [
            'post_type'      => 'faq-tab',
            'title'          => $atts['title'],
            'posts_per_page' => 1,
        ];
        $query = new WP_Query($args);
        if (!$query->have_posts()) return '<p>No FAQ Tab found.</p>';

        $post = $query->posts[0];
        $tabs = get_post_meta($post->ID, '_faq_tabs', true);
        if (empty($tabs)) return '<p>No FAQs available.</p>';

        ob_start();

        echo '<div class="faq-search-wrapper" style="margin-bottom:20px; position:relative;">
            <input type="search" id="faq-search" placeholder="'. esc_html__("Describe el problema", "sanasana") .'">
            <button id="faq-search-btn"><img src="'. esc_url("/wp-content/plugins/sanasana/assets/images/search.svg") .'"></button>
            <button id="faq-reset-btn" style="display:none; margin-left:10px;">'. esc_html__("Restablecer", "sanasana") .'</button>
            <div id="faq-suggestions" class="faq-suggestions-list"></div>
        </div>';

        echo '<div id="faq-autocomplete-data" style="display:none;">';
        foreach ($tabs as $tabIndex => $tab) {
            foreach ($tab['faqs'] as $faqIndex => $faq) {
                echo '<div class="faq-suggestion-item" 
                        data-tab-index="' . esc_attr($tabIndex) . '"
                        data-faq-index="' . esc_attr($faqIndex) . '"
                        data-question="' . esc_attr($faq['title']) . '"
                        data-answer="' . esc_attr(strip_tags($faq['content'])) . '">
                      ' . esc_html($faq['title']) . '
                  </div>';
            }
        }
        echo '</div>';

        echo '<div class="faq-tabs-wrapper">';
        echo '<ul class="faq-tabs-nav">';
        foreach ($tabs as $index => $tab) {
            echo '<li class="faq-tab-nav-item' . ($index === 0 ? ' active' : '') . '" data-tab-index="' . esc_attr($index) . '">';
            if (!empty($tab['icon'])) {
                echo '<img src="' . esc_url($tab['icon']) . '" alt="" class="faq-tab-icon" />';
            }
            echo '<span>' . esc_html($tab['label']) . '</span></li>';
        }
        echo '</ul>';

        echo '<div class="faq-tabs-content">';
        foreach ($tabs as $index => $tab) {
            echo '<div class="faq-tab-content" data-tab-index="' . esc_attr($index) . '" style="display: ' . ($index === 0 ? 'block' : 'none') . ';">';
            foreach ($tab['faqs'] as $faq) {
                echo '<div class="faq-accordion-item">';
                echo '<div class="faq-question">' . esc_html($faq['title']) . '<span class="faq-icon" aria-hidden="true"><img src="https://sanasanastoragews.blob.core.windows.net/blobwsdev/2025/06/flechaabajo.png" alt="" /></span></div>';
                echo '<div class="faq-answer" style="display: none;">' . wp_kses_post(wpautop($faq['content'])) . '</div>';
                echo '</div>';
            }
            echo '</div>';
        }
        echo '</div></div>';
        ?>

        <style>
        .faq-suggestions-list {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #fff;
            border: 1px solid #ddd;
            z-index: 1000;
            max-height: 250px;
            overflow-y: auto;
            display: none;
        }
        .faq-suggestion-result {
            padding: 10px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
        }
        .faq-suggestion-result:hover {
            background: #f0f0f0;
        }
        .faq-suggestion-question {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .faq-suggestion-answer {
            font-size: 13px;
            color: #666;
        }
        </style>

        <script>
        (function($) {
            $(document).ready(function () {
                const normalize = str => str.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();

                $('#faq-search').on('input', function () {
                    const search = normalize($(this).val());
                    const $results = $('#faq-suggestions');
                    $results.empty();

                    if (!search) {
                        $results.hide();
                        return;
                    }

                    $('#faq-autocomplete-data .faq-suggestion-item').each(function () {
                        const question = normalize($(this).data('question'));
                        const answer = normalize($(this).data('answer'));

                        if (question.includes(search) || answer.includes(search)) {
                            const tabIndex = $(this).data('tab-index');
                            const faqIndex = $(this).data('faq-index');
                            const display = $(this).data('question');
                            const answerDisplay = $(this).data('answer').substring(0, 80) + '...';

                            const suggestion = $(`
                                <div class="faq-suggestion-result">
                                    <div class="faq-suggestion-question">${display}</div>
                                    <div class="faq-suggestion-answer">${answerDisplay}</div>
                                </div>
                            `);

                            suggestion.on('click', function () {
                                $('.faq-tab-nav-item').removeClass('active').eq(tabIndex).addClass('active');
                                $('.faq-tab-content').hide().eq(tabIndex).show();

                                const targetItem = $('.faq-tab-content').eq(tabIndex).find('.faq-accordion-item').eq(faqIndex);
                                targetItem.find('.faq-answer').slideDown();
                                targetItem.addClass('open');

                                $('html, body').animate({
                                    scrollTop: targetItem.offset().top - 100
                                }, 400);

                                $results.empty().hide();
                            });

                            $results.append(suggestion);
                        }
                    });

                    $results.show();
                });
            });
        })(jQuery);
        </script>
        <?php

        wp_reset_postdata();
        return ob_get_clean();
    }

}
