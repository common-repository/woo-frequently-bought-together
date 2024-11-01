<?php

if (!defined('ABSPATH'))
    exit;

if (!class_exists('OCCP_front')) {

    class OCCP_front {

        protected static $instance;


        function occp_add_to_cart_form() {
            $product = get_post_meta( get_the_ID(), 'occp_select2', true );
            $occp_discunt = get_post_meta( get_the_ID(), 'occp_off_per', true );
            $occp_discunt_type = get_post_meta( get_the_ID(), 'occp_discount_type', true );
            if(empty($product)){
              return;
            } 
            $main_product = wc_get_product( get_the_ID() );
            array_unshift($product, get_the_ID());
            $count  = 0;
            $badge ='';
            $product_details = '';
            $total= 0;
            $images = '';
            foreach ($product as $productId) {
                $product = wc_get_product( $productId );

                $current_product_link =  $product->get_permalink();
                $current_product_image = $product->get_image();
                $current_product_title = $product->get_title();
                $current_product_price = $product->get_price();
				$current_product_id = $product->get_id();
                $current_product_is_variation   = $product->is_type( 'variation' );

                $current_product_discount='';
                $current_product_discount_type='';
                if(!empty($occp_discunt[$current_product_id])){
                    $current_product_discount = $occp_discunt[$current_product_id];
                }
                if(!empty($occp_discunt_type[$current_product_id])){
                    $current_product_discount_type = $occp_discunt_type[$current_product_id];
                }
                

                $current_product_exact_price = $this->occp_get_price($current_product_price, $current_product_discount, $current_product_discount_type);
                if($count == 0){           
                    $current_product_exact_prices = 0;
           		}else{
                    $current_product_exact_prices = $current_product_exact_price;
                }
           		
                $dis_type = get_post_meta( get_the_ID(), 'occp_discount_type' );
                $dis_amt = get_post_meta( get_the_ID(), 'occp_off_per' );

                if(!empty($dis_amt[0][$current_product_id])) {
                    if(get_option('woocommerce_currency_pos') == 'left' || get_option('woocommerce_currency_pos') == 'left_space'){
                        if($dis_type[0][$current_product_id] == "percentage") {
                            $badge = '<div class="dis_badge"><span><p>off</p>- '.$dis_amt[0][$current_product_id].' %</span></div>';
                        }else if($dis_type[0][$current_product_id] == "fixed"){
                            $badge = '<div class="dis_badge"><span><p>off</p>- '.get_woocommerce_currency_symbol().$dis_amt[0][$current_product_id].'</span></div>';
                        }
                    }else{
                        if($dis_type[0][$current_product_id] == "percentage") {
                            $badge = '<div class="dis_badge"><span><p>off</p>- '.$dis_amt[0][$current_product_id].' %</span></div>';
                        }else if($dis_type[0][$current_product_id] == "fixed"){
                            $badge = '<div class="dis_badge"><span><p>off</p>- '.$dis_amt[0][$current_product_id].get_woocommerce_currency_symbol(). '</span></div>';
                        }
                    }  
                }

                $images .= '<td class="oocp-image sss" image_pro_id="'.$current_product_id.'"><a href="' . $current_product_link . '">' . $current_product_image . '</a>'.$badge.'</td>';
                ob_start();
                ?>
                <li class="occp_combo_item">
                    <label for="comboId_<?php echo $count ?>">

                        
                        <input type="checkbox" name="proID[]" id="proID_<?php echo $count ?>" class="product_check" value="<?php echo $current_product_id; ?>" price="<?php echo $current_product_exact_price ; ?>" checked <?php if($count == 0){ echo "disabled"; } ?> disabled/>
                        <span class="occp_product_name">
                            <a href="<?php echo $current_product_link; ?>"><?php echo $current_product_title; ?></a>
                        </span>
                        <?php
                            if( $current_product_is_variation ) {
                                $attributes = $product->get_variation_attributes();
                                $variations = array();

                                foreach( $attributes as $key => $attribute ) {
                                    $variations[] = $attribute;
                                }

                                if( ! empty( $variations ) )
                                echo '<span class="product-attributes"> &ndash; ' . implode( ', ', $variations ) . '</span>';
                            }
                            
                            if(!empty($product->get_price())) { 
                                $price = wc_price($product->get_price()); 
                            }else { 
                                $price = wc_price(0);
                            }
                            echo ' &ndash; <span class="occp_price_old">' . $price . '</span><span class="occp_price_new">('. wc_price($current_product_exact_price) .')</span>';
                        ?>
                    </label>
                </li>
                <?php
                $product_details .= ob_get_clean();
                // increment total
                $total += floatval( $current_product_exact_prices );
                $count++;
            }
            ?>

            <input type="hidden" name="formate" value="<?php echo get_woocommerce_currency_symbol(); ?>" class="formate">
            <input type="hidden" name="layout" value="layout1" class="layout">
            <!-- <form class="occp-product-form" method="post" action=""> -->
            	<h3><?php echo get_post_meta( get_the_ID(), 'occp_head_txt', true ); ?></h3>
                <table class="occp-product-images">
                    <tbody>
                        <tr>
                            <?php echo $images; ?>
                        </tr>
                    </tbody>
                </table>
                <ul class="occp_combo occp_main_theme">
                    <?php echo $product_details; ?>
                </ul>
                <div class="occp_add_cart_div occp_main_theme" style="margin-bottom: 10px;">
                    <div class="occp_price">
                        <span class="occp_price_label">
                            <?php echo "Price for all : "; ?>
                        </span>
                        &nbsp;
                        <span class="occp_price_total" data-total="<?php echo $total ?>">
                            <?php echo wc_price( $total ); ?>
                        </span>
                    </div>
                </div>
            <!-- </form> -->
            <?php   
        }

        function occp_after_add_to_cart_form() {
        	
            
            $product = get_post_meta( get_the_ID(), 'occp_select2', true );
            $occp_discunt = get_post_meta( get_the_ID(), 'occp_off_per', true );
            $occp_discunt_type = get_post_meta( get_the_ID(), 'occp_discount_type', true );

            if(empty($product)){
              return;
            }

            $main_product = wc_get_product( get_the_ID() );
            if( $main_product->has_child() ){
              $product_variable = new WC_Product_Variable( get_the_ID() );
              $variations = $product_variable->get_available_variations();
              $vari = 0;
              foreach ($variations as $variation) {
                $vari++;
                if($vari == 1){
                  if (in_array($variation['variation_id'], $product)){ 
                    
                  }else{
                    array_unshift($product,$variation['variation_id']);
                  }
                }
              }
            }else{
              array_unshift($product, get_the_ID());
            }
            $count = 0;
            $badge ='';
            $product_details = '';
            $total= 0;
            $images = '';
            foreach ($product as $productId) {
                $product = wc_get_product( $productId );
                $current_product_link = $product->get_permalink();
                $current_product_image = $product->get_image();
                $current_product_title = $product->get_title();
                $current_product_price = $product->get_price();
                $current_product_id = $product->get_id();
                $current_product_is_variation   = $product->is_type( 'variation' );
                $current_product_discount='';
                $current_product_discount_type='';
                 if(!empty($occp_discunt[$current_product_id])){
                     $current_product_discount = $occp_discunt[$current_product_id];
                 }
                if(!empty($occp_discunt_type[$current_product_id])){
                    $current_product_discount_type = $occp_discunt_type[$current_product_id];
                 }
                
				$current_product_exact_price = $this->occp_get_price($current_product_price, $current_product_discount, $current_product_discount_type);
              


                $dis_type = get_post_meta( get_the_ID(), 'occp_discount_type' );
                $dis_amt = get_post_meta( get_the_ID(), 'occp_off_per' );
                
                if(!empty($dis_amt[0][$current_product_id])) {
                    if(get_option('woocommerce_currency_pos') == 'left' || get_option('woocommerce_currency_pos') == 'left_space'){
                        if($dis_type[0][$current_product_id] == "percentage") {
                            $badge = '<div class="dis_badge"><span><p>off</p>- '.$dis_amt[0][$current_product_id].' %</span></div>';
                        }else if($dis_type[0][$current_product_id] == "fixed"){
                            $badge = '<div class="dis_badge"><span><p>off</p>- '.get_woocommerce_currency_symbol().$dis_amt[0][$current_product_id].'</span></div>';
                        }
                    }else{
                        if($dis_type[0][$current_product_id] == "percentage") {
                            $badge = '<div class="dis_badge"><span><p>off</p>- '.$dis_amt[0][$current_product_id].' %</span></div>';
                        }else if($dis_type[0][$current_product_id] == "fixed"){
                            $badge = '<div class="dis_badge"><span><p>off</p>- '.$dis_amt[0][$current_product_id].get_woocommerce_currency_symbol(). '</span></div>';
                        }
                    }
                    
                }



                $images .= '<td class="oocp-image sss" image_pro_id="'.$current_product_id.'"><a href="' . $current_product_link . '">' . $current_product_image . '</a>'.$badge.'</td>';
                

               
                ob_start();
                ?>
                <li class="occp_combo_item">
                    <label for="comboId_<?php echo $count ?>">

                        <input type="checkbox" name="proID[]" id="proID_<?php echo $count ?>" class="product_check" value="<?php echo $current_product_id; ?>" price="<?php echo $current_product_exact_price; ?>" checked disabled/>
                        <span class="occp_product_name">
                            <a href="<?php echo $current_product_link; ?>"><?php echo $current_product_title; ?></a>
                        </span>
                        <?php
                            if( $current_product_is_variation ) {
                                $attributes = $product->get_variation_attributes();
                                $variations = array();

                                foreach( $attributes as $key => $attribute ) {
                                    $variations[] = $attribute;
                                }

                                if( ! empty( $variations ) )
                                echo '<span class="product-attributes"> &ndash; ' . implode( ', ', $variations ) . '</span>';
                            }
                           
                            if(!empty($product->get_price())) { 
                                $price = wc_price($product->get_price()); 
                            }else { 
                                $price = wc_price(0);
                            }
                            echo ' &ndash; <span class="occp_price_old">' . $price . '</span><span class="occp_price_new">('. wc_price($current_product_exact_price) .')</span>';
                        ?>

                    </label>
                </li>
                <?php
                $product_details .= ob_get_clean();
                // increment total
                $total += floatval( $current_product_exact_price );
                $count++;
            }
            ?>
        	<input type="hidden" name="formate" value="<?php echo get_woocommerce_currency_symbol(); ?>" class="formate">
            <input type="hidden" name="layout" value="layout2" class="layout">
        	<form class="occp-product-form" method="post" action="">
            	<h3><?php echo get_post_meta( get_the_ID(), 'occp_head_txt', true ); ?></h3>
                <table class="occp-product-images">
                    <tbody>
                        <tr>
                            <?php echo $images; ?>
                            
                        </tr>
                    </tbody>
                </table>
                <ul class="occp_combo">
                    <?php echo $product_details; ?>
                </ul>
               
                <div class="occp_add_cart_div" style="margin-bottom: 10px;">
                    <div class="occp_price">
                        <span class="occp_price_label">
                            <?php echo "Price for all : "; 
                            ?>
                        </span>
                        &nbsp;
                        <span class="occp_price_total" data-total="<?php echo $total ?>">
                            <?php echo wc_price( $total ); ?>
                        </span>
                    </div>
                    <input type="submit" class="occp_add_cart_button button" value="<?php echo "Add To Cart"; ?>" name="occp_add_to_cart">
                </div>
                            
            </form>
            <?php
        }

        function occp_get_price($price,$discount,$discount_type) {
        	if(empty($price)){
        		$price = 0;
        	}else{
        		if(empty($discount)){
        			$price = $price;
        		}else{
        			if($discount_type == "percentage"){
	        			$price = $price - ( $price * $discount / 100 );
	        		}else{
	        			$price = $price - $discount;
	        		}
        		}
        	}
        	return $price;
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

        function occp_add_cart_item_data( $cart_item_data, $product_id ) {
       		$product_cust =  $_POST['proID'];
			if( empty( $product_cust ) ) {
                return;
            }
            
            $combo_ids = $product_cust;
            if ( ! empty( $combo_ids ) ) {
                $cart_item_data['combo_ids'] = $combo_ids;
            }
            return $cart_item_data;
        }

        function occp_add_to_cart( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
            if ( isset( $cart_item_data['combo_ids'] ) && ( $cart_item_data['combo_ids'] !== '' ) ) {

                $items = $cart_item_data['combo_ids'];

                remove_action( 'woocommerce_add_to_cart', array( $this, 'occp_add_to_cart' ), 10, 6 ); 

                foreach ($items as $keya => $valuea) {
                    $occp_product = wc_get_product( $valuea );
                    if ( $occp_product && $occp_product->is_in_stock() && $occp_product->is_purchasable() ) {
                        $cart_item_keya = WC()->cart->add_to_cart( $valuea, 1,0,array(),array("is_freq_add"=>$product_id) );
                    }  
                }
                
            }
        }

        function occp_iconic_add_to_cart() {
            global $woocommerce;
            $occp_main_id = get_the_ID();
            if(isset($_REQUEST['occp_add_to_cart'])){

                $product_cust = $this->recursive_sanitize_text_field( $_POST['proID'] );
				if( empty( $product_cust ) ) {
                    return;
                }
                
                remove_action( 'woocommerce_add_to_cart', array( $this, 'occp_add_to_cart' ), 10, 6 );
                foreach( $product_cust as $id ) {
                    $occp_product = wc_get_product( $id );
                    if ( $occp_product && $occp_product->is_in_stock() && $occp_product->is_purchasable() ) {
                        $cart_item_keya = WC()->cart->add_to_cart( $id, 1,0,array(),array("is_freq_add"=>$occp_main_id) );
                    } 
                }
                
                $cart_url = $woocommerce->cart->get_cart_url();
                wp_redirect( $cart_url );
            }
        }

        function occp_get_page_id() {
            $page_security = get_queried_object();
            if($page_security->post_type == "product"){
                if(get_post_meta( $page_security->ID, 'occp_layout', true ) == "layout1"){
                    $product = wc_get_product($page_security->ID);
                    if($product->is_type( 'variable' )){
                        add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'occp_add_to_cart_form' ) );
                    }else{
                        add_action( 'woocommerce_before_add_to_cart_quantity', array( $this, 'occp_add_to_cart_form' ) );
                    }     
                }
                if(get_post_meta( $page_security->ID, 'occp_layout', true ) == "layout2"){
                    add_filter( 'woocommerce_after_single_product_summary', array($this, 'occp_after_add_to_cart_form'), 5);
                }
            }
        }

        function occp_custom_price_to_cart_item( $cart_object ) {  
		    if( !WC()->session->__isset( "reload_checkout" )) {
			    foreach ( $cart_object->get_cart() as $key => $value ) {
			    	//echo "";
			    	if( isset( $value["is_freq_add"] ) ) {
			    		$product_id = $value['data']->get_id();

			    		$ID = $value["is_freq_add"];
				    	$product = get_post_meta( $ID, 'occp_select2', true );
				    	$occp_discunt = get_post_meta( $ID, 'occp_off_per', true );
	            		$occp_discunt_type = get_post_meta( $ID, 'occp_discount_type', true );
	            		
			    		
		    			$product = wc_get_product( $product_id );
           				$price = $product->get_price();
           				$discount = $occp_discunt[$product_id];
           				$discount_type = $occp_discunt_type[$product_id];
           				$exact_price = $this->occp_get_price($price, $discount, $discount_type);
               				
               			$value['data']->set_price( $exact_price );
			    		
				    }
			    } 
		    }   
		}

        function occp_woo_combo($atts, $content = null) {
            $page_security = get_queried_object();
            if(get_post_meta( $page_security->ID, 'occp_layout', true ) == "none"){
                $product = get_post_meta( get_the_ID(), 'occp_select2', true );
            $occp_discunt = get_post_meta( get_the_ID(), 'occp_off_per', true );

            $occp_discunt_type = get_post_meta( get_the_ID(), 'occp_discount_type', true );

            if(empty($product)){
              return;
            }

            $main_product = wc_get_product( get_the_ID() );
            if( $main_product->has_child() ){
              $product_variable = new WC_Product_Variable( get_the_ID() );
              $variations = $product_variable->get_available_variations();
              $vari = 0;
              foreach ($variations as $variation) {
                $vari++;
                if($vari == 1){
                  if (in_array($variation['variation_id'], $product)){ 
                    
                  }else{
                    array_unshift($product,$variation['variation_id']);
                  }
                }
              }
            }else{
              array_unshift($product, get_the_ID());
            }
            $count  = 0;
           
            foreach ($product as $productId) {
                $product = wc_get_product( $productId );
                $current_product_link = $product->get_permalink();
                $current_product_image = $product->get_image();
                $current_product_title = $product->get_title();
                $current_product_price = $product->get_price();
                $current_product_id = $product->get_id();
                $current_product_is_variation   = $product->is_type( 'variation' );

                $current_product_discount='';
                $current_product_discount_type='';
                if(!empty($occp_discunt[$current_product_id])){
                    $current_product_discount = $occp_discunt[$current_product_id];
                }
                if(!empty($occp_discunt_type[$current_product_id])){
                    $current_product_discount_type = $occp_discunt_type[$current_product_id];
                }
                
               
                $current_product_exact_price = $this->occp_get_price($current_product_price, $current_product_discount, $current_product_discount_type);
               
               
                $dis_type = get_post_meta( get_the_ID(), 'occp_discount_type' );
                $dis_amt = get_post_meta( get_the_ID(), 'occp_off_per' );

                if(!empty($dis_amt[0][$current_product_id])) {
                    if(get_option('woocommerce_currency_pos') == 'left' || get_option('woocommerce_currency_pos') == 'left_space'){
                        if($dis_type[0][$current_product_id] == "percentage") {
                            $badge = '<div class="dis_badge"><span><p>off</p>- '.$dis_amt[0][$current_product_id].' %</span></div>';
                        }else if($dis_type[0][$current_product_id] == "fixed"){
                            $badge = '<div class="dis_badge"><span><p>off</p>- '.get_woocommerce_currency_symbol().$dis_amt[0][$current_product_id].'</span></div>';
                        }
                    }else{
                        if($dis_type[0][$current_product_id] == "percentage") {
                            $badge = '<div class="dis_badge"><span><p>off</p>- '.$dis_amt[0][$current_product_id].' %</span></div>';
                        }else if($dis_type[0][$current_product_id] == "fixed"){
                            $badge = '<div class="dis_badge"><span><p>off</p>- '.$dis_amt[0][$current_product_id].get_woocommerce_currency_symbol(). '</span></div>';
                        }
                    }
                    
                }



                $images .= '<td class="oocp-image sss" image_pro_id="'.$current_product_id.'"><a href="' . $current_product_link . '">' . $current_product_image . '</a>'.$badge.'</td>';
                
                ob_start();
                ?>
                <li class="occp_combo_item">
                    <label for="comboId_<?php echo $count ?>">

                        <input type="checkbox" name="proID[]" id="proID_<?php echo $count ?>" class="product_check" value="<?php echo $current_product_id; ?>" price="<?php echo $current_product_exact_price; ?>" checked disabled/>
                        <span class="occp_product_name">
                            <a href="<?php echo $current_product_link; ?>"><?php echo $current_product_title; ?></a>
                        </span>
                        <?php
                            if( $current_product_is_variation ) {
                                $attributes = $product->get_variation_attributes();
                                $variations = array();

                                foreach( $attributes as $key => $attribute ) {
                                    $variations[] = $attribute;
                                }

                                if( ! empty( $variations ) )
                                echo '<span class="product-attributes"> &ndash; ' . implode( ', ', $variations ) . '</span>';
                            }
                            // echo product price
                            if(!empty($product->get_price())) { 
                                $price = wc_price($product->get_price()); 
                            }else { 
                                $price = wc_price(0);
                            }
                            echo ' &ndash; <span class="occp_price_old">' . $price . '</span><span class="occp_price_new">('. wc_price($current_product_exact_price) .')</span>';
                        ?>

                    </label>
                </li>
                <?php
                $product_details .= ob_get_clean();
                // increment total
                $total += floatval( $current_product_exact_price );
                $count++;
            }
            ?>
            <input type="hidden" name="formate" value="<?php echo get_woocommerce_currency_symbol(); ?>" class="formate">
            <input type="hidden" name="layout" value="layout2" class="layout">
            <form class="occp-product-form" method="post" action="">
                <h3><?php echo get_post_meta( get_the_ID(), 'occp_head_txt', true ); ?></h3>
                <table class="occp-product-images">
                    <tbody>
                        <tr>
                            <?php echo $images; ?>
                        </tr>
                    </tbody>
                </table>
                <ul class="occp_combo">
                    <?php echo $product_details; ?>
                </ul>
                <div class="occp_add_cart_div" style="margin-bottom: 10px;">
                    <div class="occp_price">
                        <span class="occp_price_label">
                            <?php echo "Price for all : "; ?>
                        </span>
                        &nbsp;
                        <span class="occp_price_total" data-total="<?php echo $total ?>">
                            <?php echo wc_price( $total ); ?>
                        </span>
                    </div>
                    <input type="submit" class="occp_add_cart_button button" value="<?php echo "Add To Cart"; ?>" name="occp_add_to_cart">
                </div>
            </form>
            <?php
            }
        }

      
        function init() {
            add_action( 'wp_head', array($this, 'occp_get_page_id') );
            add_filter( 'woocommerce_add_cart_item_data', array( $this, 'occp_add_cart_item_data' ), 10, 3 );
            add_action( 'woocommerce_add_to_cart', array( $this, 'occp_add_to_cart' ), 10, 6 ); 
            add_action( 'template_redirect', array($this, 'occp_iconic_add_to_cart') );
            add_action( 'woocommerce_before_calculate_totals', array($this, 'occp_custom_price_to_cart_item') , 99 );         
            add_shortcode( 'Woo_Frequently_added', array($this,'occp_woo_combo'));
        }

        public static function instance() {
            if (!isset(self::$instance)) {
                self::$instance = new self();
                self::$instance->init();
            }

            return self::$instance;
        }

    }

    OCCP_front::instance();
}





