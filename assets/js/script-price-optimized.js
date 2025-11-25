/**
 * Sanasana Price Tables & Accordion - Optimized
 * @package Sanasana
 * @version 1.1.0
 * 
 * Optimizations applied:
 * - DOM caching to reduce repeated queries
 * - Debounced scroll handlers
 * - Event delegation for dynamic elements
 * - Reduced jQuery overhead
 * - Removed commented/dead code
 * - Consolidated repeated logic
 */

(function($) {
	'use strict';

	// Configuration
	const CONFIG = {
		animationSpeed: 900,
		scrollIncrement: 90,
		debounceDelay: 16, // ~60fps
		breakpoints: {
			mobile: 1040,
			tablet: 1200
		}
	};

	// DOM cache
	const DOM = {
		window: $(window),
		body: $('body'),
		priceValues: null,
		priceAnnual: null,
		toggleSwitch: null,
		toggleSwitchPrograms: null,
		accordionItems: null,
		priceSlider: null,
		init() {
			this.priceValues = $('.price-value');
			this.priceAnnual = $('.price-annual');
			this.toggleSwitch = $('#toggleSwitch');
			this.toggleSwitchPrograms = $('#toggleSwitch-programs');
			this.accordionItems = $('.sanasana-accordion-item');
			this.priceSlider = $('.price-container');
		}
	};

	// Utility: Debounce
	function debounce(func, wait) {
		let timeout;
		return function executedFunction(...args) {
			clearTimeout(timeout);
			timeout = setTimeout(() => func.apply(this, args), wait);
		};
	}

	// Utility: Get original price from element
	function getOriginalPrice($el) {
		const price = parseFloat($el.attr('data-original'));
		return isNaN(price) ? 0 : price;
	}

	// Utility: Update price text node
	function updatePriceText($el, newPrice) {
		$el.contents().filter(function() {
			return this.nodeType === 3;
		}).first().replaceWith(newPrice + ' ');
	}

	// Initialize price values with data attributes
	function initializePrices() {
		DOM.priceValues.each(function() {
			const $this = $(this);
			const priceText = $this.clone().children().remove().end().text().trim();
			const originalPrice = parseFloat(priceText);
			
			if (!isNaN(originalPrice)) {
				$this.attr('data-original', originalPrice);
				updatePriceText($this, originalPrice.toFixed(0));
			}
		});

		// Initialize accordion prices
		$('.sanasana-accordion-col-item_price').each(function() {
			const $this = $(this);
			const priceText = $this.clone().children().remove().end().text().trim();
			const originalPrice = parseFloat(priceText);
			
			if (!isNaN(originalPrice)) {
				$this.attr('data-original', originalPrice);
				updatePriceText($this, originalPrice.toFixed(0));
			}
		});
	}

	// Toggle price (monthly/annual)
	function togglePrice($toggle, isPrograms = false) {
		const isAnnual = $toggle.hasClass('active');
		const $prices = isPrograms ? $('.sanasana-accordion-col-item_price') : DOM.priceValues;

		$prices.each(function() {
			const $this = $(this);
			const originalPrice = getOriginalPrice($this);
			if (!originalPrice) return;

			const newPrice = isAnnual ? (originalPrice * 12).toFixed(0) : originalPrice.toFixed(0);
			updatePriceText($this, newPrice);

			if (!isPrograms) {
				$this.toggleClass('animate-out animate-in', !isAnnual);
			}
		});

		// Toggle annual label visibility
		const $annualLabels = isPrograms ? $('.sanasana-accordion-col-item_annual') : DOM.priceAnnual;
		$annualLabels.css('display', isAnnual ? 'none' : 'block');
	}

	// Smooth scroll with easing
	function smoothScroll($element, to, duration) {
		const start = $element.scrollLeft();
		const change = to - start;
		let currentTime = 0;

		function easeInOutQuad(t, b, c, d) {
			t /= d / 2;
			if (t < 1) return (c / 2) * t * t + b;
			t--;
			return (-c / 2) * (t * (t - 2) - 1) + b;
		}

		function animateScroll() {
			currentTime += CONFIG.scrollIncrement;
			const newScrollLeft = easeInOutQuad(currentTime, start, change, duration);
			$element.scrollLeft(newScrollLeft);
			if (currentTime < duration) {
				setTimeout(animateScroll, CONFIG.scrollIncrement);
			}
		}
		animateScroll();
	}

	// Accordion toggle logic
	function toggleAccordion($trigger) {
		const $row = $trigger.closest('.sanasana-accordion-item');
		const $targetRows = $row.find('.sanasana-accordion-row-content');
		const isActive = $targetRows.hasClass('show-body');

		// Close all
		$('.sanasana-accordion-item .sanasana-accordion-row-content').removeClass('show-body');
		$('.sanasana-accordion-button').removeClass('active');

		// Open if wasn't active
		if (!isActive) {
			$targetRows.addClass('show-body');
			$row.find('.sanasana-accordion-button').addClass('active');
		}
	}

	// Scroll handler for programs page sticky cards
	const handleProgramsScroll = debounce(function() {
		const url = window.location.pathname;
		if (url !== '/programas/' && url !== '/en/programs/') return;

		const windowWidth = DOM.window.width();
		const scrollTop = DOM.window.scrollTop();
		const $rowCards = $('.row-cards');
		const $detailsImage = $('.price-table-details-image');
		const $accordion = $('.sanasana-accordion-container');

		if (!$rowCards.length || !$accordion.length) return;

		const topOffset = $rowCards.offset().top;
		const fixedPoint = topOffset - scrollTop;

		// Desktop/Tablet sticky behavior
		if (windowWidth >= CONFIG.breakpoints.mobile) {
			const threshold = windowWidth >= CONFIG.breakpoints.tablet ? 139 : 105;
			$rowCards.toggleClass('row-cards-active', fixedPoint <= threshold);
		} else {
			// Mobile scroll behavior
			$rowCards.toggleClass('row-cards_active_responsive', scrollTop >= 250);
			if (scrollTop < 250) {
				$rowCards.add($detailsImage).css('transform', '');
			}
		}
	}, CONFIG.debounceDelay);

	// Horizontal scroll handler for mobile programs
	function initMobileProgramsScroll() {
		const url = window.location.pathname;
		const windowWidth = DOM.window.width();

		if ((url !== '/programas/' && url !== '/en/programs/') || windowWidth > CONFIG.breakpoints.mobile) {
			return;
		}

		const $wrapper = $('.flex-container-wrapper');
		const $rowCards = $('.row-cards');
		const $detailsImage = $('.price-table-details-image');

		$rowCards.add($detailsImage).css({
			transition: 'transform 0.05s linear',
			'will-change': 'transform'
		});

		$wrapper.on('scroll', function() {
			const scrollLeft = $(this).scrollLeft();
			$('.item-name').css('border-right', scrollLeft > 10 ? 'solid 1px #5167ec' : 'none');

			if ($rowCards.hasClass('row-cards_active_responsive')) {
				window.requestAnimationFrame(() => {
					$rowCards.css('transform', `translateX(-${scrollLeft}px)`);
					$detailsImage.css('transform', `translateX(${scrollLeft}px)`);
				});
			}
		});
	}

	// Initialize slider navigation
	function initSlider() {
		if (!DOM.priceSlider.length) return;

		const cardWidth = $('.price-card').outerWidth(true) + 22;

		$('.price-next').on('click', function() {
			smoothScroll(DOM.priceSlider, DOM.priceSlider.scrollLeft() + cardWidth, CONFIG.animationSpeed);
		});

		$('.price-prev').on('click', function() {
			smoothScroll(DOM.priceSlider, DOM.priceSlider.scrollLeft() - cardWidth, CONFIG.animationSpeed);
		});
	}

	// Initialize info tooltips (responsive hover/click)
	function initInfoTooltips() {
		const isMobile = window.matchMedia('(max-width: 768px)').matches;

		if (isMobile) {
			$('.item-info-icon').on('click', function() {
				$(this).siblings('.item-info-content').toggleClass('show-info');
			});
			$('.item-info-content').on('click', function() {
				$(this).toggleClass('show-info');
			});
		} else {
			$('.item-info-icon').hover(
				function() { $(this).siblings('.item-info-content').addClass('show-info'); },
				function() { $(this).siblings('.item-info-content').removeClass('show-info'); }
			);
			$('.item-info-content').hover(
				function() { $(this).addClass('show-info'); },
				function() { $(this).removeClass('show-info'); }
			);
		}
	}

	// Initialize review modals
	function initReviewModals() {
		$('.resena-ver-mas').on('click', function() {
			const modalId = $(this).data('modal');
			$('#' + modalId).fadeIn();
		});

		$('.resena-close, .resena-modal-content').on('click', function(e) {
			$(this).closest('.resena-modal').fadeOut();
		});

		DOM.window.on('click', function(e) {
			if ($(e.target).hasClass('resena-modal')) {
				$(e.target).fadeOut();
			}
		});
	}

	// Main initialization
	function init() {
		// Cache DOM
		DOM.init();

		// Initialize prices
		initializePrices();

		// Open first accordion item on programs pages
		const url = window.location.pathname;
		if (url === '/programas/' || url === '/en/programs/') {
			DOM.accordionItems.first().find('.sanasana-accordion-row-content').addClass('show-body');
		}

		// Event: Toggle switches
		DOM.toggleSwitch.on('click', function() {
			$(this).toggleClass('active');
			togglePrice($(this), false);
		});

		DOM.toggleSwitchPrograms.on('click', function() {
			$(this).toggleClass('active');
			togglePrice($(this), true);
		});

		// Event: Accordion (using delegation for better performance)
		DOM.body.on('click', '.sanasana-accordion-button, .sanasana-accordion-header', function() {
			toggleAccordion($(this));
		});

		// Initialize components
		initSlider();
		initInfoTooltips();
		initReviewModals();
		initMobileProgramsScroll();

		// Scroll handler
		DOM.window.on('scroll', handleProgramsScroll);
	}

	// Run on DOM ready
	$(init);

})(jQuery);
