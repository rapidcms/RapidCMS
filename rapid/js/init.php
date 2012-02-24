<?php 
	// We'll use this is enqueue our scripts, saves on load time
	function enqueue_script($path, $type="fast") {
		if ($type == "fast") {
			echo"<script id=\"$path\">";
			include($path);
			echo "</script>\n";
		} else {
			$root_url = dirname($_SERVER["SCRIPT_NAME"]); 
			echo "<script src='" . RAPID_DIR . "/js/" . $path . "' id='$path'></script>\n";
		}
	}

	enqueue_script("jquery-1.7.1.min.js", "slow");
	enqueue_script("nicEdit.min.js", "slow");
	enqueue_script("nicEdit.extend.js", "slow");
?>

<?php if ($_SESSION['rapid_uid'] <> 0 && $_SESSION['rapid_uuid'] == RAPID_UUID) { ?>
<script type="text/javascript" id="init.php">
	/*global window document $ nicEditor*/
	
	$(document).ready(function () {
		var editor;
		var editing = false;
		var backup_content;
		
		$('body').css({
			'height': $(document).height()
		});
		
		editor = new nicEditor({
			buttonList: ['bold', 'italic', 'underline', 'left', 'center', 'right', 'ol', 'ul', 'fontFormat', 'superscript', 'link', 'image', 'xhtml'],
			iconsPath: '<?php echo RAPID_DIR;?>/js/nicEditorIcons.gif',
			xhtml: true
		});
		var edit_img = "<?php echo RAPID_DIR; ?>/images/edit_mode.png";
		var edit_button = "<a id='rapid_edit'href='#'></a>";
		$('body').prepend(edit_button);
		
		$('#rapid_edit').css({
			'display': 'block',
			'position' : 'fixed',
			'width': '44px',
			'height': '161px',
			'float': 'left',
			'border': '0',
			'margin': '0',
			'padding': '0',
			'background' : "url('" + edit_img + "')",
			'opacity' : '0.5',
			'text-align' : 'center',
			'text-decoration': 'none'
		});
		/*
		$('#rapid_caption').css({
			'margin': '0',
			'padding': '5px 7px 7px 7px',
			'line-height': '34px'
		});
		*/
		
		$('#rapid_edit').hover(function () {
			$(this).css({'opacity': '0.75'});
		}, function () {
			$(this).css({'opacity': '0.4'});
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
			$('#rapid_edit').css('background-position', '-46px 0px');
			editor.floatingPanel();
			$('.block').each(function () {
				editor.addInstance($(this).attr('id'));
			});
		}, function () {
			edit_mode = false;
			$('#rapid_edit').css('background-position','0 0');
			editor.floating.remove();
			$('.block').each(function () {
				editor.removeInstance($(this).attr('id'));
			});
		});
		
		// toggle on and off to initialize the editors
		$('#rapid_edit').trigger("click").trigger("click");
		
		// if we're in edit mode then, disable all the links.
		$('a').click(function (e) {
			if (edit_mode) {
				// TODO: popup on lower left saying links are not active ...
				e.preventDefault();
			}
		});
		
		$('.block').hover(function () {
			if (edit_mode && !editing) {
				$(this).css({'opacity': '0.5'});
				var position = $(this).offset();
				$(this).focus();
				$(editor.floating).css({
					'width': $(this).outerWidth()
				});
				$(editor.floating).css({
					'z-index': 999999999,
					'top': position.top - $(editor.floating).height(),
					'left': position.left
				});
			}
		}, function () {
			if (edit_mode && !editing) {
				$(this).css({'opacity': '1.0'});
				$(editor.floating).css({'top': '-1000px'});
				$(this).blur();
			}
		});
		
		$('.block').click(function () {
			// if we're not editing already, then
			if (!editing) {
				// make sure we are in edit_mode
				if (edit_mode) {
					// we're editing this id now
					editing = $(this).attr('id');
					var name = String(editing).substring(6);
					var that = this;
					$.ajax({
						url: "<?php echo RAPID_DIR; ?>/ajax.php",
						type: "POST",
						data: {
							action: 'load',
							name: name
						},
						success: function (msg) {
							editor.instanceById(editing).setContent(msg);
							backup_content = msg;
							$(that).css({'opacity': '1.0'});
						}
					});
				}
			} else {
				if ($(this).attr('id') !== editing) {
					$(this).blur();
				}
			}
		});
		
		$('.block').blur(function (e) {
			if (editing) {
				var is_pane = $('html').find('.nicEdit-pane').hasClass('nicEdit-pane');
				if (edit_mode) {
					if (!is_pane) {
						var name = String(editing).substring(6);
						var content = editor.instanceById(editing).getContent();
						

						$('body').ajaxError(function () {
							alert("There has been an error. Your content was not saved.");
						});

						$.ajax({
							url: "<?php echo RAPID_DIR; ?>/ajax.php",
							type: "POST",
							data: ({
								action: 'update',
								content: content,
								name: name
							}),	
							success: function (msg) {
								// we replace the block with what we get from ajax.php
								$('#' + editing).html(msg);
								$('#' + editing).fadeOut(125).fadeIn(125).fadeOut(125).fadeIn(125);
								editing = false;
							}
						});
					}
				}
			}
		});
		
		$('.nicEdit-pane :submit').live("click", function() {
			editor.selectedInstance.elm.focus();
		});
		
		editor.addEvent('key', function (instance, e) {
			if (e.keyCode === 27) {
				instance.setContent(backup_content);
				instance.blur();
				$('body').trigger('focus');
			}
		});
	});
</script>
<?php } ?>