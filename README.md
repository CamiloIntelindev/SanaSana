# SanaSana – WordPress Plugin

Plugin personalizado para el sitio SanaSana. Incluye shortcodes, metaboxes y controladores para tablas de precios, programas, tabs, cuestionarios, FAQs, reseñas y utilidades SEO.

## Requisitos
- WordPress 6.x
- PHP 7.4+ (recomendado PHP 8.1+)
- Servidor con extensiones comunes de PHP (mysqli, curl, mbstring)

## Instalación
1. Copia esta carpeta `sanasana` dentro de `wp-content/plugins/`.
2. En el dashboard de WordPress, ve a Plugins y activa “SanaSana”.
3. Verifica que los assets (CSS/JS) se estén cargando en el frontend.

## Estructura principal
- `sanasana.php`: bootstrap del plugin.
- `inc/`: controladores y funcionalidad (General, Programs, PriceTable, Questionnaire, TabsTable, Faq, Seo, Resenas).
- `assets/`: estilos, scripts y recursos.
- `vendor/`: dependencias de Composer (el repositorio incluye `vendor/`).

## Desarrollo
- CSS: `assets/css/*`
- JS: `assets/js/*` (hay versiones minificadas disponibles: `*.min.js`)
- Encolado de assets: `inc/General/EnqueueController.php`
- Shortcodes principales:
  - Programs: `inc/Programs/ProgramsShortcode.php`
  - PriceTable: `inc/PriceTable/PriceTableShortcode.php`
  - Tabs: `inc/TabsTable/TabsTableShortcode.php`
  - FAQ: `inc/Faq/FaqShortcode.php`
  - Questionnaire: `inc/Questionnaire/QuestionnaireShortcode.php`

## Seguridad y secretos
- Este repo ignora `wp-config.php` mediante `.gitignore` para evitar subir credenciales.
- Si usas variables de entorno (`.env`), también están ignoradas.

## Flujo de trabajo con Git
- Rama principal: `main`.
- Remoto: `origin` → `https://github.com/CamiloIntelindev/SanaSana.git`.
- Commit convencional sugerido: `tipo(scope): mensaje` (opcional).

## Publicación
- Crear un tag para el release:
  - `v1.0.0` (release inicial)
- (Opcional) Crear el Release en GitHub y adjuntar notas.

## Soporte
- Para issues o mejoras, usa el apartado “Issues” en el repositorio.
