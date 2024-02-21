<?php

defined('ABSPATH') || exit;

global $rz_explore;

$promoted_type = get_option('rz_promoted_listings_display');

$is_avis = isset($_GET['type']) && $_GET['type'] == 'avis';
$is_achatgrp = isset($_GET['type']) && $_GET['type'] == 'achatgrp';
?>

<div class="rz-dynamic rz-dynamic-listings">

    <div class="rz-explore-listings">

        <?php if( $rz_explore->total_types ): ?>

            <?php if( $promoted_type == 'random' ): ?>
                <?php Rz()->the_template('routiz/explore/listings-random-priority'); ?>
            <?php endif; ?>

            <?php if( $rz_explore->query()->posts->have_posts() ): ?>

                <div class="brk-listing-summary">
                    <div class="brk--viewing">
                        <?php

                            global $rz_explore;

                            $from = ( $rz_explore->query()->page - 1 ) * $rz_explore->query()->posts_per_page + 1;
                            $to = $from + $rz_explore->query()->posts_per_page - 1;

                        ?>
                        <p>
                            <?php echo sprintf(
                                esc_html__( 'Showing %s &ndash; %s of %s results', 'brikk' ),
                                $from,
                                $to > $rz_explore->query()->posts->found_posts ? $rz_explore->query()->posts->found_posts : $to,
                                $rz_explore->query()->posts->found_posts
                            ); ?>
                        </p>
                    </div>
                    <div class="brk--sorting">
                        <?php
                        if (isset($_GET['type']) && $_GET['type'] == 'avis') {
                            // 2 sort methods: by priority & newest, by review score, if $_get['sortBy'] == 'score' we display a link to sortBy=''
                            if (isset($_GET['sortBy']) && $_GET['sortBy'] == 'score') {
                                echo 'Triés par: <a href="' . esc_url(add_query_arg('sortBy', '')) . '">meilleure note <i class="fas fa-retweet"></i></a>';
                            } else {
                                echo 'Triés par: <a href="' . esc_url(add_query_arg('sortBy', 'score')) . '">les plus récents <i class="fas fa-retweet"></i></a>';
                            }
                        } else {
                           echo esc_html_e('Sorted by priority & newest', 'brikk');
                        }
                        ?>
                    </div>
                </div>

                <?php

                    $cols = 5;

                    if( $rz_explore->get_display_type() == 'map' ) {
                        $cols = 2;
                    }

                ?>

                <ul class="rz-listings" data-cols="<?php echo (int) $cols; ?>" data-is-avis="<?php echo $is_avis ? 'true' : 'false'; ?>" data-is-achatgrp="<?php echo $is_achatgrp ? 'true' : 'false'; ?>">
                    <?php while( $rz_explore->query()->posts->have_posts() ): $rz_explore->query()->posts->the_post(); ?>
                        <li class="rz-listing-item <?php Rz()->listing_class(); ?>">
                            <?php Rz()->the_template('routiz/explore/listing/listing'); ?>
                        </li>
                        <?php Rz()->the_template('routiz/explore/listing/banner'); ?>
                    <?php endwhile; wp_reset_postdata(); ?>
                </ul>

                <?php Rz()->the_template('routiz/explore/paging'); ?>

            <?php else: ?>

                <h5><?php esc_html_e( 'No results were found', 'brikk' ); ?></h5>

            <?php endif; ?>

        <?php else: ?>

            <h5><?php esc_html_e( 'No listing types were found', 'brikk' ); ?></h5>

        <?php endif; ?>

    </div>

</div>
