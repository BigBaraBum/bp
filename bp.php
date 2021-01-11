<?php
/*
Plugin Name: Bookmarks Posts
Description: Plugin that allows you to bookmark posts
Version: 0.1
Author: Reutov Vladimir
*/

class Bp{
    public $post_type = 'post';
    public $append    = false;
    public $taxonomy  = 'post_tag';
    public $terms     = "bookmarked";
    function __construct($args = array()){
        add_filter('post_row_actions',array($this,'_action_row'), 10, 2);
        add_action('admin_footer-edit.php',array($this,'addJS'));
        add_action('wp_ajax_setToCat', array($this,'ajaxSetToCat'));
        add_action('wp_ajax_removeFromCat', array($this, 'ajaxRemoveFromCat'));
        add_action('admin_menu', array($this, 'bookmarks_page'));
    }
    function bookmarks_page(){
      add_menu_page("Bookmarked posts", "Bookmarked Posts", "manage_options", "bookmark-posts",array($this, 'render'));
    }
    function render() {
      require plugin_dir_path( __FILE__ ) . 'bookmarkpage.php';
    }

    function _action_row($actions, $post){
        if ($post->post_type == $this->post_type){
            $actions['bookmark'] = '<a href="#" class="move_TO_CAT" data-pid="'.$post->ID.'">'.__('Bookmark').'</a>';
        }
        return $actions;
    }
    function addJS(){
        wp_enqueue_script( 'jquery');
        ?>
         <div id="status_update_working" style="background-color: green; color: #fff; font-wieght: bolder;   font-size: 22px;   height: 33px;   left: 40%;   padding: 35px;   position: fixed;   top: 100px;   width: 350px; display:none !important; "><?php _e('Changing status...'); ?></div>
        <script type="text/javascript">
        jQuery(document).ready(function($){
            function ajaxSetCat(p){
                jQuery("#status_update_working").show('fast');
                jQuery.getJSON(ajaxurl,
                    {   pid: p.attr("data-pid"),
                        action: "setToCat",
                        _nonce: "<?php echo wp_create_nonce('setToCat'); ?>"
                    },
                    function(data) {
                        if (data.error){
                            alert(data.error);
                        }else{
                             alert(data.text);
                        }
                    }
                );
                jQuery("#status_update_working").hide('9500');
            }
            $('.move_TO_CAT').click(function(){
                ajaxSetCat($(this))
            });
        });
        </script>
        <?php

    }
    function ajaxRemoveFromCat(){
      if (!isset($_GET['pid']) || ! wp_verify_nonce($_GET['_nonce'], 'removeFromCat')){
          $re['error'] = __('something went wrongdddd ...');
          echo json_encode($re);
          die();
      }
      $results = wp_set_post_terms( intval($_GET['pid']), 'lol', $this->taxonomy, $this->append );
      if ( is_wp_error( $results ) ){
          $re['error'] = __('something went wrong ...') ." ". $results->get_error_message();
      }elseif($results === false || !is_array($results)){
          $re['error'] = __('something went wrong ...');
      }else{
          $re['text'] = __('Bookmarked removed succesfully');
      }
      echo json_encode($re);
      die();
  }
    function ajaxSetToCat(){
        if (!isset($_GET['pid']) || ! wp_verify_nonce($_GET['_nonce'], 'setToCat')){
            $re['error'] = __('something went wrong ...');
            echo json_encode($re);
            die();
        }
        $results = wp_set_post_terms( intval($_GET['pid']), $this->terms, $this->taxonomy, $this->append );
        if ( is_wp_error( $results ) ){
            $re['error'] = __('something went wrong ...') ." ". $results->get_error_message();
        }elseif($results === false || !is_array($results)){
            $re['error'] = __('something went wrong ...');
        }else{
            $re['text'] = __('Bookmarked succesfully');
        }
        echo json_encode($re);
        die();
    }
}
new Bp();
