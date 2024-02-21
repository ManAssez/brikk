<?php get_header(); ?>

<div class="brk-container">

	<?php get_template_part('templates/title'); ?>

	<div class="brk-row">
		<main class="brk-main">
			<div class="brk-content">

				<?php if( function_exists('routiz') ): ?>

					<?php

						global $wpdb;

						$author = get_queried_object();
						$user = new \Routiz\Inc\Src\User( $author->ID );
						$user_avatar = $user->get_avatar();
						$user_cover = $user->get_cover();

						$listings_per_page = 6;
						$current_page = isset( $_GET['onpage'] ) ? (int) $_GET['onpage'] : 1;
						if( $current_page < 1 ) {
							$current_page = 1;
						}

						$listing_types = new \WP_Query([
							'post_status' => 'publish',
							'post_type' => 'rz_listing_type',
							'posts_per_page' => -1,
						]);

						$args = [
							'post_status' => 'publish',
							'post_type' => 'rz_listing',
							'author' => $author->ID,
							'posts_per_page' => $listings_per_page,
							'offset' => ( $current_page - 1 ) * $listings_per_page,
						];

						// set listing type by query string
						if( isset( $_GET['type'] ) ) {
							$listing_type = new \Routiz\Inc\Src\Listing_Type\Listing_Type( $_GET['type'] );
							if( $listing_type->id ) {
								$args['meta_query'] = [
									[
										'key' => 'rz_listing_type',
										'value' => $listing_type->id,
										'compare' => '=',
									]
								];
							}
						}

						// set listing type by first
						if( ! isset( $args['meta_query'] ) ) {
							if( $listing_types->found_posts > 0 ) {
								$args['meta_query'] = [
									[
										'key' => 'rz_listing_type',
										'value' => $listing_types->posts[0]->ID,
										'compare' => '=',
									]
								];
							}
						}

						$listings = new \WP_Query( $args );

						$author_posts = $wpdb->get_results(
							$wpdb->prepare("
									SELECT ID
									FROM $wpdb->posts
									WHERE post_status = 'publish'
									AND post_type = 'rz_listing'
									AND post_author = %d
								",
								$author->ID
							)
						);

						$comment_rating = '-';
						$rating_count = 0;

						$author_post_ids = [];
						foreach( $author_posts as $author_post ) {
							$author_post_ids[] = $author_post->ID;
						}

						if( $author_post_ids ) {

							$comments = $wpdb->get_row("
								SELECT *, m.meta_value as review_rating_average, SUM( m.meta_value ) as rating_sum, SUM( CASE WHEN m.meta_value THEN 1 ELSE 0 END ) as rating_count
								FROM {$wpdb->comments} c
									LEFT JOIN {$wpdb->prefix}commentmeta m ON m.comment_id = c.comment_ID AND m.meta_key = 'rz_rating_average'
								WHERE comment_post_ID IN ( " . implode( ',', $author_post_ids ) . " )
								AND comment_type = 'rz-review'
								AND comment_approved = 1
								AND comment_parent = 0
							");

							$rating_count = $comments->rating_count;

							if( $comments->rating_sum and $comments->rating_count ) {
								$comment_rating = number_format( $comments->rating_sum / $comments->rating_count, 2 );
							}

						}

                    $args2 = array(
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

                    $is_pro = count(get_posts( $args2 )) > 0;

                    if($is_pro) {
                        $societe = get_field('societe', 'user_' . $author->ID);
                        $logo = get_field('logo', 'user_'.$author->ID);
                    }

					?>

		                <div class="brk-author-cover">
		                    <div class="brk--cover<?php if( $user_cover ) { echo ' brk--has-cover'; } if( $user_avatar ) { echo ' brk--has-avatar'; } ?>" style="<?php if( $user_cover ) { echo sprintf( 'background-image: url(\'%s\');', $user_cover ); } ?>">
		                        <div class="brk--inner">
		                            <div class="brk--cover-avatar">
		                                <?php if( $is_pro && !empty($logo)): ?>
                                            <img src="<?php echo $logo; ?>" alt="logo">
                                        <?php else: ?>
                                            <?php if( $user_avatar ): ?>
                                                <img src="<?php echo esc_url( $user_avatar ); ?>">
                                            <?php else: ?>
                                                <i class="brk--avatar fas fa-user-alt"></i>
                                            <?php endif; ?>
                                        <?php endif; ?>
		                            </div>
		                            <h4 class="brk--name">
										<?php if( get_user_meta( $author->ID, 'rz_verified', true ) || $is_pro): ?>
                                            <i class="fas fa-briefcase rz--verified"></i>
								        <?php endif; ?>

                                        <?php if( $is_pro && !empty($societe) ): ?>
                                            <?php echo esc_html( $societe ); ?>
                                        <?php else: ?>
                                            <?php echo esc_html( $author->display_name ); ?>
                                        <?php endif; ?>
									</h4>
									<?php
									if ($is_pro) {
										$show_coordinates = get_field('show_coordinates', 'user_'.$author->ID);

										if($show_coordinates == 1) {
											$phone = get_field('telephone', 'user_'.$author->ID);
											$mail_pro = get_field('mail_pro', 'user_'.$author->ID);

											echo '<p><i class="fas fa-phone-alt"></i>&nbsp; '.trim(htmlentities($phone)).'<br><i class="fas fa-envelope"></i>&nbsp; '.trim(htmlentities($mail_pro)).'</p>';
										}
									}
									?>
									<?php
									if ($is_pro) {
										$description = get_field('description_pro', 'user_'.$author->ID);

										if($description) {
											echo '<p class="brk--description">'.trim(htmlentities($description)).'</p>';
										}
									}
									?>
                                    <?php
                                    if ($is_pro) {
                                        $lien_youtube = get_field('lien_youtube', 'user_'.$author->ID);

                                        if($lien_youtube) {
                                            $video_id = explode('v=', $lien_youtube)[1];
                                            echo '<div class="brk--video"><iframe width="560" height="315" src="https://www.youtube.com/embed/'.$video_id.'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>';
                                        }
                                    }
                                    ?>
		                        </div>
		                    </div>
		                </div>

					<div class="brk-grid brk-align-center brk-mt-4 brk-mb-4">
						<div class="brk-col">
							<h4 class="brk-author-title">
								<?php esc_html_e( 'Author\'s listings', 'brikk' ); ?>
							</h4>
						</div>
						<?php if( $listing_types->found_posts > 1 ): ?>
							<div class="brk-col-4 brk-col-md-12">
								<div class="rz-form-group brk-select-listing-type">
									<div class="rz-select rz-select-single">
										<select data-action="author-listing-types">
											<?php foreach( $listing_types->posts as $listing_type ): ?>
                                                <?php $slug = Rz()->get_meta( 'rz_slug', $listing_type->ID ); ?>
                                                <?php $name = Rz()->get_meta( 'rz_name_plural', $listing_type->ID ); ?>
                                                <?php if(in_array($slug, array('achatgrp', 'avis'))) continue; ?>
												<option value="<?php echo esc_html( $slug ); ?>"<?php if( isset( $_GET['type'] ) and $_GET['type'] == $slug ) { echo ' selected="selected"'; } ?>>
													<?php echo esc_html( $name ); ?>
												</option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>
							</div>
						<?php endif; ?>
					</div>

	                <?php if( $listings->have_posts() ): ?>

	                    <ul class="rz-listings" data-cols="3">
	                        <?php while( $listings->have_posts() ): $listings->the_post(); ?>
	                            <li class="rz-listing-item <?php Rz()->listing_class(); ?>">
									<?php Rz()->the_template('routiz/explore/listing/listing'); ?>
								</li>
	                        <?php endwhile; wp_reset_postdata(); ?>
	                    </ul>

	                    <div class="brk-paging">
	                        <?php

	                            echo Rz()->pagination([
	                                'base' => add_query_arg( [ 'onpage' => '%#%' ], get_author_posts_url( $author->ID ) ),
	                                'format' => '?onpage=%#%',
	                                'current' => $current_page,
	                                'total' => $listings->max_num_pages,
	                            ]);

	                        ?>
	                    </div>

	                    <?php

	                        $from = ( $current_page - 1 ) * $listings_per_page + 1;
	                        $to = $from + $listings_per_page - 1;

	                    ?>

	                    <div class="rz-summary rz-text-center">
	                        <p>
	                            <?php echo sprintf(
	                                esc_html__( '%s - %s of %s listings. ', 'brikk' ),
	                                $from,
	                                $to > $listings->found_posts ? $listings->found_posts : $to,
	                                $listings->found_posts
	                            ); ?>
	                        </p>
	                    </div>

	                <?php else: ?>

	                    <p><?php esc_html_e( 'No results were found', 'brikk' ); ?></p>

	                <?php endif; ?>

				<?php else: ?>

					<?php if( have_posts() ): ?>
						<div class="brk-msnry">
							<?php while ( have_posts() ) : the_post(); ?>
								<?php get_template_part( 'templates/article-msnry' ); ?>
							<?php endwhile; ?>
						</div>
						<div class="brk-paging">
							<?php echo Brk()->pagination(); ?>
						</div>
					<?php else: ?>
						<?php get_template_part( 'templates/content-none' ); ?>
					<?php endif; ?>

				<?php endif; ?>

			</div>
		</main>
	</div>
</div>

<?php get_footer();
