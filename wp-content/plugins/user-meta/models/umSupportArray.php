<?php

if( !class_exists( 'umSupportArray' ) ) :
class umSupportArray {
    
    function controllersOrder(){
        return array(
            'umPreloadsController',
            'umPreloadsProController',
            'umBackendProfileController',
            'umShortcodesController',
            'umFieldsController',
            'umFormsController',
            'umEmailNotificationController',
            'umUserImportExportController',
            'umSettingsController'          
        );
    }
    
    function enqueueScripts( $scripts=array() ){
        global $userMeta;
        
        $jsUrl = $userMeta->assetsUrl . 'js/';
        $cssUrl = $userMeta->assetsUrl . 'css/';
               
        $list = array(
            'plugin-framework' => array(
                'plugin-framework.js' => '',
                'plugin-framework.css' => '',
            ),   
            'user-meta' => array(
                'user-meta.js' => '',
                'user-meta.css' => '',
            ),   
            'jquery-ui-all' => array(
                'jquery.ui.all.css' => 'jqueryui/',
            ),
            'fileuploader' => array(
                'fileuploader.js'   => 'jquery/',
                'fileuploader.css'  => 'jquery/',
            ),
            'wysiwyg' => array(
                'jquery.wysiwyg.js' => 'jquery/',
                'wysiwyg.image.js'  => 'jquery/',
                'wysiwyg.link.js'   => 'jquery/',
                'wysiwyg.table.js'  => 'jquery/',
                'jquery.wysiwyg.css'=> 'jquery/',
            ),
            'timepicker' => array(
                'jquery-ui-timepicker-addon.js' => 'jqueryui/',
            ),
            'validationEngine' => array(
                'validationEngine.js'   => 'jquery/',
                'validationEngine-en.js'=> 'jquery/',
                'validationEngine.css'  => 'jquery/',
            ),
            'password_strength' => array(
                'jquery.password_strength.js'   => 'jquery/',
            ),
        );
    
        foreach( $scripts as $script ){
            if( isset( $list[$script] ) ){
                foreach( $list[$script]  as $key => $val ){
                    $file = $userMeta->fileinfo( $key );
                    if( $file->ext == 'js' )
                        wp_enqueue_script( $file->name, $jsUrl . $val . $key, array('jquery') );  
                    elseif( $file->ext == 'css' )
                        wp_enqueue_style( $file->name, $cssUrl . $val . $key );  
                }
            }else
                wp_enqueue_script($script);
                       
        }      
                
    }    
    
    
    function umFields(){   
        global $userMeta;
        
        $fieldsList = array(
        
            //WP default fields
            'user_login' => array(
                'title'         => __( 'Username', $userMeta->name ),
                'field_group'   => 'wp_default',  
                'is_free'       => true, 
            ),
            'user_email' => array(
                'title'         => __( 'Email', $userMeta->name ),
                'field_group'   => 'wp_default',
                'is_free'       => true,
            ),   
            'user_pass' => array(
                'title'         => __( 'Password', $userMeta->name ),
                'field_group'   => 'wp_default',
                'is_free'       => true,
            ),   
            /*'user_nicename' => array(
                'title'         => 'Nicename',
                'field_group'     => 'wp_default', 
            ), */            
            'user_url' => array(
                'title'         => __( 'Website', $userMeta->name ),
                'field_group'   => 'wp_default',
                'is_free'       => true,
            ),   
            'display_name' => array(
                'title'         => __( 'Display Name', $userMeta->name ),
                'field_group'   => 'wp_default',  
                'is_free'       => true, 
            ),   
            'nickname' => array(
                'title'         => __( 'Nickname', $userMeta->name ),
                'field_group'   => 'wp_default',   
                'is_free'       => true,
            ),   
            'first_name' => array(
                'title'         => __( 'First Name', $userMeta->name ),
                'field_group'   => 'wp_default',  
                'is_free'       => true,
            ),   
            'last_name' => array(
                'title'         => __( 'Last Name', $userMeta->name ),
                'field_group'   => 'wp_default',   
                'is_free'       => true,
            ),   
            'description' => array(
                'title'         => __( 'Biographical Info', $userMeta->name ),
                'field_group'   => 'wp_default',  
                'is_free'       => true, 
            ),   
            'user_registered' => array(
                'title'         => __( 'Registration Date', $userMeta->name ),
                'field_group'   => 'wp_default',  
                'is_free'       => true,
            ),   
            'role' => array(
                'title'         => __( 'Role', $userMeta->name ),
                'field_group'   => 'wp_default',  
                'is_free'       => true,
            ),   
            'jabber' => array(
                'title'         => __( 'Jabber', $userMeta->name ),
                'field_group'   => 'wp_default',  
                'is_free'       => true,
            ),   
            'aim' => array(
                'title'         => __( 'Aim', $userMeta->name ),
                'field_group'   => 'wp_default', 
                'is_free'       => true, 
            ),   
            'yim' => array(
                'title'         => __( 'Yim', $userMeta->name ),
                'field_group'   => 'wp_default',
                'is_free'       => true,
            ),   
            'user_avatar' => array(
                'title'         => __( 'Avatar', $userMeta->name ),
                'field_group'   => 'wp_default',  
                'is_free'       => true,
            ),             
            
         
            //Standard Fields
            'text' => array(
                'title'         => __( 'Textbox', $userMeta->name ),
                'field_group'   => 'standard',
                'is_free'       => true,   
            ),   
            'textarea' => array(
                'title'         => __( 'Paragraph', $userMeta->name ),
                'field_group'   => 'standard',
                'is_free'       => true,   
            ),   
            'rich_text' => array(
                'title'         => __( 'Rich Text', $userMeta->name ),
                'field_group'   => 'standard',
                'is_free'       => true,   
            ),  
            'hidden' => array(
                'title'         => __( 'Hidden Field', $userMeta->name ),
                'field_group'   => 'standard',
                'is_free'       => true,   
            ),                       
            'select' => array(
                'title'         => __( 'Drop Down', $userMeta->name ),
                'field_group'   => 'standard',
                'is_free'       => true,   
            ),   
            'checkbox' => array(
                'title'         => __( 'Checkbox', $userMeta->name ),
                'field_group'   => 'standard',
                'is_free'       => true,  
            ),   
            'radio' => array(
                'title'         => __( 'Select One (radio)', $userMeta->name ),
                'field_group'   => 'standard',
                'is_free'       => true,  
            ),     
            'datetime' => array(
                'title'         => __( 'Date / Time', $userMeta->name ),
                'field_group'   => 'standard',
                'is_free'       => false,
            ),                      
            'password' => array(
                'title'         => __( 'Password', $userMeta->name ),
                'field_group'   => 'standard', 
                'is_free'       => false,
            ),    
            'email' => array(
                'title'         => __( 'Email', $userMeta->name ),
                'field_group'   => 'standard',
                'is_free'       => false,
            ),             
            'file' => array(
                'title'         => __( 'File Upload', $userMeta->name ),
                'field_group'   => 'standard',
                'is_free'       => false,
            ), 
            'image_url' => array(
                'title'         => __( 'Image URL', $userMeta->name ),
                'field_group'   => 'standard',  
                'is_free'       => false,
            ),                   
            'phone' => array(
                'title'         => __( 'Phone Number', $userMeta->name ),
                'field_group'   => 'standard',  
                'is_free'       => false,
            ), 
            'number' => array(
                'title'         => __( 'Number', $userMeta->name ),
                'field_group'   => 'standard', 
                'is_free'       => false, 
            ), 
            'url' => array(
                'title'         => __( 'Website', $userMeta->name ),
                'field_group'   => 'standard', 
                'is_free'       => false,
            ),                          
            'country' => array(
                'title'         => __( 'Country', $userMeta->name ),
                'field_group'   => 'standard', 
                'is_free'       => false,
            ),      
            /*'scale' => array(
                'title'         => 'Scale',
                'field_group'     => 'standard',  
            ),*/           
            
            
            //Formating Fields
            'page_heading' => array(
                'title'         => __( 'Page Heading', $userMeta->name ),
                'field_group'   => 'formatting',  
                'is_free'       => false,
            ),                                     
            'section_heading' => array(
                'title'         => __( 'Section Heading', $userMeta->name ),
                'field_group'   => 'formatting',  
                'is_free'       => false,
            ),                                                                        
            'html' => array(
                'title'         => __( 'HTML', $userMeta->name ),
                'field_group'   => 'formatting',  
                'is_free'       => false,
            ),                                     
            'captcha' => array(
                'title'         => __( 'Captcha', $userMeta->name ),
                'field_group'   => 'formatting',  
                'is_free'       => false,
            ),                                                         
        );        
        return $fieldsList;                    
    }    
    

    function isValidFormType( $type ){
        $data = array(
            'profile', 'registration', 'both', 'none', 'login'
        );
        return in_array( $type , $data ) ? true : false;
    }
    
    function loginByArray(){
        global $userMeta;
        return array( 
            'user_login' => __( 'Username', $userMeta->name ),
            'user_email' => __( 'Email', $userMeta->name ),
            'user_login_or_email' => __( 'Username or Email', $userMeta->name ),
        );
    }
    
    function defaultSettingsArray( $key=null ){
        $settings = array(
        
            'general' => array(),
        
            'login' => array(
                'login_by'          => 'user_login',
                'login_form'        => "%login_form%\n%lostpassword_form%",
                'loggedin_profile' => array(
                    'administrator' => "<p>Hello %user_login%</p>\n<p>%avatar%</p>\n<p><a href=\"%admin_url%\">Admin Section</a></p>\n<p><a href=\"%logout_url%\">Logout</a></p>",
                    'subscriber'    => "<p>Hello %user_login%</p>\n<p>%avatar%</p>\n<p><a href=\"%logout_url%\">Logout</a></p>",
                ),
            ),  
            
            'registration' => array(
                'user_activation'    => 'auto_active',
            ), 
              
            'redirection'   => array(
                'login'     => array(
                    'administrator' => 'dashboard',
                    'subscriber'    => 'default',
                ),
                'logout'    => array(
                    'administrator' => 'default',
                    'subscriber'    => 'default',
                ),
                'registration'  => array(
                    'administrator' => 'default',
                    'subscriber'    => 'default',
                ),                
            ),
            
            'backend_profile'   => array(), 
            
            'misc'  => array(),
            
                
        );
        
        if( $key )
            return @$settings[ $key ];
        return $settings;        
    }
    
    function defaultEmailsArray( $key = null ){
        global $userMeta;

        $emails = array(
        
            'registration'  => array(
                'user_email'    => array(
                    'subject'   => '[%site_title%] Your username and password',
                    'body'      => "Username: %user_login% \r\nE-mail: %user_email% \r\nPassword: %password% \r\n\r\nLogin Url: %login_url%",

                ),
                'admin_email'    => array(
                    'subject'   => '[%site_title%] New User Registration',
                    'body'      => "Username: %user_login% \r\nEmail: %user_email% \r\n",
                ),                
            ),
            
            'activation'  => array(
                'user_email'    => array(
                    'subject'   => '[%site_title%] User Activated',
                    'body'      => "Congratulations! \r\n\r\nYour account is activated. You can login with your username and password. \r\n\r\nLogin Url: %login_url%",
                ),               
            ),
            
            'deactivation'  => array(
                'user_email'    => array(
                    'subject'   => '[%site_title%] User Deactivated',
                    'body'      => "Your account is deactivated by administrator. You can not login anymore to [%site_title%].",
                ),               
            ),
            
            'email_verification'  => array(
                'user_email'    => array(
                    'subject'   => '[%site_title%] Email verified',
                    'body'      => "Your email [%user_email%] is successfully verified on [%site_title%].",
                ),  
                'admin_email'    => array(
                    'subject'   => '[%site_title%] Email verified',
                    'body'      => "User email [%user_email%] is successfully verified on [%site_title%].",
                ),                                                
            ),                                    
            
            'lostpassword'  => array(
                'user_email'    => array(
                    'subject'   => "[%site_title%] Password Reset",
                    'body'      => "Someone requested that the password be reset for the following account:\r\n\r\n%site_url% \r\n\r\nUsername: %user_login% \r\n\r\nIf this was a mistake, just ignore this email and nothing will happen. \r\n\r\nTo reset your password, visit the following address: \r\n\r\n%reset_password_link% \r\n",
                ),                
            ), 
            
            'profile_update'  => array(
                'user_email'    => array(
                    'subject'   => '[%site_title%] Profile Updated',
                    'body'      => "Hi %display_name%,\r\n\r\nYour profile have updated on site: %site_url%",
                    'um_disable'=> true,
                ),
                'admin_email'    => array(
                    'subject'   => '[%site_title%] Profile Updated',
                    'body'      => "Profile updated for Username: %user_login% ",
                    'um_disable'=> true,
                ),                
            ),            
            
        );
        
        if( $key )
            return @$emails[ $key ];
        return $emails;         
    }
    
    function runLocalization(){
        global $userMeta, $userMetaCache; 
               
        if( empty( $userMetaCache->localizedStrings ) ){                  
            $userMetaCache->localizedStrings = array(
                'user-meta' => array(
                    'get_pro_link'=> sprintf( __( 'Get pro version from %s to use this feature.', $userMeta->name ), $userMeta->website ),
                    'please_wait'=> __( 'Please Wait...', $userMeta->name ),
                ),
                'fileuploader' => array(
                    'upload'        => __( 'Upload', $userMeta->name ),
                    'drop'          => __( 'Drop files here to upload', $userMeta->name ),
                    'cancel'        => __( 'Cancel', $userMeta->name ),
                    'failed'        => __( 'Failed', $userMeta->name ),
                    'invalid_extension' => sprintf( __( '%1$s has invalid extension. Only %2$s are allowed.', $userMeta->name ), '{file}', '{extensions}' ),
                    'too_large'         => sprintf( __( '%1$s is too large, maximum file size is %2$s.', $userMeta->name ), '{file}', '{sizeLimit}' ),
                    'empty_file'        => sprintf( __( '%s is empty, please select files again without it.', $userMeta->name ), '{file}' ),
                ),
                'jquery.password_strength' => array(
                    'too_weak'      => __( 'Too weak', $userMeta->name ),
                    'weak'          => __( 'Weak password', $userMeta->name ),
                    'normal'        => __( 'Normal strength', $userMeta->name ),
                    'strong'        => __( 'Strong password', $userMeta->name ),
                    'very_strong'   => __( 'Very strong password', $userMeta->name ),
                ),
                'validationEngine-en' => array(
                    'required_field'    => __( '* This field is required', $userMeta->name ),
                    'required_option'   => __( '* Please select an option', $userMeta->name ),
                    'required_checkbox' => __( '* This checkbox is required', $userMeta->name ),
                    'min'               => __( '* Minimum ', $userMeta->name ),
                    'max'               => __( '* Maximum ', $userMeta->name ),
                    'char_allowed'      => __( ' characters allowed', $userMeta->name ),
                    'min_val'           => __( '* Minimum value is ', $userMeta->name ),
                    'max_val'           => __( '* Maximum value is ', $userMeta->name ),
                    'past'              => __( '* Date prior to ', $userMeta->name ),
                    'future'            => __( '* Date past ', $userMeta->name ),
                    'options_allowed'   => __( ' options allowed', $userMeta->name ),
                    'please_select'     => __( '* Please select ', $userMeta->name ),
                    'options'           => __( ' options', $userMeta->name ),
                    'not_equals'        => __( '* Fields do not match', $userMeta->name ),
                    'invalid_phone'     => __( '* Invalid phone number', $userMeta->name ),
                    'invalid_email'     => __( '* Invalid email address', $userMeta->name ),
                    'invalid_integer'   => __( '* Not a valid integer', $userMeta->name ),
                    'invalid_number'    => __( '* Invalid floating decimal number', $userMeta->name ),
                    'invalid_date'      => __( '* Invalid date, must be in YYYY-MM-DD format', $userMeta->name ),
                    'invalid_time'      => __( '* Invalid time, must be in hh:mm:ss format', $userMeta->name ),
                    'invalid_datetime'  => __( '* Invalid datetime, must be in YYYY-MM-DD hh:mm:ss format', $userMeta->name ),
                    'invalid_ip'        => __( '* Invalid IP address', $userMeta->name ),
                    'invalid_url'       => __( '* Invalid URL', $userMeta->name ),
                    'numbers_only'      => __( '* Numbers only', $userMeta->name ),
                    'letters_only'      => __( '* Letters only', $userMeta->name ),
                    'no_special_char'   => __( '* No special characters allowed', $userMeta->name ),
                    'user_exists'       => __( '* This user is already taken', $userMeta->name ),
                ),

            );
        }
        
        foreach( $userMetaCache->localizedStrings as $scriptName => $data ){
            $objectName = str_replace( array( '.', '-' ), '_', $scriptName );
            wp_localize_script( $scriptName, $objectName, $data );
        }
     
    }    
    
}
endif;
