
<?php //display available categories part1 ?>
			
 <?php $terms = get_terms(array(    
  // 'post_type' =>'roasters',
	'taxonomy' => 'zipcode_location',
    'hide_empty' => false,
));
 ?>
	<ul class="cat-list" style="   display: block;    padding: 20px;    margin: 20px;">
	  <li><a class="cat-list_item active" href="#!" data-slug="">All projects</a></li>

	  <?php foreach($terms as $term) : ?>
		<li>
		  <a class="cat-list_item" href="#!" data-slug="<?php echo $term->slug; ?>"><?php echo $term->name; ?></a>
		</li>
	  <?php endforeach; ?>
	</ul>

<label for="cars">Choose a zip:</label>

<select name=" " id="zipID">
  <option value="" data-slug="">All Projects</option>
 
  <?php foreach($terms as $term) : ?>
		 <option value="" data-slug="<?php echo $term->slug; ?> "> <?php echo $term->name; ?></option>
		  <a class="cat-list_item" href="#!" data-slug="<?php echo $term->slug; ?>"><?php echo $term->name; ?></a>
		
	  <?php endforeach; ?>
</select>




<?php 
  $projects = new WP_Query([
    'post_type' => 'roasters',
    'posts_per_page' => -1,
    'order_by' => 'date',
    'order' => 'desc',
  ]);
?>

<?php if($projects->have_posts()): ?>
  <ul class="project-tiles">
    <?php
      while($projects->have_posts()) : $projects->the_post();
	  

        include('project-list-item.php');
	  
      endwhile;
    ?>
  </ul>
  <?php wp_reset_postdata(); ?>
<?php endif; ?>




