<?php
global $userMeta;

$wpFields = null;
foreach( $userMeta->getFields( 'field_group', 'wp_default', 'title' ) as $fieldKey => $fieldValue )
    $wpFields .= "<div field_type='$fieldKey' class='button um_field_selecor' onclick='umNewField(this)'>$fieldValue</div>";            

$standardFields = null;
foreach( $userMeta->getFields( 'field_group', 'standard', 'title' ) as $fieldKey => $fieldValue )
    $standardFields .= "<div field_type='$fieldKey' class='button um_field_selecor' onclick='umNewField(this)'>$fieldValue</div>";            
            
$formattingFields = null;
foreach( $userMeta->getFields( 'field_group', 'formatting', 'title' ) as $fieldKey => $fieldValue )
    $formattingFields .= "<div field_type='$fieldKey' class='button um_field_selecor' onclick='umNewField(this)'>$fieldValue</div>";


return array(
    'wp_default'    => $wpFields,
    'standard'      => $standardFields,
    'formatting'    => $formattingFields,
);

?>