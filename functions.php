/**
 * Add Custom Content to WooCommerce Category and Brand Archive Pages
 * This code allows users to select Elementor templates and inject them into the bottom of WooCommerce category archive pages.
 */

// Add a dropdown to select Elementor templates when adding a new product category
function add_extra_taxonomy_fields($taxonomy) {
    $args = array(
        'post_type' => 'elementor_library',
        'numberposts' => -1
    );
    $elementor_templates = get_posts($args);

    echo '<div class="form-field">';
    echo '<label for="elementor_template">Add Page Content</label>';
    echo '<select name="elementor_template" id="elementor_template">';
    echo '<option value="">Select from the list</option>';
    foreach($elementor_templates as $template) {
        echo '<option value="' . $template->ID . '">' . $template->post_title . '</option>';
    }
    echo '</select>';
    echo '<p class="description">You can add content to the bottom of the page from here. You should first create this content by creating a new template in the templates section in the main menu. Then, by selecting the name of the relevant template from here, you can ensure it appears on the page.</p>';
    echo '</div>';
}
add_action('product_cat_add_form_fields', 'add_extra_taxonomy_fields', 10, 1);

// Add a dropdown to select Elementor templates when editing an existing product category
function edit_extra_taxonomy_fields($term) {
    $args = array(
        'post_type' => 'elementor_library',
        'numberposts' => -1
    );
    $elementor_templates = get_posts($args);

    $selected_template = get_term_meta($term->term_id, 'elementor_template', true);

    echo '<tr class="form-field">';
    echo '<th scope="row" valign="top"><label for="elementor_template">Add Page Content</label></th>';
    echo '<td>';
    echo '<select name="elementor_template" id="elementor_template">';
    echo '<option value="">Select from the list</option>';
    foreach($elementor_templates as $template) {
        $selected = ($selected_template == $template->ID) ? 'selected' : '';
        echo '<option value="' . $template->ID . '" ' . $selected . '>' . $template->post_title . '</option>';
    }
    echo '</select>';
    echo '<p class="description">You can add content to the bottom of the page from here. You should first create this content by creating a new template in the templates section in the main menu. Then, by selecting the name of the relevant template from here, you can ensure it appears on the page.</p>';
    echo '</td>';
    echo '</tr>';
}
add_action('product_cat_edit_form_fields', 'edit_extra_taxonomy_fields', 10, 1);

// Save the selected Elementor template when the product category is saved or updated
function save_extra_taxonomy_fields($term_id) {
    if (isset($_POST['elementor_template'])) {
        update_term_meta($term_id, 'elementor_template', $_POST['elementor_template']);
    }
}
add_action('edited_product_cat', 'save_extra_taxonomy_fields', 10, 1);
add_action('create_product_cat', 'save_extra_taxonomy_fields', 10, 1);

// Display the selected Elementor template at the bottom of the WooCommerce category archive page
function display_elementor_template_at_archive_bottom() {
    if (!is_tax('product_cat')) return;

    $term_id = get_queried_object_id();
    $content_id = get_term_meta($term_id, 'elementor_template', true);

    if (!$content_id) return;

    if (defined('ELEMENTOR_VERSION')) {
        echo Elementor\Plugin::instance()->frontend->get_builder_content_for_display($content_id);
    } else {
        $_content = get_post_field('post_content', $content_id);
        echo do_shortcode($_content);
    }
}
add_action('woocommerce_after_main_content', 'display_elementor_template_at_archive_bottom', 20);
