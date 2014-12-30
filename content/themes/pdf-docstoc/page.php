<?php get_header(); ?>
<div class="container_12">
    <div class="grid_12">
        <header>
            <h3 style="margin-top: 50px"><?php echo title(); ?></h3>
        </header>
        <section>
            <?php echo results('content'); ?>
        </section>
    </div>
</div>
<?php get_footer(); ?>