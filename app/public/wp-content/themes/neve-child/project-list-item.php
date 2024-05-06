<?php  // Retrieve ZIP code ACF field for the current post
        $zip = !empty(get_field('zip_code')) ? get_field('zip_code') : 'No Zip';
?>

<li><?php the_title(); ?> <?php the_date()?> - <?php echo $zip;?>  </li>