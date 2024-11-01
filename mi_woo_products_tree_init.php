<?php
/*
Plugin Name: Woo Products Tree
Plugin URI: http://meteo-is.in.ua/projects/woo-products-tree/
Description: Woo Products Tree is a simple plugin which adds a widget displaying navigation tree of products on your WooCommerce store. Look for "METEO-IS. Woo Products Tree" in your widgets list.
Version: 1.0
Author: METEO-IS, Denis Pishniak, Dmytriyenko Vyacheslav
Author URI: http://meteo-is.in.ua/
License: GPL2 
 
Copyright 2016  Denis Pishniak  (email: den.meteo.is@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/



add_action("widgets_init", function () {
    register_widget("TextWidget");      
});


class TextWidget extends WP_Widget
{

    /**
     * Category ancestors.
     *
     * @var array
     */
    public $cat_ancestors;

    /**
     * Current Category.
     *
     * @var bool
     */
    public $current_cat;


    public function __construct() {
        parent::__construct("text_widget", "METEO-IS. Woo Products Tree",
            array("description" => "Displaying navigation tree of products and categories for WooCommerce store."));

        function mi_scripts_ctyles(){ 
            wp_register_script( 'prodtree-script', plugins_url( '/js/scripts.js', __FILE__ ), '20161108', true ); 
            wp_register_style( 'prodtree-style', plugins_url( '/css/styles.css', __FILE__ ), array(), '20161108', 'all' );  
            wp_enqueue_script( 'prodtree-script' );  
            wp_enqueue_style( 'prodtree-style' );  
        } 
        add_action( 'wp_enqueue_scripts', 'mi_scripts_ctyles' );


        function showProductsList($catid, $current_postID, $instance){ 

            $args = array( 
                'post_type' => 'product',
                'posts_per_page' => '-1',
                'orderby' => 'name',
                'order' => 'ASC',
                'tax_query' => array( 
                    array( 
                    'taxonomy' => 'product_cat', 
                    'field' => 'id', 
                    'terms' => $catid
                    ) 
                ) 
            ); 

            echo "<ul class='mi-catprodlist'>";
            query_posts( $args );
            while(have_posts()) :
                 the_post();
                 $postinf = get_the_terms($post->ID, 'product_cat');
                 $last_post_cat = end($postinf)->term_id;
                 $postid = get_the_ID();
                 if ($last_post_cat == $catid) : 
                    global $product; 
       
        echo '<li class="mi-prod ';
        if ($postid == $current_postID)     
            echo 'mi-currentprod';
        echo '" style="';
        if ($instance['show_p_img'])        
            echo 'min-height:'.$instance['p_img_size'].'px; ';
        if ($instance['show_lmarks'])        
            echo 'list-style-type: disc;';
        if (!$instance['show_lmarks'])        
            echo 'list-style-type: none;';
        echo '">';
        echo '<a class="mi-prodlink" href="'.esc_url(get_permalink($product->id)).'" title="'
            .esc_attr( $product->get_title()).'">';
        if ($instance['show_p_img']){
            if ($instance['p_img_posi'] == left)
            echo'<img width="'.$instance['p_img_size'].'height="'.$instance['p_img_size']
                .'" style="float:'.$instance['p_img_posi'].'"'.$product->get_image();
            if ($instance['p_img_posi'] == right)
            echo'<img width="'.$instance['p_img_size'].'height="'.$instance['p_img_size']
                .'" style="float:'.$instance['p_img_posi'].'; margin-right:0"'.$product->get_image();
        }
        echo '<span class="product-title">'.$product->get_title().' </span></a>';
        if ($instance['show_price'])
            echo $product->get_price_html(); 
        echo '</li>';
                        
                 endif; 
             endwhile;
             echo "</ul>";
             echo "</div>";
             echo "</div>";
        }  

    }



    public function form($instance) {

        $defaults = array( 
            'title'         => "Shop Products Tree", 
            'show_counts'   => off,
            'show_price'    => off,
            'show_p_img'    => on,
            'p_img_size'    => 30,
            'p_img_posi'    => "left",
            'show_lmarks'   => off,
            'tab_color1'     => "230,230,230",
            'tab_transp'     => "0.5",
            'show_borders'   => on,
        );
        $instance = wp_parse_args( (array) $instance, $defaults ); 
        $img_size = $instance['p_img_size'];
        $img_size = preg_replace("/[^0-9]/", '', $img_size);
        if ($img_size > 100) 
            $img_size = 100;

        echo '<p><label for="'.$this->get_field_id("title").'">Display Title:</label><br>';
        echo '<input id="'.$this->get_field_id("title").'" type="text" name="'
            .$this->get_field_name("title").'" value="'.$instance['title'].'" style="width:100%; "><br></p>';

        echo '<p><input class="checkbox" id="'.$this->get_field_id('show_counts').'" type="checkbox" '; 
        checked( $instance['show_counts'], on ); 
        echo 'name="'.$this->get_field_name('show_counts').'" />';
        echo '<label for="'.$this->get_field_id('show_counts').'">Show product counts</label><br></p>'; 

        echo '<p><input class="checkbox" id="'.$this->get_field_id('show_price').'" type="checkbox" '; 
        checked( $instance['show_price'], on ); 
        echo 'name="'.$this->get_field_name('show_price').'" />';
        echo '<label for="'.$this->get_field_id('show_price').'">Show price</label><br></p>'; 

        echo '<p><input class="checkbox" id="'.$this->get_field_id('show_p_img').'" type="checkbox" '; 
        checked( $instance['show_p_img'], on ); 
        echo 'name="'.$this->get_field_name('show_p_img').'" />';
        echo '<label for="'.$this->get_field_id('show_p_img').'">Show products image</label><br>';

        echo '<label for="'.$this->get_field_id("p_img_size").'">Image size in pixels (max 100): </label>';
        echo '<input id="'.$this->get_field_id("p_img_size").'" type="text" name="'
            .$this->get_field_name("p_img_size").'" value="'.$img_size.'" style="width:50px"><br>';

        echo '<label for="'.$this->get_field_id("p_img_posi").'">Image position: </label>';
        echo '<select id="'.$this->get_field_id("p_img_posi").'" name="'.$this->get_field_name('p_img_posi').'">
                <option value="'.$instance['p_img_posi'].'">'.$instance['p_img_posi'].'</option>';
        if ($instance['p_img_posi'] == left) echo '<option value="right">right</option>';
        else echo '<option value="left">left</option>';
        echo '</select></p>';

        echo '<p><input class="checkbox" id="'.$this->get_field_id('show_lmarks').'" type="checkbox" '; 
        checked( $instance['show_lmarks'], on ); 
        echo 'name="'.$this->get_field_name('show_lmarks').'" />';
        echo '<label for="'.$this->get_field_id('show_lmarks').'">Show list markers</label><br></p>'; 

        echo '<p><label for="'.$this->get_field_id("tab_color1").'">Tab color Red,Green,Blue (0-255): </label>';
        echo '<input id="'.$this->get_field_id("tab_color1").'" type="text" name="'
            .$this->get_field_name("tab_color1").'" value="'.$instance['tab_color1'].'" style="width:130px;
             background-color:rgb('.$instance['tab_color1'].')"><br>';
        echo '<label for="'.$this->get_field_id("tab_transp").'">Tab color transparency (0-1): </label>';
        echo '<input id="'.$this->get_field_id("tab_transp").'" type="text" name="'
            .$this->get_field_name("tab_transp").'" value="'.$instance['tab_transp'].'" style="width:50px;
             background-color:rgb('.$instance['tab_transp'].')"></p>';

        echo '<p><input class="checkbox" id="'.$this->get_field_id('show_borders').'" type="checkbox" '; 
        checked( $instance['show_borders'], on ); 
        echo 'name="'.$this->get_field_name('show_borders').'" />';
        echo '<label for="'.$this->get_field_id('show_borders').'">Show borders</label><br></p>';
    }



    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['show_counts'] = $new_instance['show_counts'];
        $instance['show_price'] = $new_instance['show_price'];
        $instance['show_p_img'] = $new_instance['show_p_img'];
        $instance['p_img_size'] = strip_tags( $new_instance['p_img_size'] );
        $instance['p_img_posi'] = $new_instance['p_img_posi'];
        $instance['show_lmarks'] = $new_instance['show_lmarks'];
        $instance['tab_color1'] = strip_tags( $new_instance['tab_color1'] );
        $instance['tab_transp'] = preg_replace('/[^\d.]/','',$new_instance['tab_transp']);
        $instance['show_borders'] = $new_instance['show_borders'];
        return $instance;
    }



    public function widget($args, $instance) {
        extract($args);
        global $wp_query, $post;
        $current_cat   = false;
        $cat_ancestors = array();
        $current_postID = 0;
        if ( is_tax( 'product_cat' ) ) {                
            $current_cat   = $wp_query->queried_object;
            $cat_ancestors = get_ancestors( $current_cat->term_id, 'product_cat' );
            $current_cat1ID = $current_cat->term_id;    
            $current_cat2ID = reset($cat_ancestors);    

        } elseif ( is_singular( 'product' ) ) {         
            $product_category = wc_get_product_terms( $post->ID, 'product_cat', 
                apply_filters( 'woocommerce_product_categories_widget_product_terms_args', array( 'orderby' => 'parent' ) ) );
            $current_postID = $post->ID;
            if ( ! empty( $product_category ) ) {
                $current_cat = $product_category;
                $current_cat1ID = reset($current_cat)->parent; 
                $current_cat2ID = reset($current_cat)->term_id;   
            }
        }

       
        $args = array(     
            'taxonomy'  => 'product_cat', 
            'orderby'   => 0,
            'parent'    => 0
        );
        $cats = get_categories( $args );

        echo $before_widget;
        $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        if ($title)
            echo $before_title . $title . $after_title;

        foreach ($cats as $cat) {

            echo '<div class="mi-cats ';
            if($instance['show_borders']) 
                echo 'mi-borders ';
            if ($cat->cat_ID == $current_cat1ID || $cat->cat_ID == $current_cat2ID)
                echo 'mi-currentcat" style="background-color: rgba('.$instance['tab_color1'].','.$instance['tab_transp'].')';
            echo '">';
            echo '<div id="mi-but'.$cat->cat_ID.'" class="mi-show-button" onclick="jsShowFunc(this)"></div>';
            echo '<h3 class="mi-catname"><a href='.get_category_link( $cat->cat_ID).'>'.$cat->cat_name.'</a>';
            if($instance['show_counts']) 
                echo ' ('.$cat->count.')'; 
            echo '</h3><div id="mi-cat'.$cat->cat_ID.'" class="mi-cats-content">';
            
            $enclosed_args = array(
              'taxonomy'     => 'product_cat', 
              'orderby'      => 0,  
              'title_li'     => '' ,      
              'child_of'    => $cat->cat_ID
            );
            $categories = get_categories( $enclosed_args ); 
            foreach($categories as $category) {
                echo '<div class="mi-cats2 ';
                if ($category->cat_ID == $current_cat1ID || $category->cat_ID == $current_cat2ID)
                    echo 'mi-currentcat" style="background-color: rgba('.$instance['tab_color1'].','.$instance['tab_transp'].')';
                echo '">';
                echo '<div id="mi-but'.$category->cat_ID.'" class="mi-show-button" onclick="jsShowFunc(this)"></div>';
                echo '<a href='.get_category_link( $category->cat_ID).'><h3 class="mi-catname">'.$category->cat_name.'</h3></a>';
                echo '<div id="mi-cat'.$category->cat_ID.'" class="mi-cats-content">';
                showProductsList($category->cat_ID, $current_postID, $instance);
            }

            showProductsList($cat->cat_ID, $current_postID, $instance);

        }   
    echo '<div id="mi-color1" style="background-color: rgba('.$instance['tab_color1'].','.$instance['tab_transp'].')"></div>';
    echo $after_widget;


    } 


}
?>