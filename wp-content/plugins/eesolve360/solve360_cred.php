<?php

    // REQUIRED Edit with your email address
    define('USER', 'peter@esser-emmerik.nl');
    // REQUIRED Edit with token, Workspace > My Account > API Reference > API Token                             
    define('TOKEN', '44V0jaP5R8H4af5e3fg0W2O9j2P4naB2I2z6tdnb');  
    
    // Configure service gateway object
    require 'Solve360Service.php';
    $solve360Service = new Solve360Service(USER, TOKEN);
?>