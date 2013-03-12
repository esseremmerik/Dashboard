<?php
global $userMeta;
// Expected: $loginBy, $loginTitle, $disableAjax

$uniqueID = rand(0,99);

$onSubmit = @$disableAjax ? null : "onsubmit=\"umLogin(this); return false;\"";
$html = "<form id=\"um_login_form$uniqueID\" class=\"um_login_form\" method=\"post\" $onSubmit >";

$html .= $userMeta->createInput( 'user_login', 'text', array(
    'label'         => $loginTitle,
    'id'            => 'user_login' . $uniqueID,
    'class'         => 'um_login_field um_input validate[required]',
    'label_class'   => 'pf_label',
    'enclose'       => 'p',
) );
$html .= $userMeta->createInput( 'user_pass', 'password', array(
    'label'     => __( 'Password', $userMeta->name ), 
    'id'        => 'user_pass' . $uniqueID,
    'class'     => 'um_pass_field um_input validate[required]',
    'label_class'   => 'pf_label',
    'enclose'   => 'p',
) );            

$html .= $userMeta->createInput( 'remember', 'checkbox', array(    
    'label'     => __( 'Remember Me', $userMeta->name ),
    'id'        => 'remember' . $uniqueID,
    'class'     => 'um_remember_field',
    'enclose'   => 'p',
) );    

$html .= "<input type='hidden' name='action' value='um_login' />";
$html .= "<input type='hidden' name='action_type' value='login' />";
$html .= "<input type='hidden' name='login_by' value='$loginBy' />";
$html .= $userMeta->nonceField();
//$html .= wp_original_referer_field( false, 'previous' );

$html .= $userMeta->createInput( 'login', 'submit', array(
    'value'     => __( 'Login', $userMeta->name ),
    'id'        => 'um_login_button' . $uniqueID,
    'class'     => 'um_login_button',
    'enclose'   => 'p',
) );


$html .= "</form>"; 

/*$html .= "
<script type='text/javascript'>
    jQuery('#um_login_form_{$initBy}').validationEngine();  
</script>
";*/

?>