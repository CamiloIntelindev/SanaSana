jQuery(document).ready(function ($) {
	var $tabsList = $(".tab-container-mobile .tabs-wrapper .nav-tabs-vertical");
    var $tabButtons = $(".nav-link-mobile-vertical");
    var $tabContents = $(".tab-pane-mobile-vertical");
    var tabCount = $tabButtons.length;

    $tabsList.on("scroll", function () {
        var scrollLeft = $tabsList.scrollLeft(); // Posición actual del scrollbar
        var maxScroll = $tabsList[0].scrollWidth - $tabsList.outerWidth(); // Máximo desplazamiento

        if (maxScroll <= 0 || tabCount === 0) return; // Evita errores

        var scrollPercentage = (scrollLeft / maxScroll) * 100;

        // Calculamos el índice activo basado en el número total de tabs
        var step = 100 / tabCount;
        var activeIndex = Math.floor(scrollPercentage / step);

        // Aseguramos que no se pase del último índice
        activeIndex = Math.min(activeIndex, tabCount - 1);

        // Cambiar la clase `active` en los tabs
        $tabButtons.removeClass("active");
        $tabButtons.eq(activeIndex).addClass("active");

        // Cambiar la clase `active` en el contenido del tab
        $tabContents.removeClass("active");
        $tabContents.eq(activeIndex).addClass("active");
    }); 

  

    // También actualizar al hacer click en un tab
    $tabButtons.on("click", function () {
        var index = $(this).data("index");

        // Mover el scroll al porcentaje correcto
        var targetScroll = (index * 20 * ($tabsList[0].scrollWidth - $tabsList.outerWidth())) / 100;

        $tabsList.animate({ scrollLeft: targetScroll }, 400);
    });
	
	//Faq
	// Inicializar el primer tab como activo
    $('.faq-tab-nav-item').first().addClass('active');
	$('.faq-tab-content').first().removeAttr('style');
	// Verificar al cargar la página
    $('.faq-tab-nav-item.active').each(function () {
        var img = $(this).find('img');
        if (img.length) {
            let src = img.attr('src');
            if (!src.endsWith('-blue.svg')) {
                let newSrc = src.replace(/(\.svg)$/, '-blue$1');
                img.attr('src', newSrc);
            }
        }
    });

    // Evento al hacer clic
    $('.faq-tab-nav-item').on('click', function () {
        var index = $(this).data('tab-index');

        // Restablecer todas las imágenes al estado original
        $('.faq-tab-nav-item img').each(function () {
            var src = $(this).attr('src');
            var originalSrc = src.replace('-blue.svg', '.svg');
            $(this).attr('src', originalSrc);
        });

        // Activar el tab actual
        $('.faq-tab-nav-item').removeClass('active');
        $(this).addClass('active');

        // Modificar la URL de la imagen anidada en el tab clicado
        let img = $(this).find('img');
        if (img.length) {
            var src = img.attr('src');
            if (!src.endsWith('-blue.svg')) {
                var newSrc = src.replace(/(\.svg)$/, '-blue$1');
                img.attr('src', newSrc);
            }
        }

        // Mostrar el contenido correspondiente
        $('.faq-tab-content').hide();
        $('.faq-tab-content[data-tab-index="' + index + '"]').show();
    });


	
	$(document).on('click', '.faq-question', function () {
		var $item = $(this).closest('.faq-accordion-item');

		if ($item.hasClass('open')) {
			$item.removeClass('open');
			$item.find('.faq-answer').slideUp();
		} else {
			$item.addClass('open');
			$item.find('.faq-answer').slideDown();
		}
	});


});
