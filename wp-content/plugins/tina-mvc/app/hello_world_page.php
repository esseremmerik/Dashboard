<?php
 
   class hello_world_page extends tina_mvc_base_page_class {       
 
      function __construct( $request ) {
 
         parent::__construct( $request );
         $this->set_post_title( 'Happy ' . date('l') );
         $this->set_post_content( 'Hello World!' );
 
    }
 
}
 
?>