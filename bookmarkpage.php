
<h1>Bookmarked posts</h1>
<?php
$posts = get_posts(array(
  'tag' => 'bookmarked',
));
foreach( $posts as $post ){
  ?>
  <div class="post-bookmarked data-pid="<?echo $post->ID?>">
  <h3 class="post_title">Title: <? echo $post->post_title ?></h3>
  <p class="post_content">Content:<? echo $post->post_content ?></p>
  <button class="remove_CAT" data-pid="<?echo $post->ID?>">Remove post from bookmarked</button>
  </div>
  <?php
   // формат вывода the_title() ...
}
?>
<script type="text/javascript">
jQuery(document).ready(function($){
            function ajaxRemoveCat(p){
                jQuery("#status_update_working").show('fast');
                jQuery.getJSON(ajaxurl,
                    {   pid: p.attr("data-pid"),
                        action: "removeFromCat",
                        _nonce: "<?php echo wp_create_nonce('removeFromCat'); ?>"
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
            $('.remove_CAT').click(function(){
                ajaxRemoveCat($(this))
            });
        });
</script>
<style>
.post-bookmarked{
  max-width: 300px;
  border: 4px solid black;
  border-radius: 5px;
  padding: 20px;
}
</style>
