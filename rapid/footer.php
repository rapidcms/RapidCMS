		</section>
		
		<aside id="admin-sidebar" class="g320">
			<?php 
			global $hooks;
			
			$hooks->add_action('admin_sidebar');
			?>
		</aside>

		<footer>
			<?php 
			global $hooks;
		
			$hooks->add_action('admin_footer'); 
			?>
			<p>Powered by: <a href="http://rapidcms.org">RapidCMS</a></p>
		</footer>
	<div>
</body>
</html>