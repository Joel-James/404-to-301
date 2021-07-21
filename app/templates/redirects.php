<?php
/**
 * Admin settings page base template.
 *
 * @var array  $tabs    Tabs list.
 * @var string $current Current tab.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    View
 *
 * @subpackage Pages
 */

?>

<div class="wrap tsf-metaboxes">
	<form method="post" action="options.php" autocomplete="off">
		<input type="hidden" id="closedpostboxesnonce" name="closedpostboxesnonce" value="2e9c3e5f68">
		<input type="hidden" id="meta-box-order-nonce" name="meta-box-order-nonce" value="b552bb3b2c">
		<input type="hidden" name="option_page" value="autodescription-site-settings">
		<input type="hidden" name="action" value="update">
		<input type="hidden" id="_wpnonce" name="_wpnonce" value="e1be12fbbc">
		<input type="hidden" name="_wp_http_referer" value="/incsub/single/wp-admin/admin.php?page=theseoframework-settings">
		<div class="tsf-top-wrap">
			<h1 class="font-bold mb-5">
				<?php esc_html_e( '404 to 301', '404-to-301' ); ?>
				<span class="subtitle pl-4">
					by <a href="https://duckdev.com/?utm_source=dd404&utm_medium=plugin&utm_campaign=dd404_settings_header">Joel James</a> ( v<?php echo esc_attr( DD4T3_VERSION ); ?> )
				</span>
			</h1>
			<p class="tsf-top-buttons">
				<input type="submit" name="submit" class="button button-primary" value="Save Settings">
				<input type="submit" name="reset" class="button" value="Reset Settings" onclick="return confirm(`Are you sure you want to reset all SEO settings to their defaults?`)">
			</p>
		</div>

		<hr class="wp-header-end">

		<div class="tsf-notice-wrap">
			<div class="notice updated tsf-notice tsf-show-icon is-dismissible">
				<p><?php esc_html_e( 'Settings updated!', '404-to-301' ); ?></p>
			</div>
		</div>

		<div class="metabox-holder columns-2">
			<div class="postbox-container-1">
				<div id="main-sortables" class="meta-box-sortables ui-sortable">
					<div id="autodescription-general-settings" class="postbox " style="">
						<div class="postbox-header">
							<h2 class="hndle ui-sortable-handle">Settings</h2>
						</div>
						<div class="inside">
							<div class="tsf-nav-tab-wrapper hide-if-no-tsf-js" id="general-tabs-wrapper">
								<div class="tsf-tab tsf-active-tab"><label for="tsf-general-tab-layout" class="tsf-nav-tab"><span class="dashicons dashicons-randomize tsf-dashicons-tabs"></span><span class="tsf-nav-desktop">Redirect</span></label></div>
								<div class="tsf-tab"><input type="radio" class="tsf-tabs-radio tsf-input-not-saved" id="tsf-general-tab-performance" name="tsf-general-tabs"><label for="tsf-general-tab-performance" class="tsf-nav-tab"><span class="dashicons dashicons-media-default tsf-dashicons-tabs"></span><span class="tsf-nav-desktop">Logs</span></label></div>
								<div class="tsf-tab"><input type="radio" class="tsf-tabs-radio tsf-input-not-saved" id="tsf-general-tab-canonical" name="tsf-general-tabs"><label for="tsf-general-tab-canonical" class="tsf-nav-tab"><span class="dashicons dashicons-email-alt tsf-dashicons-tabs"></span><span class="tsf-nav-desktop">Email</span></label></div>
								<div class="tsf-tab"><input type="radio" class="tsf-tabs-radio tsf-input-not-saved" id="tsf-general-tab-timestamps" name="tsf-general-tabs"><label for="tsf-general-tab-timestamps" class="tsf-nav-tab"><span class="dashicons dashicons-admin-settings tsf-dashicons-tabs"></span><span class="tsf-nav-desktop">General</span></label></div>
								<div class="tsf-tab"><input type="radio" class="tsf-tabs-radio tsf-input-not-saved" id="tsf-general-tab-exclusions" name="tsf-general-tabs"><label for="tsf-general-tab-exclusions" class="tsf-nav-tab"><span class="dashicons dashicons-editor-unlink tsf-dashicons-tabs"></span><span class="tsf-nav-desktop">Exclusions</span></label></div>
							</div>
							<div class="tsf-tabs-content tsf-general-tabs-content tsf-active-tab-content" id="tsf-general-tab-layout-content">
								<h4>Administrative Layout Settings</h4>
								<p><span class="description">SEO hints can be visually displayed throughout the dashboard.</span></p>
								<hr>
								<h4>SEO Bar Settings</h4>
								<div class="tsf-fields">
									<span class="tsf-toblock"><label for="autodescription-site-settings[display_seo_bar_tables]"><input type="checkbox" class="tsf-default-selected" name="autodescription-site-settings[display_seo_bar_tables]" id="autodescription-site-settings[display_seo_bar_tables]" value="1" checked="checked"> Display the SEO Bar in overview tables?</label></span>
									<span class="tsf-toblock"><label for="autodescription-site-settings[display_seo_bar_metabox]"><input type="checkbox" class="" name="autodescription-site-settings[display_seo_bar_metabox]" id="autodescription-site-settings[display_seo_bar_metabox]" value="1"> Display the SEO Bar in the SEO Settings metabox?</label></span>
									<span class="tsf-toblock">
										<label for="autodescription-site-settings[seo_bar_symbols]">
											<input type="checkbox" class="" name="autodescription-site-settings[seo_bar_symbols]" id="autodescription-site-settings[seo_bar_symbols]" value="1"> Use symbols for warnings? <span class="tsf-tooltip-wrap">
												<span class="tsf-tooltip-item tsf-help" title="If you have difficulty discerning colors, this may help you spot issues more easily." data-desc="If you have difficulty discerning colors, this may help you spot issues more easily." tabindex="0">[?]</span>
											</span>
										</label>
									</span>
								</div>
								<hr>
								<h4>Counter Settings</h4>
								<div class="tsf-fields"><span class="tsf-toblock"><label for="autodescription-site-settings[display_pixel_counter]"><input type="checkbox" class="tsf-default-selected" name="autodescription-site-settings[display_pixel_counter]" id="autodescription-site-settings[display_pixel_counter]" value="1" checked="checked"> Display pixel counters? <span class="tsf-tooltip-wrap"><a href="https://kb.theseoframework.com/?p=48" class="tsf-tooltip-item tsf-help" target="_blank"
								                                                                                                                                                                                                                                                                                                                                                                                     rel="nofollow noreferrer noopener"
								                                                                                                                                                                                                                                                                                                                                                                                     title="The pixel counter computes whether the input will fit on search engine result pages."
								                                                                                                                                                                                                                                                                                                                                                                                     data-desc="The pixel counter computes whether the input will fit on search engine result pages.">[?]</a></span></label></span>
									<span class="tsf-toblock"><label for="autodescription-site-settings[display_character_counter]"><input type="checkbox" class="tsf-default-selected" name="autodescription-site-settings[display_character_counter]" id="autodescription-site-settings[display_character_counter]" value="1" checked="checked"> Display character counters? <span class="tsf-tooltip-wrap"><span class="tsf-tooltip-item tsf-help" title="The character counter is based on guidelines."
									                                                                                                                                                                                                                                                                                                                                                                                data-desc="The character counter is based on guidelines." tabindex="0">[?]</span></span></label></span>
								</div>
							</div>
							<div class="tsf-tabs-content tsf-general-tabs-content" id="tsf-general-tab-performance-content">
								<div class="hide-if-tsf-js tsf-content-no-js">
									<div class="tsf-tab tsf-tab-no-js">
					<span class="tsf-nav-tab tsf-active-tab">
						<span class="dashicons dashicons-performance tsf-dashicons-tabs"></span>						<span>Performance</span>					</span>
									</div>
								</div>
								<h4>Performance Settings</h4>
								<p><span class="description">Depending on your server's configuration, adjusting these settings can affect performance.</span></p>
								<hr>
								<h4>Query Alteration Settings</h4>
								<p><span class="description">Altering the query allows for more control of the site's hierarchy.<br>If your website has thousands of pages, these options can greatly affect database performance.</span></p>
								<p><span class="description">Altering the query in the database is more accurate, but can increase database query time.<br>Altering the query on the site is much faster, but can lead to inconsistent pagination. It can also lead to 404 error messages if all queried pages have been excluded.</span></p>
								<div class="tsf-fields"><span class="tsf-toblock"><label for="autodescription-site-settings[alter_search_query]"><input type="checkbox" class="tsf-default-selected" name="autodescription-site-settings[alter_search_query]" id="autodescription-site-settings[alter_search_query]" value="1" checked="checked"> Enable search query alteration? <span class="tsf-tooltip-wrap"><span class="tsf-tooltip-item tsf-help"
								                                                                                                                                                                                                                                                                                                                                                                                       title="This allows you to exclude pages from on-site search results."
								                                                                                                                                                                                                                                                                                                                                                                                       data-desc="This allows you to exclude pages from on-site search results."
								                                                                                                                                                                                                                                                                                                                                                                                       tabindex="0">[?]</span></span></label></span>
									<label for="autodescription-site-settings[alter_search_query_type]">Perform alteration:</label>
									<select name="autodescription-site-settings[alter_search_query_type]" id="autodescription-site-settings[alter_search_query_type]">
										<option value="in_query" selected="selected">In the database</option>
										<option value="post_query">On the site</option>
									</select></div>
								<div class="tsf-fields"><span class="tsf-toblock"><label for="autodescription-site-settings[alter_archive_query]"><input type="checkbox" class="tsf-default-selected" name="autodescription-site-settings[alter_archive_query]" id="autodescription-site-settings[alter_archive_query]" value="1" checked="checked"> Enable archive query alteration? <span class="tsf-tooltip-wrap"><span class="tsf-tooltip-item tsf-help"
								                                                                                                                                                                                                                                                                                                                                                                                           title="This allows you to exclude pages from on-site archive listings."
								                                                                                                                                                                                                                                                                                                                                                                                           data-desc="This allows you to exclude pages from on-site archive listings." tabindex="0">[?]</span></span></label></span>
									<label for="autodescription-site-settings[alter_archive_query_type]">Perform alteration:</label>
									<select name="autodescription-site-settings[alter_archive_query_type]" id="autodescription-site-settings[alter_archive_query_type]">
										<option value="in_query" selected="selected">In the database</option>
										<option value="post_query">On the site</option>
									</select></div>
								<hr>
								<h4>Transient Cache Settings</h4>
								<p><span class="description">To improve performance, generated output can be stored in the database as transient cache.</span></p>
								<div class="tsf-fields"><span class="tsf-toblock"><label for="autodescription-site-settings[cache_sitemap]"><input type="checkbox" class="tsf-default-selected" name="autodescription-site-settings[cache_sitemap]" id="autodescription-site-settings[cache_sitemap]" value="1" checked="checked"> Enable optimized sitemap generation cache? <span class="tsf-tooltip-wrap"><span class="tsf-tooltip-item tsf-help"
								                                                                                                                                                                                                                                                                                                                                                                                   title="Generating the sitemap can use a lot of server resources."
								                                                                                                                                                                                                                                                                                                                                                                                   data-desc="Generating the sitemap can use a lot of server resources."
								                                                                                                                                                                                                                                                                                                                                                                                   tabindex="0">[?]</span></span></label></span></div>
							</div>
							<div class="tsf-tabs-content tsf-general-tabs-content" id="tsf-general-tab-canonical-content">
								<div class="hide-if-tsf-js tsf-content-no-js">
									<div class="tsf-tab tsf-tab-no-js">
					<span class="tsf-nav-tab tsf-active-tab">
						<span class="dashicons dashicons-external tsf-dashicons-tabs"></span>						<span>Canonical</span>					</span>
									</div>
								</div>
								<h4>Canonical URL Settings</h4>
								<p><span class="description">The canonical URL meta tag urges search engines to go to the outputted URL.</span></p>
								<p><span class="description">If the canonical URL meta tag represents the visited page, then the search engine will crawl the visited page. Otherwise, the search engine may go to the outputted URL.</span></p>
								<hr>
								<h4>Scheme Settings</h4>
								<p><span class="description">If your website is accessible via both HTTP as HTTPS, you may want to set this to HTTPS if not detected automatically. Secure connections are preferred by search engines.</span></p>        <label for="autodescription-site-settings[canonical_scheme]">Preferred canonical URL scheme:</label>
								<select name="autodescription-site-settings[canonical_scheme]" id="autodescription-site-settings[canonical_scheme]">
									<option value="automatic" selected="selected">Detect automatically (HTTP)</option>
									<option value="http">HTTP</option>
									<option value="https">HTTPS</option>
								</select>

								<hr>
								<h4>Link Relationship Settings</h4>
								<p><span class="description">Some search engines look for relations between the content of your pages. If you have pagination on a post or page, or have archives indexed, these options will help search engines look for the right page to display in the search results.</span></p>
								<p><span class="description">It's recommended to turn these options on for better SEO consistency and to prevent duplicated content issues.</span></p>
								<div class="tsf-fields"><span class="tsf-toblock"><label for="autodescription-site-settings[prev_next_posts]"><input type="checkbox" class="tsf-default-selected" name="autodescription-site-settings[prev_next_posts]" id="autodescription-site-settings[prev_next_posts]" value="1" checked="checked"> Add <code>rel</code> link tags to posts and pages?</label></span><span class="tsf-toblock"><label for="autodescription-site-settings[prev_next_archives]"><input
												type="checkbox" class="tsf-default-selected" name="autodescription-site-settings[prev_next_archives]" id="autodescription-site-settings[prev_next_archives]" value="1" checked="checked"> Add <code>rel</code> link tags to archives?</label></span><span class="tsf-toblock"><label for="autodescription-site-settings[prev_next_frontpage]"><input type="checkbox" class="tsf-default-selected" name="autodescription-site-settings[prev_next_frontpage]"
								                                                                                                                                                                                                                                                                                                                                                                     id="autodescription-site-settings[prev_next_frontpage]" value="1" checked="checked"> Add <code>rel</code> link tags to the homepage?</label></span>
								</div>
							</div>
							<div class="tsf-tabs-content tsf-general-tabs-content" id="tsf-general-tab-timestamps-content">
								<div class="hide-if-tsf-js tsf-content-no-js">
									<div class="tsf-tab tsf-tab-no-js">
					<span class="tsf-nav-tab tsf-active-tab">
						<span class="dashicons dashicons-clock tsf-dashicons-tabs"></span>						<span>Timestamps</span>					</span>
									</div>
								</div>
								<h4>Timestamp Settings</h4>
								<p><span class="description">Timestamps help indicate when a page has been published and modified.</span></p>
								<hr>

								<fieldset>
									<legend><h4>Timestamp Format Settings</h4></legend>
									<p><span class="description">This setting determines how specific the timestamp is.</span></p>
									<p id="sitemaps-timestamp-format" class="tsf-fields">
				<span class="tsf-toblock">
					<input type="radio" name="autodescription-site-settings[timestamps_format]" id="autodescription-site-settings[timestamps_format_0]" value="0">
					<label for="autodescription-site-settings[timestamps_format_0]">
						<code>2021-07-21</code> <span class="tsf-tooltip-wrap"><span class="tsf-tooltip-item tsf-help" title="This outputs the complete date." data-desc="This outputs the complete date." tabindex="0">[?]</span></span>					</label>
				</span>
										<span class="tsf-toblock">
					<input type="radio" name="autodescription-site-settings[timestamps_format]" id="autodescription-site-settings[timestamps_format_1]" value="1" checked="checked">
					<label for="autodescription-site-settings[timestamps_format_1]">
						<code>2021-07-21T06:04+00:00</code> <span class="tsf-tooltip-wrap"><span class="tsf-tooltip-item tsf-help" title="This outputs the complete date including hours, minutes, and timezone." data-desc="This outputs the complete date including hours, minutes, and timezone." tabindex="0">[?]</span></span>					</label>
				</span>
									</p>
								</fieldset>
							</div>
							<div class="tsf-tabs-content tsf-general-tabs-content" id="tsf-general-tab-exclusions-content">
								<div class="hide-if-tsf-js tsf-content-no-js">
									<div class="tsf-tab tsf-tab-no-js">
					<span class="tsf-nav-tab tsf-active-tab">
						<span class="dashicons dashicons-editor-unlink tsf-dashicons-tabs"></span>						<span>Exclusions</span>					</span>
									</div>
								</div>
								<h4>Exclusion Settings</h4>
								<p><span class="description">When checked, these options will remove meta optimizations, SEO suggestions, and sitemap inclusions for the selected post types and taxonomies. This will allow search engines to crawl the post type and taxonomies without advanced restrictions or directions.</span></p>
								<p><span class="description attention">These options should not need changing when post types and taxonomies are registered correctly. When they aren't, consider applying <code>noindex</code> to purge them from search engines, instead.</span></p>
								<p><span class="description">Default post types and taxonomies can not be excluded.</span></p>
								<hr>
								<h4>Post Type Exclusions</h4>
								<p><span class="description">Select post types which should be excluded.</span></p>
								<p><span class="description">These settings apply to the post type pages and their terms. When terms are shared between post types, all their post types should be checked for this to have an effect.</span></p>
								<div class="tsf-fields"><span class="tsf-toblock"><label for="autodescription-site-settings[disabled_post_types][post]" class="tsf-disabled"><input type="checkbox" class="tsf-excluded-post-types tsf-disabled" name="autodescription-site-settings[disabled_post_types][post]" id="autodescription-site-settings[disabled_post_types][post]" value="1" disabled=""> Posts – <code>post</code></label></span>
									<span class="tsf-toblock"><label for="autodescription-site-settings[disabled_post_types][page]" class="tsf-disabled"><input type="checkbox" class="tsf-excluded-post-types tsf-disabled" name="autodescription-site-settings[disabled_post_types][page]" id="autodescription-site-settings[disabled_post_types][page]" value="1" disabled=""> Pages – <code>page</code></label></span>
									<span class="tsf-toblock"><label for="autodescription-site-settings[disabled_post_types][attachment]" class="tsf-disabled"><input type="checkbox" class="tsf-excluded-post-types tsf-disabled" name="autodescription-site-settings[disabled_post_types][attachment]" id="autodescription-site-settings[disabled_post_types][attachment]" value="1" disabled=""> Media – <code>attachment</code></label></span>
									<span class="tsf-toblock"><label for="autodescription-site-settings[disabled_post_types][download]"><input type="checkbox" class="tsf-excluded-post-types" name="autodescription-site-settings[disabled_post_types][download]" id="autodescription-site-settings[disabled_post_types][download]" value="1"> Downloads – <code>download</code></label></span></div>
								<hr>
								<h4>Taxonomy Exclusions</h4>
								<p><span class="description">Select taxonomies which should be excluded.</span></p>
								<p><span class="description">When taxonomies have all their bound post types excluded, they will inherit their exclusion status.</span></p>
								<div class="tsf-fields"><span class="tsf-toblock"><label for="autodescription-site-settings[disabled_taxonomies][category]" class="tsf-disabled"><input type="checkbox" class="tsf-excluded-taxonomies tsf-disabled" name="autodescription-site-settings[disabled_taxonomies][category]" id="autodescription-site-settings[disabled_taxonomies][category]" value="1" disabled="" data-post-types="[&quot;post&quot;]"> Categories – <code>category</code></label></span>
									<span class="tsf-toblock"><label for="autodescription-site-settings[disabled_taxonomies][post_tag]" class="tsf-disabled"><input type="checkbox" class="tsf-excluded-taxonomies tsf-disabled" name="autodescription-site-settings[disabled_taxonomies][post_tag]" id="autodescription-site-settings[disabled_taxonomies][post_tag]" value="1" disabled="" data-post-types="[&quot;post&quot;]"> Tags – <code>post_tag</code></label></span>
									<span class="tsf-toblock"><label for="autodescription-site-settings[disabled_taxonomies][post_format]" class="tsf-disabled"><input type="checkbox" class="tsf-excluded-taxonomies tsf-disabled" name="autodescription-site-settings[disabled_taxonomies][post_format]" id="autodescription-site-settings[disabled_taxonomies][post_format]" value="1" disabled="" data-post-types="[&quot;post&quot;]"> Formats – <code>post_format</code></label></span>
									<span class="tsf-toblock"><label for="autodescription-site-settings[disabled_taxonomies][download_category]"><input type="checkbox" class="tsf-excluded-taxonomies" name="autodescription-site-settings[disabled_taxonomies][download_category]" id="autodescription-site-settings[disabled_taxonomies][download_category]" value="1" data-post-types="[&quot;download&quot;]"> Download Categories – <code>download_category</code></label></span>
									<span class="tsf-toblock"><label for="autodescription-site-settings[disabled_taxonomies][download_tag]"><input type="checkbox" class="tsf-excluded-taxonomies" name="autodescription-site-settings[disabled_taxonomies][download_tag]" id="autodescription-site-settings[disabled_taxonomies][download_tag]" value="1" data-post-types="[&quot;download&quot;]"> Download Tags – <code>download_tag</code></label></span></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="postbox-container-2">
			</div>
		</div>

		<div class="tsf-bottom-buttons">
			<input type="submit" name="submit" class="button button-primary" value="Save Settings"><input type="submit" name="autodescription-site-settings[tsf-settings-reset]" class="button" value="Reset Settings" onclick="return confirm(`Are you sure you want to reset all SEO settings to their defaults?`)"></div>
	</form>
</div>

