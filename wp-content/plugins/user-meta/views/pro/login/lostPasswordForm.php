<?php
global $userMeta;
// Expected: $disableAjax, $visibility

$uniqueID = rand(0,99);

$html = null;

$html .= "<a href=\"javascript:void(0);\" class=\"lostpassword_link_$uniqueID\">" . __( 'Lost your password?', $userMeta->name ) . "</a>";

$onSubmit   = @$disableAjax ? null : "onsubmit=\"pfAjaxRequest(this); return false;\"";
$display    = $visibility == 'hide' ? "style=\"display:none\"" : null;
$html .= "<form id=\"um_lostpass_form_$uniqueID\" class=\"um_lostpass_form\" method=\"post\" $onSubmit >";
$html .= "<div class=\"lostpassword_form_div_$uniqueID\" $display >";
$html .= "<p>" . __('Please enter your username or email address. You will receive a link to create a new password via email.') . "</p>";

/*if( !@$_REQUEST['is_ajax'] && @$_REQUEST['method_name'] == 'lostpassword' )
    $html .= $userMeta->ajaxLostpassword();   */

$html .= $userMeta->createInput( 'user_login', 'text', array(
    'label'         => __( 'Username or E-mail', $userMeta->name ),
    'id'            => 'user_login' . $uniqueID,
    'class'         => 'um_lostpass_field um_input validate[required]',
    'label_class'   => 'pf_label',
    'enclose'       => 'p',
) );

$html .= $userMeta->actionName( 'lostpassword' );
$html .= $userMeta->nonceField();

$html .= $userMeta->createInput( 'login', 'submit', array(
    'value'     => __( 'Get New Password', $userMeta->name ),
    'id'        => 'um_login_button' . $uniqueID,
    'class'     => 'um_lostpass_button',
    'enclose'   => 'p',
) );
$html .= "</div></form>";

$html .= "
<script type='text/javascript'>
    jQuery('.lostpassword_link_{$uniqueID}').click(function(){
        jQuery('.lostpassword_form_div_{$uniqueID}').toggle('slow');
    });
</script>
";

?>
