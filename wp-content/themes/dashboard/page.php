<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */

get_header(); ?>

	<div class="container-fluid left-menu">
	
<div class="span2 col main-left hidden-phone menu-large" style="width: 14.893617021276595%;">
						<div class="rrow scroll-y-left" style="overflow: hidden;">
							<div class="iScrollWrapper" style="width: auto; -webkit-transition: -webkit-transform 0ms; -webkit-transform-origin: 0px 0px; -webkit-transform: translate(0px, 0px) translateZ(0px);">
								<ul style="display: block;">
									<li class="glyphicons home currentScroll active"><a href="index.html?lang=en"><i></i><span>Dashboard</span></a></li>
									<li class="glyphicons coins"><a href="finances.html?lang=en"><i></i><span>Finances</span></a></li>
									<li class="hasSubmenu2">
										<a data-toggle="collapse" class="glyphicons shopping_cart" href="#menu_ecommerce"><i></i><span>Online Shop</span></a>
										<ul class="collapse" id="menu_ecommerce">
											<li class=""><a href="products.html?lang=en" class="glyphicons show_thumbnails"><i></i><span>Products</span></a></li>
											<!-- <li class=""><a href="categories.html?lang=en" class="glyphicons show_big_thumbnails"><i></i><span>Categories</span></a></li> -->
											<li class=""><a href="product_edit.html?lang=en" class="glyphicons cart_in"><i></i><span>Add product</span></a></li>
											<!-- <li class=""><a href="orders.html?lang=en" class="glyphicons list"><i></i><span>Orders</span></a></li> -->
										</ul>
									</li>
									<li class="glyphicons sort"><a href="pages.html?lang=en"><i></i><span>Site Pages</span></a></li>
									<li class="glyphicons picture"><a href="gallery.html?lang=en"><i></i><span>Photo Gallery</span></a></li>
									<li class="glyphicons adress_book"><a href="bookings.html?lang=en"><i></i><span>Bookings</span></a></li>
									<li class="glyphicons charts"><a href="charts.html?lang=en"><i></i><span>Charts</span></a></li>
									<li class="glyphicons cogwheels"><a href="ui.html?lang=en"><i></i><span>UI Elements</span></a></li>
									<li class="hasSubmenu2">
										<a data-toggle="collapse" class="glyphicons show_thumbnails_with_lines" href="#menu_forms"><i></i><span>Forms</span></a>
										<ul class="collapse" id="menu_forms">
											<li class=""><a href="form_demo.html?lang=en" class="glyphicons user"><i></i><span>My Account</span></a></li>
											<li class=""><a href="form_elements.html?lang=en" class="glyphicons show_big_thumbnails"><i></i><span>Form Elements</span></a></li>
											<li class=""><a href="form_validator.html?lang=en" class="glyphicons circle_ok"><i></i><span>Form Validator</span></a></li>
											<!-- <li class=""><a href="form_wizzard.html?lang=en" class="glyphicons share_alt"><i></i><span>Form Wizzard</span></a></li> -->
											<li class=""><a href="file_managers.html?lang=en" class="glyphicons file_import"><i></i><span>File Managers</span></a></li>
										</ul>
									</li>
									<li class="hasSubmenu2">
										<a data-toggle="collapse" class="glyphicons table" href="#menu_tables"><i></i><span>Tables</span></a>
										<ul class="collapse" id="menu_tables">
											<li class=""><a href="tables.html?lang=en" class="glyphicons show_thumbnails"><i></i><span>Classic Tables</span></a></li>
											<li class=""><a href="tables_themed.html?lang=en" class="glyphicons show_thumbnails"><i></i><span>Themed Tables</span></a></li>
											<li class=""><a href="tables_enhanced.html?lang=en" class="glyphicons show_thumbnails"><i></i><span>Enhanced Tables</span></a></li>
										</ul>
									</li>
									<li class="glyphicons calendar"><a href="calendar.html?lang=en"><i></i><span>Calendar</span></a></li>
								</ul>
							</div>
							<span class="navarrow hide" style="display: none;">
								<span class="glyphicons circle_arrow_down"><i></i></span>
							</span>
						<div class="iScrollLeftV" style="pointer-events: none; -webkit-transition: opacity 0ms 300ms; overflow: hidden; opacity: 0;"><div style="pointer-events: none; -webkit-transition: -webkit-transform 0ms cubic-bezier(0.33, 0.66, 0.66, 1); -webkit-transform: translate(0px, 0px) translateZ(0px); height: 724px;"></div></div></div>
					</div>
		<div id="content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'content', 'page' ); ?>
				<?php comments_template( '', true ); ?>
			<?php endwhile; // end of the loop. ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>