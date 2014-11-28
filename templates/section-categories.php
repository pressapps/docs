<?php

global $post, $helpdesk, $meta;
$meta = redux_post_meta( 'helpdesk', get_the_ID() );

$section_categories_include = 'list';
$section_categories_columns = 3;
$col_class = 4;
$i    = 0;

$title = $meta['section_categories_title'];
if (isset($meta['section_categories_include']) && $meta['section_categories_include'] != '') {
    $section_categories_include = implode(",", $meta['section_categories_include']);
}
if (isset($meta['section_categories_columns']) && $meta['section_categories_columns'] != '') {
    $section_categories_columns = $meta['section_categories_columns'];
    if ($section_categories_columns == 2) {
        $col_class = 6;
    } elseif ($section_categories_columns == 4) {
        $col_class = 3;
    } elseif ($section_categories_columns == 6) {
        $col_class = 2;
    }
} 

$categories = get_categories(array(
    'orderby'         => 'slug',
    'order'           => 'ASC',
    'include'         => $section_categories_include,
    'pad_counts'  => 1,
)); 

$categories = wp_list_filter($categories,array('parent'=>0));
?>

<section class="section-categories">
    <div class="container1">
        <?php
        if ($title) {
            echo '<h2 class="section-title">' . $title . '</h2>';
        }
        ?>
        <ul class="procedures">
        <?php
        foreach($categories as $category) { 
            
            $term_id        = array();
            $term_id[]      = $category->term_id;

            ?>
            <li class="col-sm-<?php echo $col_class; ?> half-gutter-col">
        	    <a href="<?php echo get_category_link($category->term_id); ?>" title="<?php echo $category->name; ?>" class="box">
        	        <h3><?php echo $category->name; ?></h3>
        	        <p><?php echo $category->description; ?></p>
        	    </a>
        	</li>
            <?php		
            
           
        }
        wp_reset_query();
        ?>
        </ul>
    </div>
</section>