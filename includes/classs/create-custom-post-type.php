<?php

class CreateCustomPostType
{
    private $nameSingle;
    private $namePlural;

    public function __construct($nameSingle, $namePlural, $meta_boxes = array())
    {
        $this->nameSingle = $nameSingle;
        $this->namePlural = $namePlural;
        $this->meta_boxes = $meta_boxes;
        add_action('init', array($this, 'register_post_type'));
        if (count($this->meta_boxes)){
            add_action("add_meta_boxes", array($this, 'create_meta_box'));
            add_action('save_post', array($this, 'save_post_data'));

        }

    }

    public function save_post_data($post_id)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return $post_id;

        // Retrieve post id
        if ($this->nameSingle !== get_post_type()) {
            return $post_id;
        }
        // Check the user's permissions
        if (isset( $_POST['post_type']) && $this->nameSingle  == $_POST['post_type']) {

            if (!current_user_can('edit_page', $post_id))
                return $post_id;

        } else {

            if (!current_user_can('edit_post', $post_id))
                return $post_id;
        }
        foreach ($this->meta_boxes as $key => $value){

            if(isset($_POST[$value['name_field']])){
                $field= sanitize_text_field($_POST[ $value['name_field']]);
                update_post_meta($post_id,  $value['name_field'],$field);
            }
        }
    }

    public function create_meta_box()
    {
        add_meta_box(
            "fields_custom_meta_box", // Meta box ID
            "fields custom", // Meta box title
            array($this, 'create_meta_box_callback'), // Meta box callback function
            $this->nameSingle, // The custom post type parameter 1
            "normal", // Meta box location in the edit screen
            "high" // Meta box priority
        );
    }

    public function create_meta_box_callback()
    {
        global $post;
        wp_nonce_field("freshcode-" . $this->nameSingle . "nonce", "freshcode-" . $this->nameSingle . "nonce");
        foreach ($this->meta_boxes as $key => $value):
            $field= get_post_meta($post->ID,$value['name_field'],true);
            ?>

            <th><label for="<?php echo $key ?>_name_field"<?php echo $key ?>></label>
            <th>
            <td><input
                        placeholder="<?php echo $key ?>"
                        type="<?php echo  $value['type']?>"
                        id="<?php echo $value['name_field'] ?>"
                        class="large-text"
                        name="<?php echo $value['name_field'] ?>"
                        value="<?php echo $field?>"
                />
            <td>
        <?php
        endforeach;

    }

    public function register_post_type()
    {
        $labels = array(
            'name' => __($this->namePlural),
            'singular_name' => __($this->nameSingle),
            'add_new' => __('Add New ' . $this->nameSingle),
            'add_new_item' => __('Add New ' . $this->nameSingle),
            'edit_item' => __('Edit ' . $this->nameSingle),
            'new_item' => __('New ' . $this->nameSingle),
            'all_items' => __('All ' . $this->namePlural),
            'view_item' => __('View ' . $this->nameSingle),
            'search_items' => __('Search ' . $this->nameSingle),
            /*'featured_image'     => 'Poster',
            'set_featured_image' => 'Add Poster'*/
        );

        $args = array(
            'labels' => $labels,
            /*  'description'       => 'Holds our custom article post specific data',*/
            'public' => true,
            'menu_position' => 5,
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
            'has_archive' => true,
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'query_var' => true,
        );
        register_post_type($this->nameSingle, $args);
    }
}