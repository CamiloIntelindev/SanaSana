<?php
/**
 * @package PriceTable
 *
 */

namespace SanasanaInit\Seo;

class SchemaController
{    
    public function register() {
        //add_action('wp_head', [$this, 'schema_front_page_es']);
        //add_action('wp_head', [$this, 'schema_about_page_es']);
        //add_action('wp_head', [$this, 'schema_faq_page_es']);
    }
    //Home page schema for Spanish
    public function schema_front_page_es(){
        if (is_front_page() || is_home()) {
        ?>
        <script type="application/ld+json">
        {
          "@context": "https://schema.org",
          "@type": "FAQPage",
          "mainEntity": [
            {
              "@type": "Question",
              "name": "¿Qué incluye la afiliación a Sana Sana?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "La afiliación incluye acceso a programas de salud, valoración médica, descuentos y seguimiento personalizado. Consulta todos los beneficios en nuestra sección de Programas."
              }
            },
            {
              "@type": "Question",
              "name": "¿Cuál es el periodo de carencia?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Los beneficios en el Hospital Cima son inmediatos tras la afiliación. Para proveedores externos, el periodo de carencia es de 3 días."
              }
            },
            {
              "@type": "Question",
              "name": "¿Es un seguro?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "No, Sana Sana no es un seguro. Es un programa de salud preventiva, bienestar y acompañamiento personalizado para todas las etapas de tu vida."
              }
            },
            {
              "@type": "Question",
              "name": "¿Cuál es el método de pago?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Los pagos se realizan mediante rebajo automático con la tarjeta registrada al momento de la afiliación."
              }
            },
            {
              "@type": "Question",
              "name": "¿Qué incluye la valoración integral de salud y bienestar?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Ofrecemos valoraciones médicas esenciales, plus, para adolescentes y pediátricas, incluyendo exámenes de laboratorio, consulta médica extendida y precios preferenciales para afiliados."
              }
            }
          ]
        }
        </script>

        <?php
        }
    }
    //About page schema for Spanish
    public function schema_about_page_es() {
        if (is_page('nosotros')) {
            ?>
            <script type="application/ld+json">
            {
              "@context": "https://schema.org",
              "@type": "AboutPage",
              "mainEntityOfPage": {
                "@type": "WebPage",
                "@id": "https://sanasana.com/nosotros/"
              },
              "name": "Equipo Sana Sana | Expertos en salud preventiva para Latinoamérica",
              "description": "Conoce al equipo de Sana Sana: expertos en salud preventiva, medicina funcional y bienestar integral. Descubre qué nos hace únicos y por qué miles de afiliados confían en nosotros para vivir más y mejor.",
              "publisher": {
                "@type": "Organization",
                "name": "Sana Sana",
                "url": "https://sanasana.com",
                "logo": {
                  "@type": "ImageObject",
                  "url": "https://sanasanastoragews.blob.core.windows.net/blobwsdev/2025/02/logo-black.png"
                }
              }
            }
            </script>
            <?php
        }
    }
    //FAQ schema for Spanish
    public function schema_faq_page_es() {
        if (is_page('preguntas-frecuentes')) {
            ?>
            <script type="application/ld+json">
            {
              "@context": "https://schema.org",
              "@type": "FAQPage",
              "mainEntity": [
                {
                  "@type": "Question",
                  "name": "¿Qué incluye la afiliación a Sana Sana?",
                  "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "La afiliación incluye programas de salud preventiva, descuentos exclusivos, valoración médica anual y más. Consulta la sección Programas para ver el detalle completo."
                  }
                },
                {
                  "@type": "Question",
                  "name": "¿Cómo se realiza la afiliación?",
                  "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Podés afiliarte fácilmente en sanasana.com, seleccionando el programa deseado, eligiendo el tipo de pago, completando el formulario y finalizando con el pago online."
                  }
                },
                {
                  "@type": "Question",
                  "name": "¿Cuál es el periodo de carencia?",
                  "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Beneficios inmediatos en Hospital CIMA; con proveedores externos, aplica un periodo de carencia de 3 días."
                  }
                },
                {
                  "@type": "Question",
                  "name": "¿Cuál es el método de pago?",
                  "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Los pagos se realizan mediante débito automático con la tarjeta registrada al momento de la afiliación."
                  }
                },
                {
                  "@type": "Question",
                  "name": "¿Es un seguro?",
                  "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "No, Sana Sana no es un seguro. Es un programa integral de salud preventiva y bienestar que acompaña a sus afiliados en cada etapa de su vida."
                  }
                }
              ]
            }
            </script>
            <?php
        }
    }


}