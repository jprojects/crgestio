<?php

/**
 * @version     1.0.0 Afigest $
 * @package     Afigest
 * @copyright   Copyright © 2014 - All rights reserved.
 * @license	    GNU/GPL
 * @author	    kim
 * @author mail kim@aficat.com
 * @website	    http://www.aficat.com
 *
*/

defined('_Afi') or die ('restricted access');

if(!$user->getAuth()) {
    $app->setMessage('No tens suficients permisos per accedir a aquest àrea', 'error');
    //$app->redirect($config->site.'/index.php?view=home');
}

if($app->getVar('layout', '') == 'calendar') {
    $app->addScript('https://unpkg.com/js-year-calendar@latest/dist/js-year-calendar.min.js');
    $app->addScript('https://unpkg.com/js-year-calendar@latest/locales/js-year-calendar.ca.js');
    $app->addStylesheet('https://unpkg.com/js-year-calendar@latest/dist/js-year-calendar.min.css');
}

if($app->getVar('layout', '') == '') {
    //hores
    $app->addStylesheet('assets/css/hores.css');
    $app->addScript('assets/js/hores.js');
}

