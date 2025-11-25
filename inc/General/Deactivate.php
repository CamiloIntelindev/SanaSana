<?php
/**
* @package  Sanasana
 * 
 * DEACTIVATION HOOKS
 */

  namespace SanasanaInit\General;

 class Deactivate
 {
    public static function deactivate(){
        flush_rewrite_rules();
    }

 }