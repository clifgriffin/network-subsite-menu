<?php
/*
Plugin Name: Network Subsite Menu
Description:  Show a menu with the network subsites.
Version: 1.0.1
Author: CGD Inc.
Author URI: http://cgd.io

------------------------------------------------------------------------
Copyright 2009-2014 Clif Griffin Development Inc.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

if ( ! class_exists('WordPress_SimpleSettings') )
	require('lib/wordpress-simple-settings.php'); 
	
class CGD_NetworkSubsiteMenu extends WordPress_SimpleSettings {
	var $prefix = '_cgdnm';
	var $network_only = true; // for WordPress Simple Settings
	
	function __construct() {
		parent::__construct();
		
		add_action('network_admin_menu', array($this, 'add_menu') );	
		add_action('pre_wp_nav_menu', array($this, 'show_menu'), 100, 2);
	}
	
	function add_menu() {
		add_submenu_page( 'settings.php', 'Network Menu', 'Network Menu', 'manage_options', 'network-menu', array($this,'show_admin') );
	}
	
	function show_menu($result, $args) {
		global $blog_id;
		
		$menu_settings = apply_filters('cgd_network_subsite_menu_settings', $this->get_setting('site_settings') );
				
		// Only run if we have the right theme location input
		if ( $args->menu !== 'network_subsite_menu' ) return $result;
		
		// If we don't have any subsites, return
		if ( empty($menu_settings['enabled_sites']) ) return $result;
		
		$result = '';
		$result .= '<ul id="network_subsite_menu" class="menu menu-network-subsite">';
		
			$result .= apply_filters('cgd_network_subsite_menu_before', '');
			$current_already_set = false;
			
			foreach($menu_settings['enabled_sites'] as $site_id) {
				$site_info = get_blog_details($site_id, true);
				$site_info->current = false;
				$site_info->blogname = isset($menu_settings['labels'][$site_id]) ? $menu_settings['labels'][$site_id] : $site_info->blogname;
				
				// Set current
				$site_info->current = false;
				if ( $blog_id == $site_id ) {
					$site_info->current = true;					
				}
				
				// Allow for extensibility
				$site_info = apply_filters('cgd_network_subsite_menu_site_info', $site_info, $site_id);
				
				$class = '';
				if( $site_info->current && ! $current_already_set ) {
					$class = 'current-menu-item';
				}
				
				$result .= "<li id='menu-item-$site_id' class='menu-item menu-item-type-network-subsite menu-item-$site_id $class'><a href='{$site_info->siteurl}'>$site_info->blogname</a></li>";
				
				if ( $site_info->current ) {
					$current_already_set = true; // only set one menu item as current
				}
			}
			
			$result .= apply_filters('cgd_network_subsite_menu_after', '');
		
		$result .= '</ul>';
		
		return $result;
	}
	
	function show_admin() {
		$sites = wp_get_sites();
		$menu_settings = $this->get_setting('site_settings');
		?>
		
		<div class="wrap">
			<h2>Network Menu Settings</h2>
			<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
				<?php $this->the_nonce(); ?>
				
				<table class="form-table">				
					<tbody>
						<?php foreach($sites as $s): ?>
							<?php $s = get_blog_details($s['blog_id']); ?>
							<tr>
								<th scope="row" valign="top">
									<label><?php echo $s->path; ?></label>
								</th>
								<td>
									<p>
										<label>
											<input type="checkbox" name="<?php echo $this->get_field_name('site_settings'); ?>[enabled_sites][]" value="<?php echo $s->blog_id; ?>" <?php if ( in_array($s->blog_id, $menu_settings['enabled_sites']) ) echo 'checked="checked"'; ?> />	Show in menu.
										</label>
									</p>
									
									<p>
										<label>
											<input type="text" name="<?php echo $this->get_field_name('site_settings'); ?>[labels][<?php echo $s->blog_id; ?>]; ?>" value="<?php if ( isset($menu_settings['labels'][$s->blog_id]) ) echo $menu_settings['labels'][$s->blog_id]; ?>" /><br/>
											Menu label for <?php echo $s->blogname; ?> entry.
										</label>
									</p>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}

$CGD_NetworkSubsiteMenu = new CGD_NetworkSubsiteMenu();