<?php get_template_part('templates/head'); ?>
<body <?php body_class(); ?> data-spy="scroll" data-target=".navbar-docs" data-offset="50">

  <!--[if lt IE 8]>
    <div class="alert alert-warning">
      <?php _e('You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.', 'pressapps'); ?>
    </div>
  <![endif]-->

  <?php
    do_action('get_header');
    get_template_part('templates/header');
  ?>

  <div class="wrap container" role="document">
    <main role="main">
      <?php include pa_template_path(); ?>
    </main><!-- /.main -->
    <?php get_template_part('templates/footer'); ?>
  </div><!-- /.wrap -->

  <?php wp_footer(); ?>

</body>
</html>
