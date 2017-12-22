<?php
get_header();

$options = get_option('utsic_options');

if (!isset($options['utsic_homepage_feed'])) $options['utsic_homepage_feed'] = 0;
$query_string = "cat=" . $options['utsic_homepage_feed'];
query_posts ( $query_string );
if (have_posts()) the_post();


?>


<div id="fullpage-wrapper">
    <div id="intro-paragraph">
	<?php echo apply_filters('the_content', $options['utsic_intro_blurb']) ?>
    </div>
    <div id="feature-boxes">
	<div id="feature-box-1" class="frontpage-feature-box">
	    <?php fill_feature_box(1); ?>
	</div>
	<div id="feature-box-2" class="frontpage-feature-box">
	    <?php fill_feature_box(2); ?>
	</div>
	<div id="feature-box-3" class="frontpage-feature-box">
	    <?php fill_feature_box(3); ?>
	</div>
    </div>
    <div id="bottom-matter">
	<div id="frontpage-search">
	    <form role="search" method="get" id="searchform" action="<?php echo site_url(); ?>" >
                    <div>
                        <label class="screen-reader-text" for="s">Search for:</label>
                        <input type="text" placeholder="search the collection..." name="s" id="s" />
			<input type="hidden" name="within_collection" value="entire_collection">
                        <input type="submit" id="searchsubmit" value="Search" />
                    </div>
                </form>
	</div>
	<div id="donate-box">
	    <?php
	    $durl = $options['utsic_donate_url'];
	    if ( $durl != '' ) {
		?>
		<a href="<?php echo $options['utsic_donate_url']; ?>">Donate to UTSIC</a>
		<?php
	    }
	    ?>
	</div>
	<!--<div id="frontpage-feed">
	    <h4><?php echo get_category($options['utsic_homepage_feed'])->name;?></h4>
	    <?php the_excerpt(); ?>
	    <p id="feed-date"><?php the_date(); ?></p>
	</div>-->
    </div>
</div><!-- #content --> 
		

<?php get_footer(); ?>