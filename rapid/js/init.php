<?php 
	// We'll use this is enqueue our scripts, saves on load time
	function enqueue_script($path, $type="fast")
	{
		if ($type == "fast") 
		{
			echo"<script type=\"text/javascript\" id=\"$path\">";
			include($path);
			echo "</script>";
		}
		else
		{
			$root_url = dirname($_SERVER["SCRIPT_NAME"]); 
			echo "<script type=\"text/javascript\" id=\"$path\" src=\"" . RAPID_DIR . "/js/" . $path . "\"></script>";
		}
	}

	enqueue_script("jquery-1.4.4.min.js");
	enqueue_script("nicEdit.min.js");
?>

<script type="text/javascript" id="init.php">
	/*global window document $ nicEditor*/
	var edit_mode = false;
	$(document).ready(function () {
		<?php
		if ($_SESSION['uid'] <> 0) 
		{
		?>
		// Show Edit button
		var edit_button = "<div id='rapid_edit'><a href='#'><h1 id='rapid_caption'>E D I T</h1></a></div>";
		$('body').prepend(edit_button);
		
		$('#rapid_edit').css({
			'float': 'left',
			'border': '1px solid #000',
			'border-left': '0px',
			'background' : '#F63',
			'position' : 'fixed',
			'opacity' : '0.5',
			'width' : '35px',
			'text-align' : 'center'
		});
		
		$('#rapid_edit').hover(function () {
			$(this).css({'opacity': '0.75'});
		}, function () {
			$(this).css({'opacity': '0.4'});
		});
		
		$('#rapid_edit a').css({
			'color': '#fff',
			'text-decoration': 'none'
		});
		
		$('#rapid_caption').css({
			'color': '#fff',
			'font-style': 'normal',
			'padding-top': '6px',
			'font-family': 'impact, arial bold, arial',
			'font-size': '42px',
			'line-height': '38px',
			'margin': '0 0 6px 0'
		});
		
		// Position the edit button
		if ($.browser.msie) {
			$('#rapid_edit').css({'position': 'absolute'});
			
			$(window).resize(function () {
				$('#rapid_edit').css({'top': (($(window).height() - $('#rapid_edit').height()) / 2) + $(window).scrollTop()});
			});
			
			$(window).scroll(function () {
				$(window).trigger("resize");
			});
			
			$(window).trigger("resize");
		} else {
			$(window).resize(function () {
				var edit_position = (window.innerHeight - $('#rapid_edit').height()) / 2;
				$('#rapid_edit').css({'top': edit_position});
			});
			$(window).trigger("resize");
		}
		
		// this toggles edit mode on and off
		$('#rapid_edit').toggle(function () {
			edit_mode = true;
			$('#rapid_caption').text("S T O P");
		}, function () {
			edit_mode = false;
			$('#rapid_caption').text("E D I T");
		});
		
		// if we're in edit mode then, disable all the links.
		$('a').click(function (e) {
			if (edit_mode)
			{
				e.preventDefault();
			}
		});
		
		$('.block').each(function () {
			
			// if we are in edit mode, let us know when we're over a block
			$(this).hover(function () {
				if (edit_mode) {
					$(this).css({'opacity': '0.5'});
				}
			},
			function () {
				if (edit_mode) {
					$(this).css({'opacity': '1'});
				}
			});
			
			var editor;
			$(this).click(function () {
				var position = $(this).position;
				if (!editor && edit_mode) {
					
					var oldContent = $(this).html();
					var id = $(this).attr("id");
					var name = String(id).substring(6);
					
					editor = new nicEditor({
						buttonList : ['bold', 'italic', 'underline', 'left', 'center', 'right', 'ol', 'ul', 'fontFormat', 'superscript', 'link', 'image', 'xhtml'],
						iconsPath : '<?php echo RAPID_DIR; ?>/js/nicEditorIcons.gif'
					}).panelInstance(id, {
						hasPanel : true
					});
					
					editor.addEvent('blur', function () {
						if (editor) {
							var content = $('.nicEdit-main').html();
							//DEBUG: alert(content);
							$('body').ajaxError(function () {
								alert("There has been an error. Your content was not saved.");
							});
							$.ajax({
								url: "<?php echo RAPID_DIR; ?>/ajax.php",
								type: "POST",
								data: ({
									content: content,
									id: id,
									name: name
								}),	
								success: function (msg) {
									// we replace the block with what we get from ajax.php
									$('#' + id).html(msg);
									$('#' + id).fadeOut(125).fadeIn(125).fadeOut(125).fadeIn(125);
								}
							});
							editor.removeInstance(id);
							editor = null;
							// we have to add the event listener again, seems to be a bug.
							$('a').click(function (e) {
								if (edit_mode)
								{
									e.preventDefault();
								}
							});
						}
					});
					
					editor.addEvent('key', function (nicedit, e) {
						if (e.keyCode === 27) {
							// set the content to the oldContent
							nicedit.setContent(oldContent);
							// remove the instance
							editor.removeInstance(id);
							editor = null;
						}
					});

					editor.instanceById(id).elm.focus();
					$('.nicEdit-main').focus();
					
					// if it is not ie, then lets make sure we don't use spans....
					if (!$.browser.msie) {
						document.execCommand("styleWithCSS", 0, false);
						// ff doesn't recognize <strong> or <em> tag, lets replace them
						var content = $('.nicEdit-main').html();
						content = content.replace(/strong>/gi, "b>");
						content = content.replace(/em>/gi, "i>");
						$('.nicEdit-main').html(content);
					}
				}
				
			});
		});
		<?php
		} // check if you're logged in
		?>
	});
</script>