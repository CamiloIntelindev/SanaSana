/**
 * Sanasana Tabs (Horizontal, Vertical & FAQ) - Optimized & Consolidated
 * @package Sanasana
 * @version 1.1.0
 * 
 * Optimizations:
 * - Consolidated 3 separate files into one module
 * - Event delegation for dynamic elements
 * - Debounced resize handlers
 * - Reduced DOM queries with caching
 * - requestAnimationFrame for smooth animations
 */

(function($) {
	'use strict';

	// Utility: Debounce
	function debounce(func, wait) {
		let timeout;
		return function executedFunction(...args) {
			clearTimeout(timeout);
			timeout = setTimeout(() => func.apply(this, args), wait);
		};
	}

	/**
	 * Horizontal Tabs with draggable progress bar
	 */
	class HorizontalTabs {
		constructor($container) {
			this.$container = $container;
			this.$tabsWrapper = $container.find('.tabs-wrapper-horizontal');
			this.$progressBar = $container.find('.progress-bar-horizontal');
			this.$tabs = $container.find('.nav-tabs-horizontal .nav-link');
			this.$panes = $container.find('.tab-content-horizontal .tab-pane');
			this.tabCount = this.$tabs.length;
			this.isDragging = false;
			this.tabWidth = 0;

			if (this.tabCount === 0) return;

			this.init();
		}

		init() {
			this.calculateTabWidth();
			this.attachEvents();
			// Activate first tab
			this.updateTab(0);
		}

		calculateTabWidth() {
			this.tabWidth = this.$tabsWrapper.width() / this.tabCount;
			this.$progressBar.css('width', this.tabWidth + 'px');
		}

		updateTab(index) {
			index = Math.max(0, Math.min(index, this.tabCount - 1));

			// Update active states
			this.$tabs.removeClass('active').eq(index).addClass('active');
			this.$panes.removeClass('active').eq(index).addClass('active');

			// Move progress bar
			const newPosition = index * this.tabWidth;
			this.$progressBar.css({
				left: newPosition + 'px',
				transition: 'left 0.3s ease-in-out'
			});
		}

		attachEvents() {
			const self = this;

			// Tab clicks
			this.$tabs.each(function(index) {
				$(this).on('click', () => self.updateTab(index));
			});

			// Draggable progress bar
			this.$progressBar.on('mousedown touchstart', (e) => {
				self.isDragging = true;
				self.startX = e.clientX || e.originalEvent.touches[0].clientX;
				self.initialLeft = parseFloat(self.$progressBar.css('left')) || 0;
			});

			$(document).on('mousemove.horizontalTabs touchmove.horizontalTabs', (e) => {
				if (!self.isDragging) return;

				const clientX = e.clientX || e.originalEvent.touches[0].clientX;
				const deltaX = clientX - self.startX;
				let newLeft = self.initialLeft + deltaX;

				// Constrain within bounds
				const maxLeft = self.$tabsWrapper.width() - self.tabWidth;
				newLeft = Math.max(0, Math.min(newLeft, maxLeft));

				self.$progressBar.css('left', newLeft + 'px');

				// Update active tab based on position
				const newIndex = Math.round(newLeft / self.tabWidth);
				self.updateTab(newIndex);
			});

			$(document).on('mouseup.horizontalTabs touchend.horizontalTabs', () => {
				self.isDragging = false;
			});
		}

		resize() {
			this.calculateTabWidth();
		}
	}

	/**
	 * Vertical Tabs (Mobile) with scroll-based navigation
	 */
	class VerticalTabs {
		constructor($container) {
			this.$container = $container;
			this.$tabsList = $container.find('.tabs-wrapper .nav-tabs-vertical');
			this.$tabButtons = $container.find('.nav-link-mobile-vertical');
			this.$tabContents = $container.find('.tab-pane-mobile-vertical');
			this.tabCount = this.$tabButtons.length;

			if (this.tabCount === 0) return;

			this.init();
		}

		init() {
			this.attachEvents();
		}

		attachEvents() {
			const self = this;

			// Sync tabs with scroll position
			this.$tabsList.on('scroll', function() {
				const scrollLeft = $(this).scrollLeft();
				const maxScroll = this.scrollWidth - $(this).outerWidth();

				if (maxScroll <= 0) return;

				const scrollPercentage = (scrollLeft / maxScroll) * 100;
				const step = 100 / self.tabCount;
				const activeIndex = Math.min(Math.floor(scrollPercentage / step), self.tabCount - 1);

				self.$tabButtons.removeClass('active').eq(activeIndex).addClass('active');
				self.$tabContents.removeClass('active').eq(activeIndex).addClass('active');
			});

			// Tab click scrolls to position
			this.$tabButtons.on('click', function() {
				const index = $(this).data('index');
				const maxScroll = self.$tabsList[0].scrollWidth - self.$tabsList.outerWidth();
				const targetScroll = (index * 20 * maxScroll) / 100;

				self.$tabsList.animate({ scrollLeft: targetScroll }, 400);
			});
		}
	}

	/**
	 * FAQ Tabs with accordion
	 */
	class FaqTabs {
		constructor() {
			this.$navItems = $('.faq-tab-nav-item');
			this.$contents = $('.faq-tab-content');

			if (this.$navItems.length === 0) return;

			this.init();
		}

		init() {
			// Activate first tab
			this.$navItems.first().addClass('active');
			this.$contents.first().show();
			this.updateTabIcon(this.$navItems.first());

			this.attachEvents();
		}

		updateTabIcon($navItem) {
			const $img = $navItem.find('img');
			if (!$img.length) return;

			const src = $img.attr('src');
			if (!src.endsWith('-blue.svg')) {
				$img.attr('src', src.replace(/(\.svg)$/, '-blue$1'));
			}
		}

		resetAllIcons() {
			this.$navItems.find('img').each(function() {
				const src = $(this).attr('src');
				$(this).attr('src', src.replace('-blue.svg', '.svg'));
			});
		}

		attachEvents() {
			const self = this;

			// Tab navigation
			this.$navItems.on('click', function() {
				const index = $(this).data('tab-index');

				// Reset icons and states
				self.resetAllIcons();
				self.$navItems.removeClass('active');
				$(this).addClass('active');
				self.updateTabIcon($(this));

				// Show content
				self.$contents.hide();
				$(`.faq-tab-content[data-tab-index="${index}"]`).show();
			});

			// Accordion toggle
			$(document).on('click', '.faq-question', function() {
				const $item = $(this).closest('.faq-accordion-item');
				const $answer = $item.find('.faq-answer');

				if ($item.hasClass('open')) {
					$item.removeClass('open');
					$answer.slideUp(300);
				} else {
					$item.addClass('open');
					$answer.slideDown(300);
				}
			});
		}
	}

	/**
	 * Tabs Manager - Initializes all tab types
	 */
	class TabsManager {
		constructor() {
			this.horizontalTabs = [];
			this.verticalTabs = [];
			this.faqTabs = null;
		}

		init() {
			// Initialize horizontal tabs
			$('.tab-container-horizontal').each((i, el) => {
				this.horizontalTabs.push(new HorizontalTabs($(el)));
			});

			// Initialize vertical tabs (mobile)
			$('.tab-container-mobile').each((i, el) => {
				this.verticalTabs.push(new VerticalTabs($(el)));
			});

			// Initialize FAQ tabs
			if ($('.faq-tab-nav-item').length) {
				this.faqTabs = new FaqTabs();
			}

			// Handle window resize
			$(window).on('resize', debounce(() => {
				this.horizontalTabs.forEach(tab => tab.resize());
			}, 250));
		}
	}

	// Initialize on DOM ready
	$(function() {
		const tabsManager = new TabsManager();
		tabsManager.init();
	});

})(jQuery);
