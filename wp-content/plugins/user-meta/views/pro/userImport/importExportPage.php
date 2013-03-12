<?php
global $userMeta; 
// Expected $csvCache, $maxSize
?>


<div class="wrap">
    <div id="icon-users" class="icon32 icon32-posts-page"><br /></div>  
    <h2><?php _e( 'User Import & Export', $userMeta->name ); ?></h2>   
    <?php do_action( 'um_admin_notice' ); ?>
    <div id="dashboard-widgets-wrap">
        <div class="metabox-holder">
            <div id="um_admin_content">
                <?php $userMeta->renderPro("importStep1", array(
                    'csvCache'  => $csvCache,
                    'maxSize'   => $maxSize,
                ), 'userImport'); ?>
                
                <?php echo $userMeta->ajaxUserExportForm(true); ?>                
                
                <input type="button" class="button-primary" onclick="umNewUserExportForm(this);" value="<?php _e( 'New Export Template', $userMeta->name ); ?>" />                               
            </div>                       
            
            <div id="um_admin_sidebar">                            
                <?php
                echo $userMeta->metaBox( __( '3 steps to getting started', $userMeta->name ),  $userMeta->boxHowToUse());               
                if( !@$userMeta->isPro )
                    echo $userMeta->metaBox( __( 'User Meta Pro', $userMeta->name ),   $userMeta->boxGetPro());
                echo $userMeta->metaBox( __( 'Shortcode', $userMeta->name ),   $userMeta->boxShortcodesDocs());
                ?>
            </div>
        </div>
    </div>     
</div>

<script>
jQuery(document).ready(function(){
    jQuery('.um_dropme').sortable({
        connectWith: '.um_dropme',
        cursor: 'pointer'
    }).droppable({
        accept: '.button',
        activeClass: 'um_highlight',      
    });   
    
    jQuery(".um_date").datepicker({ dateFormat: 'yy-mm-dd', changeYear: true }); 
});
</script> 