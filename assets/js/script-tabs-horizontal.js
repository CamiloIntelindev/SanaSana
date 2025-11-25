jQuery(document).ready(function ($) {
    
    function initHorizontalTabs() {
        $(".tab-container-horizontal").each(function () {
            var $container = $(this);
            var $tabsWrapper = $container.find(".tabs-wrapper-horizontal");
            var $progressBarContainer = $container.find(".progress-bar-container-horizontal");
            var $progressBarHorizontal = $progressBarContainer.find(".progress-bar-horizontal");
            var $tabs = $container.find(".nav-tabs-horizontal .nav-link");
            var $panes = $container.find(".tab-content-horizontal .tab-pane");
            var tabCount = $tabs.length;
            var isDragging = false;
            var startX, initialLeft;

            // Ajustar el tamaño de la barra de progreso
            var tabWidth = $tabsWrapper.width() / tabCount;
            $progressBarHorizontal.css("width", tabWidth + "px");

            function updateTab(index) {
                if (index < 0) index = 0;
                if (index >= tabCount) index = tabCount - 1;

                // Activar el tab seleccionado
                $tabs.removeClass("active").eq(index).addClass("active");

                // Activar el contenido correspondiente
                $panes.removeClass("active").eq(index).addClass("active");

                // Mover la barra de progreso con animación suave
                var newPosition = index * tabWidth;
                $progressBarHorizontal.css({
                    "left": newPosition + "px",
                    "transition": "left 0.3s ease-in-out"
                });
            }

            $tabs.each(function (index) {
                $(this).on("click", function () {
                    updateTab(index);
                });
            });

            // =========================
            // Drag and Drop en la Barra de Progreso Horizontal
            // =========================
            $progressBarHorizontal.on("mousedown touchstart", function (e) {
                isDragging = true;
                startX = e.clientX || e.originalEvent.touches[0].clientX;
                initialLeft = parseFloat($progressBarHorizontal.css("left")) || 0;
            });

            $(document).on("mousemove touchmove", function (e) {
                if (!isDragging) return;

                var clientX = e.clientX || e.originalEvent.touches[0].clientX;
                var deltaX = clientX - startX;
                var containerWidth = $progressBarContainer.width();
                var newLeft = initialLeft + deltaX;

                // Limitar dentro del contenedor
                newLeft = Math.max(0, Math.min(newLeft, containerWidth - tabWidth));

                // Mover la barra de progreso
                $progressBarHorizontal.css("left", newLeft + "px");

                // Determinar el tab en función de la posición
                var newIndex = Math.round(newLeft / tabWidth);
                updateTab(newIndex);
            });

            $(document).on("mouseup touchend", function () {
                isDragging = false;
            });

            // Ajustar en caso de cambios de pantalla
            $(window).on("resize", function () {
                tabWidth = $tabsWrapper.width() / tabCount;
                $progressBarHorizontal.css("width", tabWidth + "px");
            });
        });
    }

    // =========================
    // Inicializar Tabs Horizontales
    // =========================
    function initTabs() {
        initHorizontalTabs();
    }

    initTabs();
    $(window).on("resize", function () {
        initTabs();
    });

});
