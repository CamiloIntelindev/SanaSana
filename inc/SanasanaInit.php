<?php
/**
* @package  Sanasana
 * 
 * Init plugin
 */
namespace SanasanaInit;

final class SanasanaInit {

    public static function get_services() {
        return [
			
            General\BaseController::class,
            General\EnqueueController::class, // Add this line
            General\GeneralButtonsController::class, // Add this line
            //Form section
            Form\ContactUsController::class, // Add this line
            Form\LearnMoreController::class, // Add this line
            //TabsTable section
            TabsTable\TabsTableController::class, // Add this line
            TabsTable\TabsTableMetaBox::class, // Add this line
            TabsTable\TabsTableShortcode::class, // Add this line
            //Questionnaire section
            Questionnaire\QuestionnaireController::class, // Add this line    
            Questionnaire\QuestionnaireMetaBox::class, // Add this line
            Questionnaire\QuestionnaireShortcode::class, // Add this line
            //Programs section
            Programs\ProgramsCompareController::class, // Add this line
            Programs\ProgramsController::class,// Add this line
            Programs\ProgramsMetaBox::class, // Add this line
            Programs\ProgramsShortcode::class, // Add this line
            Programs\ProgramsSettings::class, // Add this line
            //Seo section
            Seo\SeoOverrideController::class,
            Seo\SchemaController::class, // Add this line
           //NavStyles section
            NavStyles\NavStylesSettings::class, // Add this line
            //Faq section
            Faq\FaqController::class, // Add this line
            Faq\FaqMetaBox::class, // Add this line
            Faq\FaqShortcode::class, // Add this line
            //Resenas section
            Resenas\ResenasController::class, // Add this line
		
            
        ];
    }

    public static function register_services() {
        foreach (self::get_services() as $class) {
            $service = self::instantiate($class);
            if (method_exists($service, 'register')) {
                $service->register();
            }
        }
    }

    private static function instantiate($class) {
        return new $class();
    }
}