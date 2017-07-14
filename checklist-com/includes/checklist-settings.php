 <!-- Create a header in the default WordPress 'wrap' container -->
    <div class="wrap">
     
        <div id="icon-themes" class="icon32"></div>
        <h2><?php esc_html_e( 'Checklist Settings', 'checklist-com' ) ?></h2>
        <?php settings_errors(); ?>
         
        <form method="post" action="options.php">
            <?php
                settings_fields( 'checklist_group' );               // option group
                do_settings_sections( 'checklist_settings' );       // the page it is on
                 
                submit_button();
             
            ?>
        </form>
         
    </div><!-- /.wrap -->