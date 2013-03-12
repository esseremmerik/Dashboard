<?php

if( !class_exists( 'umMethods' ) ) :
class umMethods {    
        
    function userUpdateRegisterProcess( $actionType, $formName ){
        global $userMeta, $user_ID;

        $userMeta->enqueueScripts( array( 
            'plugin-framework', 
            'user-meta',           
            'jquery-ui-all',
            'fileuploader',
            'wysiwyg',
            'jquery-ui-datepicker',
            'jquery-ui-slider',
            'timepicker',
            'validationEngine',
            'password_strength',
        ) );                      
        $userMeta->runLocalization();          
        
        $actionType = strtolower( $actionType );
                            
        if( empty( $actionType ) )
            return $userMeta->showError( __( 'Please provide a name of action type.', $userMeta->name ), false );
        
        if( empty( $formName ) )
            return $userMeta->showError( __( 'Please provide a form name.', $userMeta->name ), false );        
        
        if( !$userMeta->isValidFormType( $actionType ) )
            return $userMeta->showError( sprintf( __( 'Sorry. type="%s" is not supported.', $userMeta->name ), $actionType ), false );              

        if( ! (  $userMeta->isPro() && $userMeta->isPro ) ){
            if( !($actionType == 'profile' || $actionType == 'none') )
                return $userMeta->showError( "type='$actionType' is only supported, in pro version. Get " . $userMeta->getProLink( 'User Meta Pro' ), "info", false );                                    
        }
                             
        $userID  = $user_ID;
        $isAdmin = $userMeta->isAdmin();
        
        // Determine Form type
        if( $actionType == 'both' )
            $actionType = $user_ID ? 'profile' : 'registration';        
            
        // Checking Permission
        if( $actionType == 'profile' OR $actionType == 'none' ){
            if( !$user_ID )
                return $userMeta->showMessage( __( 'You do not have permission to access this page.', $userMeta->name ), 'info', false );
        }elseif( $actionType == 'registration' ) {
            if( $user_ID AND !$isAdmin )
                return $userMeta->showMessage( sprintf( __( 'You are already registered. See your <a href="%s">profile</a>', $userMeta->name ), $userMeta->getProfileLink() ) , 'info' );
            elseif( !get_option( 'users_can_register' ) )
                return $userMeta->showError( __( 'User registration is currently not allowed.', $userMeta->name ), false);            
        }
                     
        // Process submited request for non-ajax call
        $output = null;
        if( in_array( @$_REQUEST['action_type'], array( 'profile', 'registration' ) ) ) {
            if( @$_REQUEST['form_key'] == $formName && @$_REQUEST['action_type'] == $actionType )
                $output = $userMeta->ajaxInsertUser();  
        }
        
        
        // Loading $userID as admin request
        if( $isAdmin ){
            if( isset($_REQUEST['user_id']) )
                $userID = $_REQUEST['user_id'];
        }            
        
        $fields     = $userMeta->getData( 'fields' );
        $forms      = $userMeta->getData( 'forms' );                     
        $form       = isset( $forms[$formName] ) ? $forms[$formName] : null;
                    
        $userData   = get_userdata( $userID );
                   
        $output .= $userMeta->renderPro( 'generateForm', array( 
            'actionType'=> $actionType,
            'fields'    => $fields, 
            'form'      => $form, 
            'userID'    => $userID,
            'userData'  => $userData,
        ) );
        
        return $output;        
    }
    
    function userLoginProcess(){
        global $userMeta;
        
        $userMeta->enqueueScripts( array( 'plugin-framework', 'user-meta' ) );
        $userMeta->runLocalization();
        
        $output = null;
        if( $userMeta->isLoginRequest() )
            $output .= $userMeta->ajaxLogin();

        $output .= $userMeta->generateLoginForm();        
        return $output;
    }
             
}
endif;
