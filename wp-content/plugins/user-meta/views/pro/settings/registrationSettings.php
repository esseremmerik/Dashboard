<?php
global $userMeta;
// Expected: $registration
// field slug: registration

$html = null;
   
$registrationSettings = array( 
    'auto_active'           => __( 'User auto activation.', $userMeta->name ) . '<br /><em>(' . __( 'User will be activated automatically after registration', $userMeta->name ) . ')</em>', 
    'email_verification'    => __( 'Need email verification.', $userMeta->name ) . '<br /><em>(' . __( 'A verification link will be sent to user email. User must verify the link to activate their account', $userMeta->name ) . ')</em>',  
    'admin_approval'        => __( 'Need admin approval.', $userMeta->name ) . '<br /><em>(' . __( 'Admin needs to approve the new user', $userMeta->name ) . ')</em>', 
    'both_email_admin'      => __( 'Need both email verification and admin approval.', $userMeta->name ) . '<br /><em>(' . __( 'A verification link will be sent to user email. User must verify the link to activate their account and an admin needs to approve the account', $userMeta->name ) . ')</em>', 
);    
    

$html .= $userMeta->createInput( "registration[user_activation]", "radio", array( 
    'label'         => __( 'User Activation', $userMeta->name ),
    'value'         => @$registration[ 'user_activation' ],
    'label_class'   => 'pf_label',
    'option_before' => '<p>',
    'option_after'  => '</p>',
    'by_key'        => true,
 ), $registrationSettings ); 
 
//$html .= "<div class='clear'></div>";
/*
$html .= "<div class='pf_divider'></div>";
$html .= $userMeta->createInput( 'registration[auto_login]', 'checkbox', array(
    'label' => __( 'Auto login after registration', $userMeta->name ),
    'value' => @$registration[ 'auto_login' ] ? true : false,
) );
$html .= '<p><i>' . __( 'Only supported with "User auto active"', $userMeta->name ) . '</i></p>';
*/

?>