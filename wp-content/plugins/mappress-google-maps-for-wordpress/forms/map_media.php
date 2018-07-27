<div class='mapp-media'>
	<?php echo Mappress::get_support_links(); ?>
	<div class='mapp-media-list-panel'>
		<b><?php _e('Maps for This Post', 'mappress-google-maps-for-wordpress')?></b>
		<input data-mapp-media='add' class='button' type='button' value='<?php esc_attr_e('New Map', 'mappress-google-maps-for-wordpress')?>' />
		<div class='mapp-media-list'>
			<?php echo Mappress_Map::get_map_list(); ?>
		</div>
	</div>

	<div class='mapp-media-edit-panel'>
		<table class='mapp-settings'>
			<tr>
				<td><?php _e('Map ID', 'mappress-google-maps-for-wordpress');?>:</td>
				<td><span class='mapp-media-mapid'></span></td>
			</tr>

			<tr>
				<td><?php _e('Map Title', 'mappress-google-maps-for-wordpress');?>:</td>
				<td><input class='mapp-media-title' type='text' size='40' placeholder='<?php _e('Untitled', 'mappress-google-maps-for-wordpress');?>' /></td>
			</tr>

			<tr>
				<td><?php _e('Display Size', 'mappress-google-maps-for-wordpress');?>:</td>
				<td>
					<?php
						$sizes = array();
						foreach(Mappress::$options->sizes as $i => $size)
							$sizes[] = "<a href='#' class='mapp-media-size' data-width='{$size['width']}' data-height='{$size['height']}'>" . $size['width'] . 'x' . $size['height'] . "</a>";
						echo implode(' | ', $sizes);
					?>
					<input type='text' class='mapp-media-width' size='2' value='' /> x <input type='text' class='mapp-media-height' size='2' value='' />
				</td>
			</tr>

			<tr>
				<td><?php _e('Save center / zoom', 'mappress-google-maps-for-wordpress');?></td>
				<td><input type='checkbox' class='mapp-media-viewport'></td>
			</tr>
		</table>
		<div class='mapp-media-toolbar'>
			<input data-mapp-media='save' class='button button-primary' type='button' value='<?php esc_attr_e('Save', 'mappress-google-maps-for-wordpress'); ?>' />
			<input data-mapp-media='cancel' class='button' type='button' value='<?php esc_attr_e('Cancel', 'mappress-google-maps-for-wordpress'); ?>' />
			<input data-mapp-media='insert' class='button' type='button' value='<?php esc_attr_e('Insert into post', 'mappress-google-maps-for-wordpress'); ?>' />
		</div>
		<?php require Mappress::$basedir . "/forms/map_editor.php"; ?>
	</div>
</div>