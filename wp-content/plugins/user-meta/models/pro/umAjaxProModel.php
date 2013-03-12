<?php

if( !class_exists( 'umAjaxProModel' ) ) :
class umAjaxProModel {
    
    function ajaxLostpassword(){
        global $userMeta;
        $login  = $userMeta->getSettings( 'login' );

        $resetPassLink = isset( $login[ 'login_page' ] ) ? get_permalink( $login[ 'login_page' ] ) : null;                
        $response = $userMeta->retrieve_password( $resetPassLink );
        
        if( $response === true )
            $output = $userMeta->showMessage( __( 'Check your e-mail for the confirmation link.', $userMeta->name ) , 'success', false );
        elseif( is_wp_error( $response ) )
            $output = $userMeta->showError( $response->get_error_message(), false );   
            
        if( isset( $_REQUEST['is_ajax'] ) )
            echo @$output;
        else
           return @$output;               
    }
    
    
    /**
     * ajaxLogin function will call with um_login action
     */
    function ajaxLogin(){
        global $userMeta;        
        $userMeta->verifyNonce();
        
        $user = $userMeta->doLogin();  
        if( $user ){
            if( !is_wp_error( $user ) ){
                $redirect   = "redirect_to=\"$user->redirect_to\"";
                $html       = $userMeta->showMessage( __( 'Login Successful', $userMeta->name ), 'success', false );
                $html      .= $userMeta->loginResponse( $user );               
                $output     = "<div status=\"success\" $redirect >$html</div>";                 
            }else
                $output     = $userMeta->showError( $user->get_error_message(), false );
        }
        
        if( isset( $_REQUEST['is_ajax'] ) ){
            echo @$output;
            die();
        }else
           return @$output;         
          
    }
    
    function ajaxSaveEmailTemplate(){
        global $userMeta;
        if( ! isset( $_REQUEST ) )
            $userMeta->showError( __( 'There is some problem while updating', $userMeta->name ) );
        
        $data = $userMeta->arrayRemoveEmptyValue( $_REQUEST );  
        $data = $userMeta->removeNonArray( $data );
        //$userMeta->dump($data);
              
        $userMeta->updateData( 'emails', stripslashes_deep( $data ) );
        echo $userMeta->showMessage( __( 'Successfully Saved.', $userMeta->name ) );
    }
    
    /**
     * Perform user exports by ajax call also save user export template.
     */
    function ajaxUserExport(){
        global $userMeta;
        $userMeta->verifyNonce( true );        
        
        $fieldsSelected = array();
        if( is_array( @$_REQUEST[ 'fields' ] ) )
            $fieldsSelected = array_slice( $_REQUEST['fields'], 0, $_REQUEST['field_count'], true );
        
        /**
         * Saving Data 
         */
        if( $_REQUEST['action_type'] == 'save' || $_REQUEST['action_type'] == 'save_export' ){           
            $data = array();          
            $data['fields']         = $fieldsSelected;
            $data['exclude_roles']  = @$_REQUEST['exclude_roles'];
            $data['start_date']     = @$_REQUEST['start_date'];
            $data['end_date']       = @$_REQUEST['end_date'];
            $data['orderby']        = @$_REQUEST['orderby'];
            $data['order']          = @$_REQUEST['order'];
            
            $export = $userMeta->getData( 'export' );
            
            $export['user'][ @$_REQUEST['form_id'] ] = $data;
            
            $userMeta->updateData( 'export', $export );           
        }
        
        /**
         * Export to csv 
         */
        if( $_REQUEST['action_type'] == 'export' || $_REQUEST['action_type'] == 'save_export' ){
            $meta_query = array();
            if( is_array( @$_REQUEST['exclude_roles'] ) ){
                foreach( @$_REQUEST['exclude_roles'] as $role ){
                    $meta_query[] = array(
                        'key' => "wp_capabilities",
                        'value' => "\"$role\"",
                        'compare' => "not like",                       
                    );
                }                   
            }
            
            $args = array(
                'fields'        => 'all_with_meta',
                'meta_query'    => $meta_query,
                'orderby'       => @$_REQUEST['orderby'],
                'order'         => @$_REQUEST['order']
            );
            
            add_action( 'pre_user_query', array( $userMeta, 'filterRegistrationDate' ) );
            $users = get_users( $args );
            remove_action( 'pre_user_query', array( $userMeta, 'filterRegistrationDate' ) );
            
            /// Add header row for csv
            $fileData = array();
            $fileData[] = $fieldsSelected;
     
            /// Add user data for csv
            foreach( $users as $user ){
                $userData = array();
                foreach( $fieldsSelected as $key => $val )
                    $userData[ $key ] = @$user->$key;
                $fileData[] = $userData;
            } 
            
            $fileName = 'user_export_' . date('Y-m-d_H-i') . '.csv';
            $userMeta->generateCsvFile( $fileName, $fileData );
        }
    }
    
    /**
     * Build user export forms in admin section and generate new form by ajax call.
     * verifyNonce is calling inside.
     */    
    function ajaxUserExportForm( $populateAll=false ){
        global $userMeta;
                
        $fieldsDefault  = $userMeta->defaultUserFieldsArray();        
        $fieldsMeta     = array();       
        $extraFields    = $userMeta->getData('fields');
        if( is_array( $extraFields ) ){
            foreach($extraFields as $data){
                if( !empty( $data['meta_key'] ) ){
                    $fieldTitle = ! empty( $data['field_title'] ) ? $data['field_title'] : $data['meta_key'] ;
                    $fieldsMeta[ $data['meta_key'] ] = $fieldTitle;
                }
            } 
        }
        $fieldsAll = array_merge( $fieldsDefault, $fieldsMeta );
        
        $roles = $userMeta->getRoleList();
        
        if( $populateAll ){
            $export      = $userMeta->getData('export');
            $formsSaved = @$export[ 'user' ];
            if( is_array( $formsSaved ) && !empty( $formsSaved ) ){
                foreach( $formsSaved as $formID => $formData ){
                    $fieldsSelected = $formData[ 'fields' ];
                    $fieldsAvailable = $fieldsAll;
                    if( is_array( $fieldsSelected ) ){
                        foreach( $fieldsSelected as $key => $val )
                            unset( $fieldsAvailable[$key] );
                    } 
                    
                    echo $userMeta->renderPro( 'exportForm', array(
                        'formID'            => $formID,
                        'fieldsSelected'    => $fieldsSelected,
                        'fieldsAvailable'   => $fieldsAvailable,       
                        'roles'             => $roles,
                        'formData'          => $formData,
                    ), 'userImport' );                    
                }
                
                $break = true;
            }
            
            $newUserExportFormID = (int) $userMeta->maxKey( $formsSaved ) + 1;
            echo "<input type=\"hidden\" id=\"new_user_export_form_id\" value=\"$newUserExportFormID\" />";            
        }
        
        /// For default or new form
        if( !@$break ){            
            $formID = !empty($_REQUEST['form_id']) ? $_REQUEST['form_id'] : 'default';           
            if( $formID <> 'default' )
                $userMeta->verifyNonce( true );
            
             echo $userMeta->renderPro( 'exportForm', array(
                'formID'            => $formID,
                'fieldsSelected'    => array(),
                'fieldsAvailable'   => $fieldsAll,       
                'roles'             => $roles,
            ), 'userImport' );               
        }     

    }
    
    /**
     * Remove User Export Template by ajax call
     */
    function ajaxRemoveExportForm(){
        global $userMeta;
        $userMeta->verifyNonce( true );
        
        $export     = $userMeta->getData('export');
        
        if( !empty( $export[ 'user' ][ $_REQUEST['form_id'] ] ) ){
            unset( $export[ 'user' ][ $_REQUEST['form_id'] ] );
            $userMeta->updateData( 'export', $export );
        }
    }
    
}
endif;