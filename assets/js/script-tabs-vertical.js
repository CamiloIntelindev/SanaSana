jQuery(document).ready(function ($) {
    function initSliderByButtonsOnly() {
        $(".tab-container-mobile").each(function () {
            const $container = $(this);
            const $panes = $container.find(".tab-pane-mobile-vertical");
            const $titles = $container.find(".nav-link-mobile-vertical");
            const $prevBtn = $container.find(".nav-button-prev-mobile-vertical");
            const $nextBtn = $container.find(".nav-button-post-mobile-vertical");

            let currentIndex = $panes.index($panes.filter(".active"));
            if (currentIndex < 0) currentIndex = 0;

            function updatePane(index) {
                if (index < 0 || index >= $panes.length) return;
                currentIndex = index;

                // Mostrar el tab-pane correspondiente
                $panes.removeClass("active").eq(index).addClass("active");

                // Marcar el botón/título correspondiente
                $titles.removeClass("active").eq(index).addClass("active");
            }

            $prevBtn.on("click", function () {
                updatePane(currentIndex - 1);
            });

            $nextBtn.on("click", function () {
                updatePane(currentIndex + 1);
            });

            // Inicialización
            updatePane(currentIndex);
        });
    }

    initSliderByButtonsOnly();
	
	//
	const $container = $('.tab-container-desktop.tab-container-vertical');
    if ($container.length === 0) return;

    const $progressBarContainer = $container.find('.progress-bar-container-vertical');
    const $progressBar = $container.find('.progress-bar-vertical');
    const $navItems = $container.find('.nav-item-vertical');
    const $tabPanes = $container.find('.tab-pane');

    let isDragging = false;
    let containerTop = 0;
    const stepHeight = 116;
    const totalSteps = $navItems.length;
    const maxSteps = totalSteps - 1;
    let containerHeight = stepHeight * totalSteps;

    const setActiveByIndex = function (index) {
        $navItems.find('.nav-link').removeClass('active');
        $tabPanes.removeClass('active');

        $navItems.eq(index).find('.nav-link').addClass('active');
        $tabPanes.eq(index).addClass('active');
    };

    const moveBarTo = function (index) {
        const snappedY = index * stepHeight;
        $progressBar.css('top', snappedY + 'px');
        setActiveByIndex(index);
    };

    $progressBar.on('mousedown', function (e) {
        isDragging = true;
        const rect = $progressBarContainer[0].getBoundingClientRect();
        containerTop = rect.top;
        $('body').css('user-select', 'none');

        // Desactiva transición mientras arrastra
        $progressBar.css('transition', 'none');
    });

    $(document).on('mousemove', function (e) {
        if (!isDragging) return;
        const relativeY = e.clientY - containerTop;
        const clampedY = Math.max(0, Math.min(relativeY, containerHeight));
        $progressBar.css('top', clampedY + 'px');
    });

    $(document).on('mouseup', function (e) {
        if (!isDragging) return;
        isDragging = false;
        $('body').css('user-select', '');

        const relativeY = e.clientY - containerTop;
        const clampedY = Math.max(0, Math.min(relativeY, containerHeight));
        const snappedIndex = Math.round(clampedY / stepHeight);
        const finalIndex = Math.min(snappedIndex, maxSteps);

        // Activa transición y mueve al slot
        $progressBar.css('transition', 'top 0.2s ease');
        moveBarTo(finalIndex);
    });
	
	$navItems.on('click', function () {
        const index = $(this).index();

        // Mueve la barra con transición
        $progressBar.css('transition', 'top 0.2s ease');
        moveBarTo(index);
    });
});
