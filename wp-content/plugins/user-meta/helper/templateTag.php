<?php

function userMetaLogin(){
    global $userMeta;       
    
    return $userMeta->userLoginProcess();
}

function userMetaProfileRegister( $actionType, $formName ){
    global $userMeta;       
    
    return $userMeta->userUpdateRegisterProcess( $actionType, $formName );
}

?>
