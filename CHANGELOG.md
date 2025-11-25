# Changelog

All notable changes to this project will be documented in this file.

The format is based on Keep a Changelog and this project adheres to Semantic Versioning.

## [1.0.0] - 2025-11-25
### Added
- Initial public release of the SanaSana WordPress plugin.
- Shortcodes and controllers for:
  - Programs (`inc/Programs/ProgramsShortcode.php`, `ProgramsController.php`, `ProgramsSettings.php`, `ProgramsMetaBox.php`, `ProgramsCompareController.php`).
  - Price Tables (`inc/PriceTable/PriceTableShortcode.php`, `PriceTableController.php`, `PriceTableSettings.php`, `PriceTableMetaBox.php`).
  - Tabs Table (`inc/TabsTable/TabsTableShortcode.php`, `TabsTableController.php`, `TabsTableMetaBox.php`, `FaqTabsTableMetaBox.php`).
  - FAQ (`inc/Faq/FaqShortcode.php`, `FaqController.php`, `FaqMetaBox.php`).
  - Questionnaire (`inc/Questionnaire/*`).
  - Reseñas (`inc/Resenas/ResenasController.php`).
  - SEO utilities (`inc/Seo/*` incluyendo `SchemaController.php`, `SeoMetaboxesController.php`, `SeoOverrideController.php`, `SeoperformaceController.php`).
- General controllers: Activate, Deactivate, Enqueue, Cache, Fonts, Buttons, LazyLoad, Contact Form.
- Frontend assets: CSS/JS y recursos gráficos en `assets/`.
- Composer autoload en `vendor/`.

### Security
- `wp-config.php` y `.env*` ignorados en el repositorio para evitar filtrar credenciales.

[1.0.0]: https://github.com/CamiloIntelindev/SanaSana/releases/tag/v1.0.0
