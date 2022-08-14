<?php
/**
 * Liste de Course plugin for WordPress
 *
 * Plugin Name:  Liste de Course
 * Description:  Création de CPT / taxos pour le site Liste De Course.
 * Version:      1.0.0
 * Author:       JRaffi
 *
 */

require __DIR__ . '/vendor-ListeDeCourse/autoload.php';

define("LISTE_DE_COURSE_ENTRY_FILE", __FILE__);

$listeDeCourse = new Liste_de_course\Plugin();
