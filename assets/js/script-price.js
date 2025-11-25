jQuery(document).ready(function($) {
	var url = location.pathname;
	var window_width = $(window).width();
	//console.log("URL:", url);
	if (url === "/programas/" || url === "/en/programs/") {
		$('.sanasana-accordion-item').first().find('.sanasana-accordion-row-content').addClass('show-body');
		
	}

	jQuery(".price-value").each(function() {
		var priceText = jQuery(this).clone().children().remove().end().text().trim();
		var originalPrice = parseFloat(priceText);

		if (!isNaN(originalPrice)) {
			jQuery(this).attr("data-original", originalPrice); 
		}
	});

	$(".price-value").each(function() {
		var originalPrice = parseFloat($(this).attr("data-original"));
		if (!isNaN(originalPrice)) {
			$(this).contents().filter(function() {
				return this.nodeType === 3;
			}).first().replaceWith(originalPrice.toFixed(0) + " ");
		}
	});

	// Toggle comportamiento: pasa de Anual (por defecto) a Mensual
	$("#toggleSwitch").click(function() {
        $(this).toggleClass("active");

        $(".price-value").each(function() {
            var originalPrice = parseFloat($(this).attr("data-original"));
            if (isNaN(originalPrice)) return;

            var newPrice = $("#toggleSwitch").hasClass("active")
                ? (originalPrice * 12).toFixed(0)
                : originalPrice.toFixed(0);

            if ($("#toggleSwitch").hasClass("active")) {
                $(this).removeClass("animate-in").addClass("animate-out");
                $('.price-annual').css('display', 'none');
            } else {
                $(this).removeClass("animate-out").addClass("animate-in");
                $('.price-annual').css('display', 'block');
            }

            $(this).contents().filter(function() {
                return this.nodeType === 3;
            }).first().replaceWith(newPrice + " ");
        });
    });

	$(".sanasana-accordion-col-item_price").each(function() {
        var priceContainer = $(this);
        var priceText = priceContainer.clone().children().remove().end().text().trim();
        var originalPrice = parseFloat(priceText);

        if (!isNaN(originalPrice)) {
            priceContainer.attr("data-original", originalPrice);
            priceContainer.contents().filter(function() {
                return this.nodeType === 3 && this.nodeValue.trim().match(/^\d+(\.\d+)?$/);
            }).first().replaceWith(originalPrice.toFixed(0) + " ");
        }
    });

    // Aseguramos que el bloque anual esté oculto al cargar (por si acaso)
    //$(".sanasana-accordion-col-item_annual").css('display', 'none');

    // Toggle de precios: de anual a mensual
    $("#toggleSwitch-programs").click(function() {
        $(this).toggleClass("active");

        var isAnnual = $(this).hasClass("active");

        $(".sanasana-accordion-col-item_price").each(function() {
            var priceContainer = $(this);
            var originalPrice = parseFloat(priceContainer.attr("data-original"));
            if (isNaN(originalPrice)) return;

            var newPrice = isAnnual
                ? (originalPrice * 12).toFixed(0)
                : originalPrice.toFixed(0);

            priceContainer.contents().filter(function() {
                return this.nodeType === 3 && this.nodeValue.trim().match(/^\d+(\.\d+)?$/);
            }).first().replaceWith(newPrice + " ");
        });

        if (isAnnual) {
            $(".sanasana-accordion-col-item_annual").css('display', 'none');
        } else {
            $(".sanasana-accordion-col-item_annual").css('display', 'block');
        }
    });
	//scroll animation navmenu

	var first_row = jQuery(".first-row");
	var row_scroll_column = jQuery(".row-scroll-column");
	var column_scroll_column = jQuery('.column-scroll-column');
	

	var header = jQuery(".site-header"); // Asegúrate de que esta clase coincida con tu header sticky
	
	var lastScrollTop = 0;
	
	jQuery(window).on("scroll", function () {
	var scrollTop = jQuery(window).scrollTop(); // Posición del scroll actual

	if (first_row.length > 0) { // Verifica si el elemento existe antes de usar offset()
		var rowOff_Setfirst_row = (first_row.offset().top) - 200;

		if (scrollTop > rowOff_Setfirst_row) {
			jQuery('.ast-primary-header-bar').removeClass('animate-nav-out').addClass('animate-nav-in');
		}
		if (scrollTop < rowOff_Setfirst_row) {
			jQuery('.ast-primary-header-bar').removeClass('animate-nav-in').addClass('animate-nav-out');
		}
	}

	if (row_scroll_column.length > 0 && row_scroll_column.offset()) {
		var rowOffset = (row_scroll_column.offset().top) - 200;

		if (window_width > 992) {
			if (scrollTop >= 2100) {
				var scrollProgress = scrollTop - rowOffset;
				var newMargin = Math.max(-10, 0 - (scrollProgress / 50)); // De -10% a 0%

				if (column_scroll_column.length > 0) {
					column_scroll_column.css({
						"margin-top": newMargin + "%",
						"transition": "margin-top 0.3s ease-out"
					});
				}
			}
		}
	}

	// Programs page
	const url = window.location.pathname;

	if (url === "/programas/" || url === "/en/programs/") {
		const $rowCards = jQuery('.row-cards');
		const $detailsImage = jQuery('.price-table-details-image');
		const topOffsett = $rowCards.offset().top;
		const bottonOffset = jQuery('.sanasana-accordion-container').offset().top;
		const fixed_point = (topOffsett - scrollTop);

		if (window_width >= 1201) {
			if (fixed_point <= 139) {
				$rowCards.addClass('row-cards-active');
			} else {
				$rowCards.removeClass('row-cards-active');
			}
		}

		if (window_width >= 1041 && window_width <= 1200) {
			if (fixed_point <= 105) {
				$rowCards.addClass('row-cards-active');
			} else {
				$rowCards.removeClass('row-cards-active');
			}
		}

		if (window_width <= 1040) {
			console.log(scrollTop);
			if (scrollTop >= 250) {
				$rowCards.addClass('row-cards_active_responsive');
			} else {
				$rowCards.removeClass('row-cards_active_responsive').css('transform', '');
				$detailsImage.css('transform', '');
			}
		}
	}

	lastScrollTop = scrollTop;
});


// Evento scroll horizontal de .flex-container-wrapper
jQuery(function ($) {
	const url = window.location.pathname;
	const window_width = $(window).width();

	if (url === "/programas/" || url === "/en/programs/" && window_width <= 1040) {
		const $wrapper = $('.flex-container-wrapper');
		const $rowCards = $('.row-cards');
		const $detailsImage = $('.price-table-details-image');

		$rowCards.css({ transition: 'transform 0.05s linear', 'will-change': 'transform' });
		$detailsImage.css({ transition: 'transform 0.05s linear', 'will-change': 'transform' });

		$wrapper.on('scroll', function () {
			const scrollLeft = $(this).scrollLeft();
			if(scrollLeft > 10){
			   $('.item-name').css('border-right', 'solid 1px #5167ec');
			   }else{
				   $('.item-name').css('border-right', 'none');
			   }
			if ($rowCards.hasClass('row-cards_active_responsive')) {
				window.requestAnimationFrame(function () {
					$rowCards.css('transform', 'translateX(-' + scrollLeft + 'px)');
					$detailsImage.css('transform', 'translateX(' + scrollLeft + 'px)');
				});
			}
		});
	}
});


	//slider
	var $slider = $(".price-container");
	var cardWidth = $(".price-card").outerWidth(true) + 22; // Ancho de cada tarjeta con margen incluido
	var scrollAmount = cardWidth; // Cantidad a desplazar por clic
	var animationSpeed = 900; // Velocidad de la animación en milisegundos

	function smoothScroll(element, to, duration) {
		var start = element.scrollLeft();
		var change = to - start;
		var currentTime = 0;
		var increment = 90; // Controla la frecuencia de actualización

		function easeInOutQuad(t, b, c, d) {
			t /= d / 2;
			if (t < 1) return (c / 2) * t * t + b;
			t--;
			return (-c / 2) * (t * (t - 2) - 1) + b;
		}

		function animateScroll() {
			currentTime += increment;
			var newScrollLeft = easeInOutQuad(currentTime, start, change, duration);
			element.scrollLeft(newScrollLeft);
			if (currentTime < duration) {
				setTimeout(animateScroll, increment);
			}
		}
		animateScroll();
	}

	$(".price-next").on("click", function () {
		smoothScroll($slider, $slider.scrollLeft() + scrollAmount, animationSpeed);
	});

	$(".price-prev").on("click", function () {
		smoothScroll($slider, $slider.scrollLeft() - scrollAmount, animationSpeed);
	});

	// Accordion programs descriptions
	var isOpen = 0; // 0 = cerrado, 1 = abierto

	jQuery(function($) {
	function toggleAccordion($trigger) {
		const $row = $trigger.closest('.sanasana-accordion-item');
		const $allRows = $('.sanasana-accordion-item .sanasana-accordion-row-content');
		const $targetRows = $row.find('.sanasana-accordion-row-content');

		// Si ya estaba abierto
		const isActive = $targetRows.hasClass('show-body');

		// Cerrar todos
		$allRows.removeClass('show-body');
		$('.sanasana-accordion-button').removeClass('active');

		if (!isActive) {
			$targetRows.addClass('show-body');
			$row.find('.sanasana-accordion-button').addClass('active');
		}
	}

	// Click en el botón
	$('.sanasana-accordion-button').on('click', function() {
		toggleAccordion($(this));
	});

	// Click en el título
	$('.sanasana-accordion-header').on('click', function() {
		toggleAccordion($(this));
	});
});

	
	var isMobile = window.matchMedia('(max-width: 768px)').matches;

	if (isMobile) {
		// En responsive: click
		$('.item-info-icon').on('click', function () {
			$(this).siblings('.item-info-content').toggleClass('show-info');
		});
	} else {
		// En desktop: hover
		$('.item-info-icon').hover(
			function () {
				$(this).siblings('.item-info-content').addClass('show-info');
			},
			function () {
				$(this).siblings('.item-info-content').removeClass('show-info');
			}
		);

		// Opcional: mantener el hover si pasa al .item-info-content
		$('.item-info-content').hover(
			function () {
				$(this).addClass('show-info');
			},
			function () {
				$(this).removeClass('show-info');
			}
		);
	}
	
	jQuery('.item-info-content').on('click', function() {
		jQuery(this).toggleClass('show-info');
	});
	

	//resenas en home page
	$('.resena-ver-mas').on('click', function () {
		var modalId = $(this).data('modal');
		$('#' + modalId).fadeIn();
	});

	$('.resena-close').on('click', function () {
		$(this).closest('.resena-modal').fadeOut();
	});
	$('.resena-modal-content').on('click', function () {
		$(this).parent('.resena-modal').fadeOut();
	});


	$(window).on('click', function (e) {
		$('.resena-modal').each(function () {
			if (e.target === this) {
				$(this).fadeOut();
			}
		});
	});

});

