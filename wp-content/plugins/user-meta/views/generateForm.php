<?php
global $userMeta;
//get veriable by render: $actionType, $fields, $form, $userData

$uniqueID   = rand(0,99);
$formID     = "um_user_form_$uniqueID";

// Counting page
$pageCount = 0;

if( !$form )
    return $html = $userMeta->ShowError( __( 'Form not found.', $userMeta->name ) );

if( !$form['fields'] )
    return $html = $userMeta->ShowError( __( 'Fields not found.', $userMeta->name ) );

foreach( $form['fields'] as $fieldID ){
    if( isset($fields[$fieldID]) ){
        if( $fields[$fieldID]['field_type'] == 'page_heading' )
            $pageCount++;
    }
}

 
$html = null;

$onsubmit   = @$form['disable_ajax'] ? null : "onsubmit=\"umInsertUser(this); return false;\"";
$formClass  = !empty( $form['form_class'] ) ? $form['form_class'] : null;
$html .= "<form method=\"post\" action=\"\" id=\"$formID\" class=\"um_user_form $formClass\" $onsubmit enctype=\"multipart/form-data\">";
    if( $form['fields'] ):
        
        $inPage     = false;
        $inSection  = false;
        $isNext     = false;
        $isPrevious = false;
        $currentPage= 0;
        foreach( $form['fields'] as $fieldID ){
            if( isset($fields[$fieldID]) ){
               
                /// Determine Field Group
                $field_type_data   = $userMeta->getFields( 'key', $fields[$fieldID]['field_type'] ); 
                $fieldGroup        = $field_type_data['field_group'];
                
                /// Determine Field Name
                $fieldName = null;
                if( $fieldGroup == 'wp_default' ){
                    $fieldName = $fields[$fieldID]['field_type'];
                }else{
                   if( isset($fields[$fieldID]['meta_key']) )
                        $fieldName = $fields[$fieldID]['meta_key'];
                }                
                               
                /// Determine Field Value
                $fieldValue = null;
                if( isset( $fields[$fieldID]['default_value'] ) )
                    $fieldValue = $fields[$fieldID]['default_value'];    
                if( $actionType == 'profile' OR $actionType == 'none' ){
                    if( isset( $userData->$fieldName ) )
                        $fieldValue = $userData->$fieldName;
                }    
                
                if( ! @$userMeta->showDataFromDB ){
                    if( isset( $_REQUEST[$fieldName] ) )  
                        $fieldValue = $_REQUEST[$fieldName];                    
                }
                
                   
                //IF page is started then set true to $inPage or continue
                //$inPage  = ( $fields[$fieldID]['field_type'] == 'page_heading' )  ? true : $inPage;
                // = ( $fields[$fieldID]['field_type'] == 'group_heading' ) ? true : $inGroup;      
                if( $fields[$fieldID]['field_type'] == 'page_heading' ){
                    $currentPage++;
                    $isNext     = $currentPage >= 2 ? true : false;
                    $isPrevious = $currentPage >  2 ? true : false;      
                }                
                                         
                $fields[$fieldID]['field_id']    = $fieldID;
                $fields[$fieldID]['field_name']  = $fieldName;
                $fields[$fieldID]['field_value'] = $fieldValue;
                $html .= $userMeta->renderPro( 'generateField', array( 
                    'field'         => $fields[$fieldID],
                    'form'          => $form,
                    'actionType'    => $actionType,
                    'userID'        => $userID,
                    'inPage'        => $inPage,
                    'inSection'     => $inSection,
                    'isNext'        => $isNext,
                    'isPrevious'    => $isPrevious,
                    'currentPage'   => $currentPage,
                    'uniqueID'      => $uniqueID,
                ) );
                
                if( $fields[$fieldID]['field_type'] == 'page_heading' ){
                    $inPage    = true;
                    $inSection = false;
                }
                    
                if( $fields[$fieldID]['field_type'] == 'section_heading' )
                    $inSection = true;                    
                //$inPage  = ( $fields[$fieldID]['field_type'] == 'page_heading' )  ? true : $inPage;
                //$inGroup = ( $fields[$fieldID]['field_type'] == 'group_heading' ) ? false : $inGroup;                 
                
            }               
        }
        
        // Similar to generateField: field_type==page_heading
        if( $inSection )
            $html .= "</div>";             
        if( $pageCount >= 2){
            $previousPage = $currentPage - 1 ;
            $html .= $userMeta->createInput( "", "button", array(
                "value"     =>  __( 'Previous', $userMeta->name ),
                "onclick"   => "umPageNavi($previousPage, false, this)",
                "class"     => "previous_button " . !empty( $form['button_class'] ) ? $form['button_class'] : "",
            ) );                        
            //$html .= "<input type='button' onclick='umPageNavi($previousPage)' value='" . __( 'Previous', $userMeta->name ) . "'>";
        }                           

        // End
                       
        $html .= $userMeta->createInput( "form_key", "hidden", array(
            "value"     => $form['form_key'],
        ) );      
   
        
        if( $actionType == 'profile' )
            $buttonValue = __( 'Update', $userMeta->name );
        elseif( $actionType == 'registration' )
            $buttonValue = __( 'Register', $userMeta->name );
        
        if( !empty( $form['button_title'] ) )
            $buttonValue = $form['button_title'];
                      
        $html .= $userMeta->createInput( "action_type", "hidden", array(
            "value"     => $actionType,
        ) ); 
        $html .= $userMeta->createInput( "user_id", "hidden", array(
            "value"     => $userID,
        ) ); 
        
        $html .= "<script>user_id=$userID</script>";        
        
        if( isset( $buttonValue ) ){
            $html .= $userMeta->createInput( "um_sibmit_button", "submit", array(
                "value"     => $buttonValue,
                "id"        => "insert_user",
                "class"     => !empty( $form['button_class'] ) ? $form['button_class'] : "",
                'enclose'   => 'div',
            ) );
        }
        
        
        if( $inPage )
            $html .= "</div>";    
            
    endif;
    
    $html .= wp_nonce_field( $userMeta->settingsArray('nonce'), 'pf_nonce' );
$html .= "</form>";    

    
?>


<script type="text/javascript">
    jQuery(document).ready(function(){

        jQuery(".um_user_form").validationEngine();

        umPageNavi( 1, false, "<?php echo $formID; ?>" );

        jQuery(".um_rich_text").wysiwyg({initialContent:" "});  

        jQuery(".um_datetime").datetimepicker({ dateFormat: 'yy-mm-dd', timeFormat: 'hh:mm:ss', changeYear: true });
        jQuery(".um_date").datepicker({ dateFormat: 'yy-mm-dd', changeYear: true });   
        jQuery(".um_time").timepicker({timeFormat: 'hh:mm:ss'});  

        //jQuery(".pass_strength").password_strength();    

        umFileUploader( '<?php  echo $userMeta->pluginUrl . '/framework/helper/uploader.php' ?>' );    

    });      
</script>