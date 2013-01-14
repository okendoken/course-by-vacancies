<?php
/*
	Template Name: Jobs
*/
?>
<?php get_header(); ?>

<div id="content">
    <div class="taxbar">
        <?php
        $is_jobs_list_page = is_page() and get_post_meta(get_the_ID(), '_vc_page_template', true) == 1;
        $real_query_args = array(
            'post_type' => 'job',
            'paged' => $paged
        );

        if (get_query_var('job_type')){
            $real_query_args['job_type'] = get_query_var('job_type');
        }
        $temp = $wp_query;
        ?>

        <ul>
            <?php
            $cat_html = wp_list_categories(array(
                'taxonomy'     => 'job_type',
                'orderby'      => 'name',
                'show_count'   => 0,
                'pad_counts'   => 0,
                'hierarchical' => 1,
                'title_li'     => '',
                'echo' => 0
            ));

            if ($is_jobs_list_page){
                $cat_html = "<li class='current-cat'><a href='".get_permalink()."'>All</a></li>".$cat_html;
            } else {
                //searching for job listing page
                $query_args = array(
                    'posts_per_page' => -1,
                    'meta_key' => '_vc_page_template',
                    'post_type' => 'page',
                    'post_status' => 'publish'
                );
                query_posts($query_args);
                if (have_posts()){
                    while (have_posts()){
                        the_post();
                        $page_template_meta = get_post_meta(get_the_ID(), '_vc_page_template', true);
                        if ($page_template_meta == 1){
                            $cat_html = "<li><a href='".get_permalink()."'>All</a></li>".$cat_html;
                            break;
                        }
                    }
                }
            }
            echo $cat_html;
            ?>
        </ul>
    </div>

    <?php
    $count = 0;
    query_posts($real_query_args);
    ?>
    <?php while (have_posts()) : the_post(); ?>

    <div class="box clearfix <?php if (++$count % 2 == 0) { echo "altbox"; } ?>" id="post-<?php the_ID(); ?>">

        <div class="btitle">
            <h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php echo short_title('...', 7);  ?></a></h2>
            <p><?php $comdes=get_post_meta($post->ID, 'wtf_comdescript', true); echo $comdes; ?></p>

            <?php  if( has_term( 'full-time', 'job_type', $post->ID ) ) { ?>
            <span class="violspan typemet">Full time</span>
            <?php } else if ( has_term( 'contract', 'job_type', $post->ID ) ){ ?>
            <span class="greenspan typemet">Contract</span>
            <?php } else if ( has_term( 'part-time', 'job_type', $post->ID ) ){ ?>
            <span class="redspan typemet">Part time</span>
            <?php } else if ( has_term( 'internship', 'job_type', $post->ID ) ){ ?>
            <span class="orspan typemet">Internship</span>
            <?php } else if ( has_term( 'freelance', 'job_type', $post->ID ) ){ ?>
            <span class="bluspan typemet">Freelance</span>
            <?php } ?>
        </div>
        <div class="jlocation"> <?php $loc=get_post_meta($post->ID, 'wtf_comlocate', true); echo $loc; ?></div>
        <div class="jpostime"><?php the_time('M  j'); ?></div>

    </div>

    <?php endwhile; ?>
    <div class="clear"></div>
    <?php getpagenavi(); ?>
    <?php
    $wp_query = null;
    $wp_query = $temp;
    ?>
</div>
<?php get_sidebar(); ?>

<?php get_footer(); ?>