<script src="<?php echo base_url();?>assets/plugins/lightGallery/js/lightgallery.min.js"></script>
    <script src="<?php echo base_url();?>assets/plugins/lightGallery/js/lg-fullscreen.min.js"></script>
    <script src="<?php echo base_url();?>assets/plugins/lightGallery/js/lg-zoom.min.js"></script>
    <script src="<?php echo base_url();?>assets/plugins/lightGallery/js/lg-hash.min.js"></script>
    <script src="<?php echo base_url();?>assets/plugins/lightGallery/js/lg-autoplay.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#thumbs_display').lightGallery({selector:'.presentation-link',mode:'lg-zoom-in-out'});
        });
    </script>
