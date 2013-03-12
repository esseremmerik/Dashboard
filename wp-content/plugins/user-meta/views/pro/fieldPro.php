<?php

$html = null;
if( $field_type == 'datetime' ):  
    $html .= "$fieldMetaKey";
    $html .= "<div class='um_segment'>$fieldRequired $fieldAdminOnly $fieldReadOnly $fieldUnique</div>";  
    $html .= "$fieldDefaultValue $fieldDateTimeSelection";
    $html .= "$fieldSize $fieldMaxChar $fieldCssClass $fieldCssStyle";              

elseif( $field_type == 'password' ):  
    $html .= "$fieldMetaKey";
    $html .= "<div class='um_segment'>$fieldRetypePassword $fieldPasswordStrength $fieldRequired $fieldAdminOnly $fieldReadOnly $fieldUnique</div>";  
    $html .= "$fieldDefaultValue";    
    $html .= "$fieldSize $fieldMaxChar $fieldCssClass $fieldCssStyle";
    
elseif( $field_type == 'email' ):  
    $html .= "$fieldMetaKey";
    $html .= "<div class='um_segment'>$fieldRetypeEmail $fieldRequired $fieldAdminOnly $fieldReadOnly $fieldUnique</div>";  
    $html .= "$fieldDefaultValue";    
    $html .= "$fieldSize $fieldMaxChar $fieldCssClass $fieldCssStyle";
    
elseif( $field_type == 'file' ):  
    $html .= "$fieldMetaKey";
    $html .= "<div class='um_segment'>$fieldRequired $fieldAdminOnly $fieldReadOnly $fieldDisableAjax</div>";  
    $html .= "$fieldAllowedExtension $fieldImageWidth $fieldImageHeight $fieldMaxFileSize";
    $html .= "$fieldCssClass $fieldCssStyle";   

elseif( $field_type == 'image_url' ):    
    $html .= "$fieldMetaKey";
    $html .= "<div class='um_segment'>$fieldRequired $fieldAdminOnly $fieldReadOnly $fieldUnique</div>";
    $html .= "$fieldDefaultValue";
    $html .= "$fieldSize $fieldMaxChar $fieldCssClass $fieldCssStyle";  
    
elseif( $field_type == 'phone' ):    
    $html .= "$fieldMetaKey";
    $html .= "<div class='um_segment'>$fieldRequired $fieldAdminOnly $fieldReadOnly $fieldUnique</div>";
    $html .= "$fieldDefaultValue";
    $html .= "$fieldSize $fieldMaxChar $fieldCssClass $fieldCssStyle";              

elseif( $field_type == 'number' ):    
    $html .= "$fieldMetaKey";
    $html .= "<div class='um_segment'>$fieldRequired $fieldAdminOnly $fieldReadOnly $fieldUnique</div>";
    $html .= "$fieldDefaultValue $fieldMinNumber $fieldMaxNumber";
    $html .= "$fieldSize $fieldCssClass $fieldCssStyle";   
    
elseif( $field_type == 'url' ):    
    $html .= "$fieldMetaKey";
    $html .= "<div class='um_segment'>$fieldRequired $fieldAdminOnly $fieldReadOnly $fieldUnique</div>";
    $html .= "$fieldDefaultValue";
    $html .= "$fieldSize $fieldMaxChar $fieldCssClass $fieldCssStyle";     
    
elseif( $field_type == 'country' ):
    $html .= "$fieldMetaKey";
    $html .= "<div class='um_segment'>$fieldRequired $fieldAdminOnly $fieldReadOnly $fieldUnique</div>";
    $html .= "$fieldDefaultValue $fieldCountrySelectionType";
    $html .= "$fieldSize $fieldCssClass $fieldCssStyle";      
    
    
    
    
    
elseif( $field_type == 'page_heading' OR $field_type == 'section_heading' ) :
    $html .= "$fieldCssClass $fieldCssStyle $fieldShowDivider";   
    
elseif( $field_type == 'html' ): 
    $html .= "$fieldDefaultValue";
    
elseif( $field_type == 'captcha' ):
    $general    = $userMeta->getSettings( 'general' );
    if( ( !@$general['recaptcha_public_key'] ) || ( !@$general['recaptcha_private_key'] ) ){
        $html .= "<div class='pf_warning'>" . sprintf( __( 'Please provide reCaptcha public and private key from User Meta %s page', $userMeta->name ), $userMeta->adminPageUrl('settings') ) . "</div>";
    }        
    $html .= "<div class='um_segment'>$fieldNonAdminOnly $fieldRegistrationOnly</div>";
    //$html .= "$fieldCaptchaPublicKey $fieldCaptchaPrivateKey";
    //$html .= "$fieldSize $fieldCssClass $fieldCssStyle";         
endif;


?>