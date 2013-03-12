<?php

    // REQUIRED Edit with your email address
    define('USER', 'peter@esser-emmerik.nl');
    // REQUIRED Edit with token, Workspace > My Account > API Reference > API Token                             
    define('TOKEN', '44V0jaP5R8H4af5e3fg0W2O9j2P4naB2I2z6tdnb');  
    
    //Field id's van Projecblogs
    define('TITLE_PROJECTBLOGS', '8994423');
    define('ASSIGNEDTO_PROJECTBLOGS', '8994424');
    define('BACKGROUND_PROJECTBLOGS', '8994425');
    define('COMPANY_PROJECTBLOGS', '8994426');
    define('DESCRIPTION_PROJECTBLOGS', '8994430');
    define('RELATEDTO_PROJECTBLOGS', '8994431');
    define('ARCHIVE_PROJECTBLOGS', '8994432');
    define('LOGO_PROJECTBLOGS', '8994433');
    
    
    
    // Configure service gateway object
    define('BASE_PATH', dirname(__FILE__) . "/");
    require_once BASE_PATH . 'Solve360Service.php';
    //$solve360Service = new Solve360Service(USER, TOKEN);
?>