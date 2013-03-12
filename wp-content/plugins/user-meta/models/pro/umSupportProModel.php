<?php

if( !class_exists( 'umSupportProModel' ) ) :
class umSupportProModel {    
        
    function generateLoginForm(){
        global $userMeta;
        
        if( is_user_logged_in() )
            return $this->loginResponse();         
        
        $loginSettings = $userMeta->getSettings( 'login' );
        
        if( false === strpos( @$loginSettings[ 'login_form' ], '%login_form%' ) )
            @$loginSettings[ 'login_form' ] .= '%login_form%';

        return self::convertUserContent( null, $loginSettings[ 'login_form' ] );
    }
    
    /**
     * Generate login form. show loginResponse() if user logedin
     * 
     * @author  Khaled Saikat
     * @since   1.1.2
     * @param   string : shortcode, widget, template.
     * @return  string containing the form
     */
    function lgoinForm(){
        global $userMeta;
        
        //if( is_user_logged_in() )
            //return $this->loginResponse();     
        
        $login = $userMeta->getSettings( 'login' );        
        $title  = $userMeta->loginByArray();
        
        return $userMeta->renderPro( 'loginForm', array(
            'loginBy'           => @$login[ 'login_by' ] ,
            'loginTitle'        => @$title[ $login[ 'login_by' ] ],
            'disableAjax'       => @$login[ 'disable_ajax' ],
        ), 'login' );
    }
    
    /**
     * Handle resetPassword request, key validation, password reset
     */
    function lostPasswordForm( $visibility=null ){
        global $userMeta;
        
        //if( is_user_logged_in() ) return;        
        
        $login = $userMeta->getSettings( 'login' );
        
        if( !$visibility )
            $visibility = @$login[ 'disable_ajax' ] ? 'show' : 'hide';
        
        $html = null;
        if( !@$_REQUEST['is_ajax'] && @$_REQUEST['method_name'] == 'lostpassword' )
            $html .= $userMeta->ajaxLostpassword();         
                
        $html .= $userMeta->renderPro( 'lostPasswordForm', array(
            'disableAjax'   => @$login[ 'disable_ajax' ],
            'visibility'    => $visibility,
        ), 'login' ); 
          
        return $html;     
    }    

    /**
     * LoggedIn Profile.
     * 
     * @author  Khaled Saikat
     * @uses    lgoinForm()
     * @since   1.1.2
     * @param   string : shortcode, widget, template.
     * @return  string containing the form
     */    
    function loginResponse( $user = null ){
        global $userMeta;
        
        if( empty( $user ) )
            $user = wp_get_current_user();
        
        $role = $userMeta->getUserRole( $user->ID );        
        $login = $userMeta->getSettings( 'login' );
        
        return $this->convertUserContent( $user, @$login[ 'loggedin_profile' ][ $role ]  );
    }
    
    
    function resetPasswordForm(){
        global $userMeta;
        $action = @$_GET[ 'action' ];
        
        if( !in_array( $action, array( 'resetpass', 'rp' ) ) )
            return false;
        
        $user = $userMeta->check_password_reset_key( @$_GET['key'], @$_GET['login'] );                    
    	if ( !is_wp_error($user) ){
            if( isset( $_POST['pass1'] ) && isset( $_POST['pass2'] ) ){
                if ( isset($_POST['pass1']) && $_POST['pass1'] != $_POST['pass2'] ) 
                    $errors = new WP_Error('password_reset_mismatch', __('The passwords do not match.'));
                elseif ( isset($_POST['pass1']) && !empty($_POST['pass1']) ){
                    $userMeta->reset_password($user, $_POST['pass1']);
                    return $userMeta->showMessage( __( 'Your password has been reset.', $userMeta->name ), 'success', false );
                }                              
            }         	   
    	}else
            return $userMeta->showError( $user->get_error_message(), false );
            
                      
        return $userMeta->renderPro( 'resetPasswordForm', array(
            'error'     => @$errors,
        ), 'login' );
    }
    
    function emailVerification(){
        global $userMeta;
        
        $email  = @$_REQUEST[ 'email' ];
        $key    = @$_REQUEST[ 'key' ];
        $key    = urldecode( $key );
        
        if( !$email || !$key )
            return $userMeta->showError(  __( 'Invalid Parameter', $userMeta->name )  );
        
        $user = get_user_by( 'email', $email );  
        if( !$user )
            return $userMeta->showError(  __( 'Email not found', $userMeta->name )  );
            
        if( get_user_meta( $user->ID, $userMeta->prefixLong . 'user_status', true ) == 'active' )
            return $userMeta->showMessage( __( 'User already activated', $userMeta->name ) );
        
        $preSavedKey = get_user_meta( $user->ID, $userMeta->prefixLong . 'email_verification_code', true );        
        if( $preSavedKey == $key){
            $registration       = $userMeta->getSettings( 'registration' );
            $user_activation    = $registration[ 'user_activation' ];
            
            if( $user_activation == 'email_verification' ){
                $status = 'active';
            }elseif( $user_activation == 'both_email_admin' ){
                $status = 'inactive';
            }
            
            $status = $status == 'active' ? 'active' : @$status;                
            update_user_meta( $user->ID, $userMeta->prefixLong . 'user_status', $status );
            do_action( 'user_meta_email_verified', $user->ID );
            
            return $userMeta->showMessage( __( 'email is successfully verified ', $userMeta->name ) );         
        }else
            return $userMeta->showError(  __( 'Invalid Key', $userMeta->name )  );     
    }
    
    /**
     * Do login if user not logged on.
     * @return onSuccess : redirect_url | onFailed : WP_Error or false
     */
    function doLogin( $doRedirect = false ){
        global $userMeta;
        
        if( is_user_logged_in() )
            return false;
            
        $userName   = $userMeta->getUsernameForLogin();
        $userPass   = @$_REQUEST['user_pass'];
        $response   = wp_authenticate( $userName, $userPass );

        if( !is_wp_error( $response ) ){
            $user = wp_signon( array(
                'user_login'    => $userName,
                'user_password' => $userPass,
                'remember'      => @$_REQUEST['remember'] ? true : false,
            ), false );         
            
            $redirect_to    = $userMeta->applyFiltersLoginRedirection( $response );
            $redirect_to    = $redirect_to ? $redirect_to : home_url();            
            if( $doRedirect ){
                wp_redirect( $redirect_to );
                exit();                
            }
                        
            $response->redirect_to = $redirect_to;                     
        }
            
        return $response;        
    }
    
    
    /**
     * fined user_login form user_login or user_email
     */
    function getUsernameForLogin(){
        $loginBy    = @$_REQUEST[ 'login_by' ];
        $userLogin  = @$_REQUEST[ 'user_login' ];     
        
        if( @$loginBy == 'user_login_or_email' ){
            $user = get_user_by( 'email', $userLogin );
            if( $user === false )
                $user = get_user_by( 'login', $userLogin );            
        }elseif( @$loginBy == 'user_email' )
            $user = get_user_by( 'email', $userLogin );
        else
            return $userLogin;
        
        return $user ? $user->user_login : $userLogin;
    }
    
    /**
     * Determine, is it login request. Useed with http post request
     */
    function isLoginRequest(){                
        if( !is_user_logged_in() && @$_POST['action'] == 'um_login' && @$_POST['action_type'] == 'login' && @$_REQUEST['pf_nonce'] && isset( $_POST['user_login'] ) && isset( $_POST['user_pass'] ) )
            return true;
        return false;
    }
    
    function disableAdminRow( $id ){
        if( in_array( $id, array( 'heading_0', 'heading_1', 'heading_2', 'heading_3' ) ) ){
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function(){
                    id = <?php echo str_replace( 'heading_', '', $id ); ?>;
                    jQuery( "h3:eq(" + id + ")" ).hide();
                });
            </script>               
            <?php             
        }else{
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function(){
                    jQuery( "#<?php echo $id; ?>" ).parents( "tr" ).hide();
                });
            </script>               
            <?php               
        }
    }
    
    function isResetPasswordRequest(){
        $action = @$_GET[ 'action' ];
        if( in_array( $action, array( 'lostpassword', 'retrievepassword', 'resetpass', 'rp' ) ) )
            return true;
        return false;
        
    }
    
    function registerUser( $userData, $fileCache=null ){
        global $userMeta;
        
        /// $userData: array. 
        $userData = apply_filters( 'user_meta_pre_user_register', $userData );
                
        $response = $userMeta->insertUser( $userData );  
        if( is_wp_error( $response ) )
            return $userMeta->showError( $response );
        
        /// Allow to populate form data based on DB instead of $_REQUEST
        $userMeta->showDataFromDB = true;         
            
        $registrationSettings = $userMeta->getSettings( 'registration' );
        $activation = $registrationSettings[ 'user_activation' ];
        if( $activation == 'auto_active' )
            $msg    = __( 'Registration successfully completed.', $userMeta->name );
        elseif( $activation == 'email_verification' )
            $msg    = __( 'We have sent you a verification link to your email. Please complete your registration by clicking the link.', $userMeta->name );
        elseif( $activation == 'admin_approval' )
            $msg    = __( 'Please wait until an admin approve your account.', $userMeta->name );
        elseif( $activation == 'both_email_admin' )
            $msg    = __( 'We have sent you a verification link to your email. Please verify your email by clicking the link and wait for admin approval.', $userMeta->name );
            
        if( $fileCache )
            $userMeta->removeCache( 'image_cache', $fileCache, false );
        
        do_action( 'user_meta_after_user_register', (object) $response );                  
        
        $html = $userMeta->showMessage( $msg );
        
        $redirect_to = $userMeta->applyFiltersRegistrationRedirection( $response[ 'ID' ] );
        if( $redirect_to )
            $html .= $userMeta->jsRedirect( $redirect_to );         
        
        $html = "<div action_type=\"registration\">" . $html . "</div>";    
        return $userMeta->printAjaxOutput( $html );                          
    }
    
    
    function isInvalidateCaptcha(){
         global $userMeta;
         
         // Checking existance of captcha field
         if( !isset($_POST["recaptcha_challenge_field"]) )
            return false;
            
        // If key are not set then no validation
        $general    = $userMeta->getSettings( 'general' );
        if( ( !@$general['recaptcha_public_key'] ) || ( !@$general['recaptcha_private_key'] ) )
            return false;       
        
        //if( ! function_exists( 'recaptcha_check_answer' ) )             
            require_once( $userMeta->pluginPath . '/framework/helper/recaptchalib.php');
        
        $privateKey = null;
        if( isset( $general['recaptcha_private_key'] ) )
            $privateKey = $general['recaptcha_private_key'];
        
        $resp = recaptcha_check_answer ($privateKey,
                                    $_SERVER["REMOTE_ADDR"],
                                    $_POST["recaptcha_challenge_field"],
                                    $_POST["recaptcha_response_field"]);
        if (!$resp->is_valid){
            $error = $resp->error;
            if( $error == 'incorrect-captcha-sol' )
                $error = __( 'Incorrect Captcha Code', $userMeta->name );
            return $error;
        }
                   
        return false;         
    }    
    
    /**
     * Convert content for user provided by %field_name%
     * Supported Extra filter: blog_title, blog_url, avatar, logout_link, admin_link
     * @param $user: WP_User object
     * @param $data: (string) string for convertion
     * @return (string) converted string
     */
    function convertUserContent( $user, $data, $extra=array() ){
        preg_match_all( '/\%[a-zA-Z0-9_-]+\%/i', $data, $matches); 
        if( is_array( @$matches[0] ) ){
            $patterns = $matches[0];
            $replacements = array();
            foreach( $patterns as $key => $pattern ){
                $fieldName = strtolower( trim( $pattern, '%' ) );
                if( $fieldName == 'site_title' )
                    $replacements[ $key ] = get_bloginfo( 'name' );
                elseif( $fieldName == 'site_url' )
                    $replacements[ $key ] = site_url();
                elseif( $fieldName == 'avatar' )
                    $replacements[ $key ] = get_avatar( $user->ID );
                elseif( $fieldName == 'login_url' )
                    $replacements[ $key ] = wp_login_url();                    
                elseif( $fieldName == 'logout_url' )
                    $replacements[ $key ] = wp_logout_url();
                elseif( $fieldName == 'lostpassword_url' )
                    $replacements[ $key ] = wp_lostpassword_url();                                         
                elseif( $fieldName == 'admin_url' )
                    $replacements[ $key ] = admin_url();
                elseif( $fieldName == 'activation_url' )
                    $replacements[ $key ] = self::userActivationUrl( 'activate', $user->ID, false );
                elseif( $fieldName == 'email_verification_url' )
                    $replacements[ $key ] = self::emailVerificationUrl( $user );
                elseif( $fieldName == 'login_form' )
                    $replacements[ $key ] = self::lgoinForm();
                elseif( $fieldName == 'lostpassword_form' )
                    $replacements[ $key ] = self::lostPasswordForm();
                else
                    $replacements[ $key ] = @$user->$fieldName;
            }
            $data = str_replace($patterns, $replacements, $data);
        }    

        return $data;     
    }
    
    // TODO: referer
    /**
     * Get redirection url from settings.
     * @param $redirect_to: get $redirect_to from filter.
     * @param $action: login, logout or registration
     * @param $role: role name
     * @return $redirect_to: url
     */
    function getRedirectionUrl( $redirect_to, $action, $role=null ){
        global $userMeta, $user_ID;
        
        if( !$role )
            $role = $userMeta->getUserRole( $user_ID );
        
        $redirection       = $userMeta->getSettings( 'redirection' );
        
        if( @$redirection[ 'disable' ] )
            return $redirect_to;
        
        $redirectionType = @$redirection[ $action ][ $role ];
              
        
        if( $redirectionType == 'referer' ){
            if( ! empty( $_REQUEST[ '_wp_http_referer' ] ) )
                $redirect_to = 'http://' . $_SERVER[ 'HTTP_HOST' ] . $_REQUEST[ '_wp_http_referer' ];
        }
        elseif( $redirectionType == 'home' )
            $redirect_to = home_url();
        elseif( $redirectionType == 'profile' )
            $redirect_to = $userMeta->getProfileLink();
        elseif( $redirectionType == 'dashboard' )
            $redirect_to = admin_url();
        elseif( $redirectionType == 'login_page' )
            $redirect_to = wp_login_url();         
        elseif( $redirectionType == 'custom_url' ){
            if( isset( $redirection[ $action . '_url' ][ $role ] ) )
                $redirect_to = $redirection[ $action . '_url' ][ $role ];
        } 
        
        return $redirect_to;    
    }  
    
    /**
     * Generate activation/deactivation link with or without nonce.
     */
    function userActivationUrl( $action, $userID, $addNonce = true ){
        $url    = admin_url( 'users.php' );
        $url    = add_query_arg( array(
			'action'	=>	$action,
			'user'		=>	$userID
		), $url);
        
        if( $addNonce )
		  $url  =	wp_nonce_url( $url, 'um_activation' ); 
           
        return $url;      
    }  
    
    /**
     * Generate activation/deactivation link with or without nonce.
     */
    function emailVerificationUrl( $user ){
        global $userMeta;
        $login  = $userMeta->getSettings( 'login' );        
        if( !$login[ 'login_page' ] ) return;
       
        $hash   = get_user_meta( $user->ID, $userMeta->prefixLong . 'email_verification_code', true ); 
        if( !$hash ){
            $hash   = wp_hash_password( $user->user_email );
            update_user_meta( $user->ID, $userMeta->prefixLong . 'email_verification_code', $hash );
        }
               
        $url    = get_permalink( $login[ 'login_page' ] );
        $url    = add_query_arg( array(
			'action'	=> 'email_verification',
                        'email'         => $user->user_email,
			'key'		=> urlencode( $hash ),
	), $url);
                           
        return $url;      
    }       
    
    
    /**
     * Generate role based email template
     * @param $slugs : array containing two value without keys. e.g array( 'registration', 'user_email' )
     * @param $data  : array containing data to populate
     * @return html
     */
    function buildRolesEmailTabs( $slugs=array(), $data=array() ){
        global $userMeta;        
        $roles  = $userMeta->getRoleList();
        
        foreach( $roles as $key => $val ){
            $forms[ $key ] = $userMeta->renderPro( 'singleEmailForm', array(
                'slug'      => "{$slugs[0]}[{$slugs[1]}][$key]",
                'from_email'=> @$data[ $slugs[0] ][ $slugs[1] ][ $key ][ 'from_email' ],
                'from_name' => @$data[ $slugs[0] ][ $slugs[1] ][ $key ][ 'from_name' ],
                'format'    => @$data[ $slugs[0] ][ $slugs[1] ][ $key ][ 'format' ],
                'subject'   => @$data[ $slugs[0] ][ $slugs[1] ][ $key ][ 'subject' ],
                'body'      => @$data[ $slugs[0] ][ $slugs[1] ][ $key ][ 'body' ],
                /*'after_form'=> $userMeta->createInput( null, 'checkbox', array(
                                    'label'         => __( 'Copy this form data to all others role', $userMeta->name ),
                                    'enclose'       => 'p',
                                    'onclick'       => 'copyFormData(this)',
                                    'class'         => 'asdf',
                                ) ),  */                  
            ), 'email' );
        }   
        
                     
        $html = $userMeta->jQueryRolesTab( "{$slugs[0]}-{$slugs[1]}", $roles, $forms );        
        $html .= $userMeta->createInput( "{$slugs[0]}[{$slugs[1]}][um_disable]", 'checkbox', array(
            'label'         => __( 'Disable this notification', $userMeta->name ),
            'value'         => @$data[ $slugs[0] ][ $slugs[1] ][ 'um_disable' ] ? true : false,
            'enclose'       => 'p',
        ) ); 
        
        return $html;
    }     
    
    /**
     * Callback hook for "pre_user_query". Filter users by registration date
     */
    function filterRegistrationDate( $query ){
            global $wpdb;           
            
            if ( ! empty( $_REQUEST['start_date'] ) )
                $query->query_where = $query->query_where . $wpdb->prepare( " AND $wpdb->users.user_registered >= %s", $_REQUEST['start_date'] );

            if ( ! empty( $_REQUEST['end_date'] ) )
                $query->query_where = $query->query_where . $wpdb->prepare( " AND $wpdb->users.user_registered <= %s", $_REQUEST['end_date'] );
                       
            return $query;        
    }    



}
endif;
