<?php
/**
* @package  Sanasana
 * 
 * ACTIVATION HOOKS
 */

 namespace SanasanaInit\General;

 class Activate
 {
    public static function activate(){
        flush_rewrite_rules();
    }

 }