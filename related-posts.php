<?php
/*
 * Plugin Name:       Related-Posts
 * Plugin URI:        https://wordpress.org/plugins/related-posts/
 * Description:       Enhance user engagement on your WordPress site by displaying Related-Posts beneath your content."Related-Posts for WordPress" intelligently suggests posts based on shared categories and tags, encouraging visitors to explore more of your content. The plugin is fully customizable, allowing you to adjust the number of related posts displayed and style the layout to fit your theme. Improve your site's SEO and increase page views with minimal setup.
 * Version:           1.0.0
 * Requires at least: 6.5
 * Requires PHP:      7.2
 * Author:            Bappi
 * Author URI:        https://bappi.cs/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       related-posts
 * Domain Path:       /languages
 */

/*
Related-Posts is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Related-Posts but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Related-Posts. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

defined('ABSPATH') || exit;
//include class.
require_once __DIR__ . '/includes/class-related-posts.php';

/**
 * Initializing plugin.
 *
 * @return Object Plugin object.
 * @since 1.0.0
 */
function related_posts()
{
    return new Related_Posts(__FILE__);
}

related_posts();

