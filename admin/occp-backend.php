<?php

if (!defined('ABSPATH'))
    exit;

if (!class_exists('OCCP_menua')) {

    class OCCP_menua {

        protected static $instance;

        function occp_free_install() {
            if ( ! function_exists( 'WC' ) ) {
              add_action( 'admin_notices', array($this,'occp_install_error') );
            }
        }


        function occp_install_error() {
            ?>
                <div class="error">
                    <p>
                        <?php _e( 'Woo Frequently Bought Together is enabled but not effective. It requires WooCommerce.', OCCP_DOMAIN ); ?>
                    </p>
                </div>
            <?php
        }


        function occp_custom_product_tabs( $tabs) {
            $tabs['combo_product'] = array(
                'label'     => __( 'Frequently Added', 'woocommerce' ),
                'target'    => 'combo_options',
                'class'     => array( 'show_if_simple', 'show_if_variable', 'show_if_grouped', 'show_if_external' ),
            );
            return $tabs;
        }


        function occp_custom_product_tabs_fields() {
            ?> 
            <div id="combo_options" class="panel woocommerce_options_panel">
                <div class = 'options_group' >
                    <p class='form-field'>
                        <?php 
                            global $post, $product_object;
                            $product_id = $post->ID;
                            is_null( $product_object ) && $product_object = wc_get_product( $product_id );
                            $to_exclude = array( $product_id );
                        ?>
                        <label><?php _e( 'Add Product', 'woocommerce' ); ?></label>
                        <select id="occp_select_serach_box" name="occp_select2[]" multiple="multiple" style="width:70%;max-width:25em;" except="<?php echo $to_exclude[0]; ?>">
                            <?php 

                                $product = get_post_meta( get_the_ID(), 'occp_select2', true );
                                print_r($product);
                                $occp_selected_product_array = array();
                                $occp_selected_product_ids = array();

                                foreach ($product as $productId) {
                                    $product = wc_get_product( $productId );
                                    
                                    $occp_add_current_product_title = $product->get_title();
                                    //print_r($occp_add_current_product_title);
                                    $occp_add_current_product_id = $product->get_id();
                                    //print_r($occp_add_current_product_id);
                                    $occp_add_current_product_price = $product->get_price();
                                    //print_r($occp_add_current_product_price);
                                    $occp_add_current_product_is_variation   = $product->is_type( 'variation' ); 
                                    $occp_add_current_product_real_title = $occp_add_current_product_title;
                                    //print_r($occp_add_current_product_real_title);
                                        if( $occp_add_current_product_is_variation ) {
                                            $attributes = $product->get_variation_attributes();
                                            $variations = array();

                                            foreach( $attributes as $key => $attribute ) {
                                                $variations[] = $attribute;
                                            }

                                            if( ! empty( $variations ) )
                                            $occp_add_current_product_real_title .= ' - ' . implode( ', ', $variations );
                                        }

                                    $occp_add_current_product_discunt = get_post_meta( get_the_ID(), 'occp_off_per', true );
                                    $occp_add_current_product_discunt_type = get_post_meta( get_the_ID(), 'occp_discount_type', true );


                                    $occp_selected_product_ids[] = $occp_add_current_product_id;
                                    $occp_selected_product_array[] = array(
                                        'id'=>$occp_add_current_product_id,
                                        'text'=>$occp_add_current_product_real_title,
                                        'price'=>wc_price($occp_add_current_product_price),
                                        'discount'=>$occp_add_current_product_discunt[$occp_add_current_product_id],
                                        'discount_type'=>$occp_add_current_product_discunt_type[$occp_add_current_product_id]
                                    );
                                    
                                }

                            ?>
                        </select>
                        
                        <Script>
                           var occp_selected_product_array = <?php echo json_encode($occp_selected_product_array);?>;
                           var occp_selected_product_ids = <?php echo json_encode($occp_selected_product_ids);?>;
                        </Script>
                    </p>
                    <p class='form-field'>
                        <label></label>
                        If you want select more than 3 products <a href="https://www.xeeshop.com/product/woo-frequently-bought-together/" target="_blank" class="occp_pro">Get pro</a> version
                    </p>
                    <div class="occp_select_back">
                        <label><?php _e( 'Selected', 'woocommerce' ); ?></label>
                        <div class="occp_sortable">
                            <ul id="sortable"> 
                                <?php
                                    $occp_drag_product = get_post_meta( get_the_ID(), 'occp_select2', true );
                                    //print_r($occp_drag_product);
                                    if(!empty($occp_drag_product)){
                                        foreach ($occp_drag_product as $productId) {
                                            $product = wc_get_product( $productId );
                                            
                                            $occp_drag_current_product_id = $product->get_id();
                                            $occp_drag_current_product_title = $product->get_title();                        
                                            $occp_drag_current_product_is_variation = $product->is_type( 'variation' );
                                            $occp_drag_current_product_price = $product->get_price();
                                            if(empty($occp_drag_current_product_price)){
                                                $occp_drag_current_product_price = 0;
                                            }
                                            $occp_drag_current_product_discunt = get_post_meta( get_the_ID(), 'occp_off_per', true );
                                            $occp_drag_current_product_discunt_type = get_post_meta( get_the_ID(), 'occp_discount_type', true ); 

                                            ?>
                                            <li class="ui-state-default" id="<?php echo $occp_drag_current_product_id; ?>">
                                                <span class="occp-draggble-icon"></span>
                                                <span class="product-attributes-drop"> 
                                                    <?php echo $occp_drag_current_product_title ;                          
                                                        if( $occp_drag_current_product_is_variation ) {
                                                            $attributes = $product->get_variation_attributes();
                                                            $variations = array();

                                                            foreach( $attributes as $key => $attribute ) {
                                                                $variations[] = $attribute;
                                                            }

                                                            if( ! empty( $variations ) )
                                                            echo ' &ndash; ' . implode( ', ', $variations ) ;
                                                        }
                                                    echo ' (' . wc_price($occp_drag_current_product_price) .')';
                                                    ?>
                                                </span>
                                                <div class="occp_qty_box">
                                                    <input type="hidden" name="occp_drag_ids[]" value="<?php echo $occp_drag_current_product_id; ?>">
                                                    <input type="number" name="occp_off_per[<?php echo $occp_drag_current_product_id ?>]" value="<?php foreach($occp_drag_current_product_discunt as $key => $val){ if($key == $occp_drag_current_product_id){ echo $val; } } ?>">
                                                    <select name="occp_discount_type[<?php echo $occp_drag_current_product_id ?>]">
                                                        <option value="fixed" <?php if(!empty($occp_drag_current_product_discunt_type)){foreach($occp_drag_current_product_discunt_type as $key => $val){ if($key == $occp_drag_current_product_id && $val == "fixed"){ echo "selected"; } } }?>>
                                                            Fixed
                                                        </option>
                                                        <option value="percentage" <?php if(!empty($occp_drag_current_product_discunt_type)){foreach($occp_drag_current_product_discunt_type as $key => $val){ if($key == $occp_drag_current_product_id && $val == "percentage"){ echo "selected"; } } } ?>>
                                                            Percentage
                                                        </option>
                                                    </select>
                                                </div>
                                            </li>
                                            <?php 
                                        }
                                    }
                                ?>
                            </ul>
                        </div>
                    </div>
                    <p class='form-field'>
                        <label><?php _e( 'Layout', 'woocommerce' ); ?></label>
                        <input type="radio" name="rdlayout" value="layout1" <?php if(get_post_meta( get_the_ID(), 'occp_layout', true ) == "layout1"){ echo "checked"; } ?>>   Layout1
                        <input type="radio" name="rdlayout" value="layout2" <?php if(get_post_meta( get_the_ID(), 'occp_layout', true ) == "layout2" || empty(get_post_meta( get_the_ID(), 'occp_layout', true ))){ echo "checked"; } ?>>   Layout2
                        <input type="radio" name="rdlayout" value="none" <?php if(get_post_meta( get_the_ID(), 'occp_layout', true ) == "none"){ echo "checked"; } ?>>   None (<strong>None Stand for </strong>not showing any layout if you want to  use custom place on it than you can use <strong>[Woo_Frequently_added]</strong> this shortcode)
                    </p>
                    <p class='form-field'>
                        <label><?php _e( 'Heading Text', 'woocommerce' ); ?></label>
                        <input type="text" name="occp_head_txt" value="<?php if(!empty(get_post_meta( get_the_ID(), 'occp_head_txt', true ))){ echo get_post_meta( get_the_ID(), 'occp_head_txt', true ); }  ?>">
                    </p>
                </div>
            </div>
            <?php
        }

   
        function occp_search_product_ajax(){
      
            $return = array();
            $post_types = array( 'product','product_variation');
            $except = $_GET['except'];
         
            $search_results = new WP_Query( array( 
                's'=> $_GET['q'], // the search query
                'post_status' => 'publish',
                'post_type' => $post_types,
                'posts_per_page' => -1,
                'post__not_in' => array($except),
                'post_parent__not_in' => array($except),
                'meta_query' => array(
                                    array(
                                        'key' => '_stock_status',
                                        'value' => 'instock',
                                        'compare' => '=',
                                    )
                                )
                ) );
             

            if( $search_results->have_posts() ) :
                while( $search_results->have_posts() ) : $search_results->the_post();   
                    $productc = wc_get_product( $search_results->post->ID );
                    if ( $productc && $productc->is_in_stock() && $productc->is_purchasable() ) {
                        if( !$productc->is_type( 'variable' )) {
                            $title = $search_results->post->post_title;
                            $price = $productc->get_price_html();
                            $return[] = array( $search_results->post->ID, $title, $price);
                        }
                    }
                endwhile;
            endif;
            echo json_encode( $return );
            die;
        }


        function occp_save_proddata_custom_fields($post_id) {
            $select = $this->recursive_sanitize_text_field($_POST['occp_drag_ids']);
            update_post_meta( $post_id, 'occp_select2', (array) $select );

            $layout = sanitize_text_field( $_POST['rdlayout'] );
            update_post_meta( $post_id, 'occp_layout', $layout );

            $head = sanitize_text_field( $_POST['occp_head_txt'] );
            update_post_meta( $post_id, 'occp_head_txt', $head );

            $occp_off_per = $this->recursive_sanitize_text_field($_POST['occp_off_per']);
            update_post_meta( $post_id, 'occp_off_per', (array) $occp_off_per );
            
            $occp_discount_type = $this->recursive_sanitize_text_field($_POST['occp_discount_type']);
            update_post_meta( $post_id, 'occp_discount_type', (array) $occp_discount_type );
        }
     

        function recursive_sanitize_text_field($array) {
            foreach ( $array as $key => &$value ) {
                if ( is_array( $value ) ) {
                    $value = $this->recursive_sanitize_text_field($value);
                }else{
                    $value = sanitize_text_field( $value );
                }
            }
            return $array;
        }


        function init() {
            add_action( 'plugins_loaded', array($this, 'occp_free_install'), 11 );
            add_filter( 'woocommerce_product_data_tabs', array($this, 'occp_custom_product_tabs') );
            add_action( 'woocommerce_product_data_panels', array($this, 'occp_custom_product_tabs_fields') );
            add_action( 'wp_ajax_nopriv_occp_search_product_ajax',array($this, 'occp_search_product_ajax') );
            add_action( 'wp_ajax_occp_search_product_ajax', array($this, 'occp_search_product_ajax') );
            add_action( 'woocommerce_process_product_meta', array($this, 'occp_save_proddata_custom_fields') );
        }


        public static function instance() {
            if (!isset(self::$instance)) {
                self::$instance = new self();
                self::$instance->init();
            }
            return self::$instance;
        }
    }
    OCCP_menua::instance();
}
