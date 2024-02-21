<?php

if( Brk()->get_meta('rz_hide_heading') ) {
    return;
}

// $title = get_the_title();
$title = single_post_title( '', false );

if( is_category() or is_tag() or is_date() ) {
    $title = get_the_archive_title();
}

if( is_404() ) {
    $title = 404;
}

if( is_home() ) {
    $title = esc_html__( 'Blog', 'brikk' );
}

if( is_search() ) {
    $title = sprintf( esc_html__( 'Searching for `%s`', 'brikk' ), get_search_query() );
}

if( is_author() ) {
    $author = get_queried_object();

    $args = array(
        'post_type' => 'abonnement',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'proprietaire',
                'value' => $author->ID,
                'compare' => '='
            ),
            array(
                'key' => 'date_fin',
                'value' => date('Ymd'),
                'compare' => '>='
            ),
        )
    );

    $is_pro = count(get_posts( $args )) > 0;

    if($is_pro) {
        $societe = get_field('societe', 'user_'.$author->ID);

        if(!empty($societe)) {
            $title = esc_html( $societe );
        }
    } else {
        $title = $author->display_name;
    }
}

if( class_exists( 'WooCommerce' ) and is_shop() ) {
    $title = esc_html__( 'Shop', 'brikk' );
}

?>

<?php if( ! Brk()->get_meta( 'disable_page_title' ) ): ?>
    <header class="brk-page-title">
        <div class="brk-row">
            <?php if( is_single() and get_post_type() == 'post' ): ?>
                <div class="brk-categories">
                    <?php $categories = get_the_category(); ?>
                    <?php if( $categories ): ?>
                        <ul>
                            <?php foreach( $categories as $category ): ?>
                                <li>
                                    <a href="<?php echo esc_url( get_category_link( $category->term_id ) ) ?>">
                                        <?php echo esc_html( $category->name ); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if( $title ): ?>
    	    	<h1 class="brk--title">
    				<?php echo do_shortcode( $title ); ?>
    	    	</h1>
            <?php endif; ?>

            <?php if( is_archive() && $term_description = term_description() ): ?>
                <div class="rz-row brk-boxed">
                    <p><?php echo do_shortcode( wp_kses_post( $term_description ) ); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </header>
<?php endif; ?>
