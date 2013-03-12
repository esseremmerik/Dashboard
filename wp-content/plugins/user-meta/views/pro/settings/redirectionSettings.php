<?php
global $userMeta;
// Expected: $redirection
// field slug: redirection

$roles = $userMeta->getRoleList();

$html = null;

$html .= '<h4>'. __( 'User Redirection Settings', $userMeta->name ) .'</h4>';  
$html .= $userMeta->createInput( 'redirection[disable]', 'checkbox', array(
    'label' => ' ' . __( 'Disable redirection feature' ),
    'value' => @$redirection[ 'disable' ],
) );
$html .= "<div class='pf_divider'></div>";


$html .= '<h4>'. __( 'Set redirection on login, logout and registration', $userMeta->name ) .'</h4>';  
$html .= "<div id=\"redirection_tabs\">";
    $html .= "<ul>";
    foreach( $roles as $key => $val )
        $html .= "<li><a href=\"#redirection-tabs-$key\">$val</a></li>";
    $html .= "</ul>";
    
    $defaultMsg     = __( 'Default (<em>Use default redirection</em>)',   $userMeta->name );        
    $refererMsg     = __( 'Referer (<em>Send the user back to the page where they comes from</em>)',   $userMeta->name );
    $homeMsg        = __( 'Home', $userMeta->name ) . sprintf(" (<em>%s</em>)", site_url() );
    $profileMsg     = __( 'Profile', $userMeta->name ) . sprintf(" (<em>%s</em>)", $userMeta->getProfileLink() );
    $dashboardMsg   = __( 'Dashboard', $userMeta->name ) . sprintf(" (<em>%s</em>)", admin_url() );
    $loginPageMsg   = __( 'Login Page', $userMeta->name ) . sprintf(" (<em>%s</em>)", wp_login_url() );
    $customUrlMsg   = __( '<em> (Include http:// with url)</em>',   $userMeta->name );                           
        
    $loginRegistrationOptions = array( 
        'default'       => $defaultMsg, 
        'referer'       => $refererMsg,
        'home'          => $homeMsg,
        'profile'       => $profileMsg,
        'dashboard'     => $dashboardMsg,
    );
    
    $logoutOptions = array( 
        'default'       => $defaultMsg, 
        'referer'       => $refererMsg,
        'home'          => $homeMsg,
        'login_page'    => $loginPageMsg,
    );
    
    
    foreach( $roles as $key => $val ){       
        // Start foreach

        $customLoginUrl = array( 'custom_url' => $userMeta->createInput( "redirection[login_url][$key]", 'text', array(
            'value'         => @$redirection[ 'login_url' ][ $key ],
            'before'        => __( 'Custom URL:', $userMeta->name ),
            'after'         => $customUrlMsg,
            'style'         => 'width:300px;',         
        ) ) ); 
        
        //$html .= "<p><strong>". __( 'Login', $userMeta->name ) . "</strong></p>";
        $html .= $userMeta->createInput( "redirection[login][$key]", "radio", array( 
            "value"         => @$redirection[ 'login' ][ $key ] ? @$redirection[ 'login' ][ $key ] : @$redirection[ 'login' ][ 'subscriber' ],
            "label"         => __( 'Login Redirection', $userMeta->name ),
            "option_before" => "<p>",
            "option_after"  => "</p>",
            "by_key"        => true,
            "label_class"   => "pf_label",
            "enclose"       => "div id=\"redirection-tabs-$key\"",
        ), array_merge( $loginRegistrationOptions, $customLoginUrl ) );    
         
        $customLogoutUrl = array( 'custom_url' => $userMeta->createInput( "redirection[logout_url][$key]", 'text', array(
            'value'         => @$redirection[ 'logout_url' ][ $key ],
            'before'        => __( 'Custom URL:', $userMeta->name ),
            'after'         => $customUrlMsg,
            'style'         => 'width:300px;',         
        ) ) ); 
        
        $html .= $userMeta->createInput( "redirection[logout][$key]", "radio", array( 
            "value"         => @$redirection[ 'logout' ][ $key ] ? @$redirection[ 'logout' ][ $key ] : @$redirection[ 'logout' ][ 'subscriber' ],
            "label"         => __( 'Logout Redirection ', $userMeta->name ),
            "option_before" => "<p>",
            "option_after"  => "</p>",
            "by_key"        => true,
            "label_class"   => "pf_label",
            "enclose"       => "div id=\"redirection-tabs-$key\"",
        ), array_merge( $logoutOptions, $customLogoutUrl ) ); 
        
        $customRegistrationUrl = array( 'custom_url' => $userMeta->createInput( "redirection[registration_url][$key]", 'text', array(
            'value'         => @$redirection[ 'registration_url' ][ $key ],
            'before'        => __( 'Custom URL:', $userMeta->name ),
            'after'         => $customUrlMsg,
            'style'         => 'width:300px;',         
        ) ) );         
        
        $html .= $userMeta->createInput( "redirection[registration][$key]", "radio", array( 
            "value"         => @$redirection[ 'registration' ][ $key ] ? @$redirection[ 'registration' ][ $key ] : @$redirection[ 'registration' ][ 'subscriber' ],
            "label"         => __( 'Registration Redirection', $userMeta->name ),
            "option_before" => "<p>",
            "option_after"  => "</p>",
            "by_key"        => true,
            "label_class"   => "pf_label",
            "enclose"       => "div id=\"redirection-tabs-$key\"",
        ), array_merge( $loginRegistrationOptions, $customRegistrationUrl ) );
        
        
            
    }//End foreach
$html .= "</div>";

?>

<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery( "#redirection_tabs" ).tabs();
    });
</script>
