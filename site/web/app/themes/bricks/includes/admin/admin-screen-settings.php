<?php
namespace Bricks;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$settings = Database::$global_settings;

$export_global_settings_url = wp_nonce_url( admin_url( 'admin-ajax.php?action=bricks_export_global_settings' ), 'bricks-nonce', 'nonce' );
?>

<div class="wrap">
	<h1 class="admin-notices-placeholder"></h1>

	<div class="bricks-admin-title-wrapper">
		<h1 class="title"><?php esc_html_e( 'Settings', 'bricks' ); ?></h1>
		<a href="<?php echo esc_url( $export_global_settings_url ); ?>" id="bricks-admin-export-action" class="page-title-action bricks-admin-export-toggle"><?php esc_html_e( 'Export Settings', 'bricks' ); ?></a>
		<a id="bricks-admin-import-action" class="page-title-action bricks-admin-import-toggle"><?php esc_html_e( 'Import Settings', 'bricks' ); ?></a>
	</div>

	<div id="bricks-admin-import-wrapper">
		<div id="bricks-admin-import-form-wrapper">
			<p><?php esc_html_e( 'Select and import your settings JSON file from your computer.', 'bricks' ); ?></p>

			<form id="bricks-admin-import-form" method="post" enctype="multipart/form-data">
				<p><input type="file" name="files" id="bricks_import_files" accept=".json,application/json" required></p>

				<input type="hidden" name="action" value="bricks_import_global_settings">

				<?php wp_nonce_field( 'bricks-nonce', 'nonce' ); // @since 1.5.4 ?>

				<input type="submit" class="button button-primary button-large" value="<?php echo esc_attr__( 'Import settings', 'bricks' ); ?>">
				<button class="button button-large bricks-admin-import-toggle"><?php esc_html_e( 'Cancel', 'bricks' ); ?></button>
			</form>

			<i class="close bricks-admin-import-toggle dashicons dashicons-no-alt"></i>
		</div>
	</div>

	<ul id="bricks-settings-tabs-wrapper" class="nav-tab-wrapper">
		<li><a href="#tab-general" class="nav-tab nav-tab-active" data-tab-id="tab-general"><?php esc_html_e( 'General', 'bricks' ); ?></a></li>
		<li><a href="#tab-builder-access" class="nav-tab" data-tab-id="tab-builder-access"><?php esc_html_e( 'Builder access', 'bricks' ); ?></a></li>
		<li><a href="#tab-templates" class="nav-tab" data-tab-id="tab-templates"><?php esc_html_e( 'Templates', 'bricks' ); ?></a></li>
		<li><a href="#tab-builder" class="nav-tab" data-tab-id="tab-builder"><?php esc_html_e( 'Builder', 'bricks' ); ?></a></li>
		<li><a href="#tab-performance" class="nav-tab" data-tab-id="tab-performance"><?php esc_html_e( 'Performance', 'bricks' ); ?></a></li>
		<li><a href="#tab-api-keys" class="nav-tab" data-tab-id="tab-api-keys"><?php esc_html_e( 'API keys', 'bricks' ); ?></a></li>
		<li><a href="#tab-custom-code" class="nav-tab" data-tab-id="tab-custom-code"><?php esc_html_e( 'Custom code', 'bricks' ); ?></a></li>
		<?php if ( class_exists( 'woocommerce' ) ) { ?>
		<li><a href="#tab-woocommerce" class="nav-tab" data-tab-id="tab-woocommerce">WooCommerce</a></li>
		<?php } ?>
	</ul>

	<form id="bricks-settings" class="bricks-admin-wrapper" method="post" autocomplete="off">
		<table id="tab-general" class="active">
			<tbody>
				<tr>
					<th>
						<label for="postTypes"><?php esc_html_e( 'Post types', 'bricks' ); ?></label>
						<p class="description"><?php printf( esc_html__( 'Select post types to %s.', 'bricks' ), Helpers::article_link( 'editing-with-bricks', esc_html__( 'edit with Bricks', 'bricks' ) ) ); ?></p>
					</th>
					<td>
						<?php
						$registered_post_types = Helpers::get_registered_post_types();

						unset( $registered_post_types['attachment'] );

						$selected_post_types = isset( $settings['postTypes'] ) ? $settings['postTypes'] : [];

						foreach ( $registered_post_types as $key => $label ) {
							?>
						<input type="checkbox" name="postTypes[]" id="post_type_<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $key ); ?>" <?php checked( in_array( $key, $selected_post_types ) ); ?>>
						<label for="post_type_<?php echo esc_attr( $key ); ?>" title="<?php echo esc_attr( $key ); ?>"><?php echo $label; ?></label>
						<br>
						<?php } ?>
					</td>
				</tr>

				<tr>
					<th>
						<label><?php esc_html_e( 'Gutenberg data', 'bricks' ); ?></label>
						<p class="description"><?php echo sprintf( esc_html__( '%s into Bricks data and vice versa.', 'bricks' ), Helpers::article_link( 'gutenberg', esc_html__( 'Convert Gutenberg data', 'bricks' ) ) ); ?></p>
					</th>

					<td>
						<div class="setting-wrapper">
							<input type="checkbox" name="wp_to_bricks" id="wp_to_bricks" <?php checked( isset( $settings['wp_to_bricks'] ) ); ?>>
							<label for="wp_to_bricks"><?php esc_html_e( 'Load Gutenberg data into Bricks', 'bricks' ); ?></label>
						</div>

						<div class="setting-wrapper">
							<input type="checkbox" name="bricks_to_wp" id="bricks_to_wp" <?php checked( isset( $settings['bricks_to_wp'] ) ); ?>>
							<label for="bricks_to_wp"><?php esc_html_e( 'Save Bricks data as Gutenberg data', 'bricks' ); ?></label>
						</div>
					</td>
				</tr>

				<tr>
					<th>
						<label for="svg_uploads"><?php esc_html_e( 'SVG uploads', 'bricks' ); ?></label>
						<p class="description"><?php printf( esc_html__( 'SVG files describe images in XML format and can therefore contain malicious code. With %s enabled Bricks will try to sanitize SVG files during upload.', 'bricks' ), Helpers::article_link( 'svg-uploads', esc_html__( 'SVG uploads', 'bricks' ) ) ); ?></p>
					</th>
					<td>
						<?php
						$roles = wp_roles()->get_names();

						foreach ( $roles as $key => $label ) {
							$role = get_role( $key );
							// Exclude subscriber and other very limited roles
							if ( ! $role->has_cap( 'edit_posts' ) ) {
								continue;
							}

							?>
							<input type="checkbox" name="uploadSvgCapabilities[]" id="upload_svg_<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $key ); ?>" <?php checked( $role->has_cap( Capabilities::UPLOAD_SVG ) ); ?>>
							<label for="upload_svg_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
							<br>
						<?php } ?>
					</td>
				</tr>

				<tr>
					<th>
						<label for="misc_group"><?php esc_html_e( 'Miscellaneous', 'bricks' ); ?></label>
					</th>
					<td>
						<div class="setting-wrapper">
							<input type="checkbox" name="disableOpenGraph" id="disableOpenGraph" <?php checked( isset( $settings['disableOpenGraph'] ) ); ?>>
							<label for="disableOpenGraph"><?php esc_html_e( 'Disable Bricks Open Graph meta tags', 'bricks' ); ?></label>
						</div>

						<div class="setting-wrapper">
							<input type="checkbox" name="disableSeo" id="disableSeo" <?php checked( isset( $settings['disableSeo'] ) ); ?>>
							<label for="disableSeo"><?php esc_html_e( 'Disable Bricks SEO meta tags', 'bricks' ); ?></label>
						</div>

						<div class="setting-wrapper">
							<input type="checkbox" name="customImageSizes" id="customImageSizes" <?php checked( isset( $settings['customImageSizes'] ) ); ?>>
							<label for="customImageSizes"><?php esc_html_e( 'Generate custom image sizes', 'bricks' ); ?></label>
						</div>

						<div class="setting-wrapper">
							<input type="checkbox" name="deleteBricksData" id="deleteBricksData" <?php checked( isset( $settings['deleteBricksData'] ) ); ?>>
							<label for="deleteBricksData"><?php echo sprintf( esc_html__( 'Enable "%s" button', 'bricks' ), esc_html__( 'Delete Bricks data', 'bricks' ) ); ?></label>
						</div>

						<div class="setting-wrapper">
							<input type="checkbox" name="disableSkipLinks" id="disableSkipLinks" <?php checked( isset( $settings['disableSkipLinks'] ) ); ?>>
							<label for="disableSkipLinks"><?php esc_html_e( 'Disable "Skip links"', 'bricks' ); ?></label>
						</div>

						<div class="setting-wrapper">
							<input type="checkbox" name="searchResultsQueryBricksData" id="searchResultsQueryBricksData" <?php checked( isset( $settings['searchResultsQueryBricksData'] ) ); ?>>
							<label for="searchResultsQueryBricksData"><?php esc_html_e( 'Query Bricks data in search results', 'bricks' ); ?></label>
						</div>
					</td>
				</tr>

				<tr>
					<th>
						<label for="misc_group"><?php esc_html_e( 'Custom breakpoints', 'bricks' ); ?></label>
						<p class="description">
							<?php printf( esc_html__( '%s are best configured before you start working on your site.', 'bricks' ), Helpers::article_link( 'responsive-editing/#custom-breakpoints', esc_html__( 'Custom breakpoints', 'bricks' ) ) ); ?>
						</p>
						<span class="badge"><?php esc_html_e( 'experimental', 'bricks' ); ?></span>
					</th>
					<td>
						<div class="setting-wrapper">
							<input type="checkbox" name="customBreakpoints" id="customBreakpoints" <?php checked( isset( $settings['customBreakpoints'] ) ); ?>>
							<label for="customBreakpoints"><?php esc_html_e( 'Custom breakpoints', 'bricks' ); ?></label>
						</div>

						<button id="breakpoints-regenerate-css-files" class="ajax button button-secondary">
							<span class="text"><?php esc_html_e( 'Regenerate CSS files', 'bricks' ); ?></span>
							<span class="spinner is-active"></span>
							<i class="dashicons dashicons-yes hide"></i>
						</button>
					</td>
				</tr>

				<tr>
					<th>
						<label for="search_replace"><?php esc_html_e( 'Convert', 'bricks' ); ?></label>
						<p class="description">
							<?php printf( esc_html__( 'Running the %s detects any outdated Bricks data and automatically updates it to the latest syntax.', 'bricks' ), Helpers::article_link( 'converter', esc_html__( 'Converter', 'bricks' ) ) ); ?>
						</p>
						<span class="badge"><?php esc_html_e( 'experimental', 'bricks' ); ?></span>
					</th>
					<td>
						<p class="info">
							<?php esc_html_e( 'Please create a full-site backup before running the converter.', 'bricks' ); ?>
						</p>

						<ul>
							<li>
								<input type="checkbox" name="convert_container" id="convert_container">
								<label for="convert_container"><?php esc_html_e( 'Convert "Container" to new "Section" & "Block" elements', 'bricks' ); ?></label>
							</li>

							<li>
								<input type="checkbox" name="convert_element_ids_classes" id="convert_element_ids_classes">
								<label for="convert_element_ids_classes"><?php esc_html_e( 'Convert element IDs & classes', 'bricks' ); ?></label>
							</li>

							<li>
								<input type="checkbox" name="add_position_relative" id="add_position_relative">
								<label for="add_position_relative"><?php esc_html_e( 'Add "position: relative" as needed', 'bricks' ); ?></label>
							</li>

							<li>
								<input type="checkbox" name="entry_animation_to_interaction" id="entry_animation_to_interaction">
								<label for="entry_animation_to_interaction"><?php esc_html_e( 'Entry animation to interaction', 'bricks' ); ?></label>
							</li>

							<!-- TODO NEXT: Disable in production in 1.5 (might introduce at a later stage) -->
							<!-- <li>
								<input type="checkbox" name="convert_to_nestable_elements" id="convert_to_nestable_elements">
								<label for="convert_to_nestable_elements"><?php echo esc_html__( 'Convert elements to nestable elements', 'bricks' ) . ' (' . esc_html__( 'Slider', 'bricks' ) . ', ' . esc_html__( 'Testimonials', 'bricks' ) . ', ' . esc_html__( 'Carousel', 'bricks' ) . ')'; ?></label>
							</li> -->
						</ul>

						<button id="bricks-run-converter" class="ajax button button-secondary">
							<span class="text"><?php esc_html_e( 'Convert', 'bricks' ); ?></span>
							<span class="spinner is-active"></span>
						</button>
					</td>
				</tr>
			</tbody>
		</table>

		<table id="tab-builder-access">
			<tbody>
				<tr>
					<th>
						<label><?php esc_html_e( 'Builder access', 'bricks' ); ?></label>
						<p class="description">
							<?php printf( esc_html__( 'Set %s per user role. To define access for a specific user edit the user profile directly.', 'bricks' ), Helpers::article_link( 'builder-access', esc_html__( 'builder access', 'bricks' ) ) ); ?>
						</p>
					</th>

					<?php
					$roles        = wp_roles()->get_names();
					$capabilities = Capabilities::builder_caps();
					?>

					<td>
						<?php
						foreach ( $roles as $key => $label ) {
							$role = get_role( $key );

							echo '<section data-user-role="' . esc_attr( $key ) . '">';
							?>

							<label for="user_role_<?php echo esc_attr( $key ); ?>">
								<span><?php echo esc_html( $label ); ?></span>
							</label>

							<select name="builderCapabilities[<?php echo esc_attr( $key ); ?>]" id="user_role_<?php echo esc_attr( $key ); ?>" <?php echo $key === 'administrator' ? 'disabled' : ''; ?>>
								<?php
								foreach ( $capabilities as $capability ) {
									$selected = $role->has_cap( $capability['capability'] );

									if ( $role->name === 'administrator' && $capability['capability'] === Capabilities::FULL_ACCESS ) {
										$selected = true;
									}

									echo '<option value="' . $capability['capability'] . '" ' . selected( $selected ) . '>' . esc_html( $capability['label'] ) . '</option>';
								}
								?>
							</select>
							<?php
							echo '</section>';
						}
						?>
					</td>

				</tr>

				<tr>
					<th>
						<label for="execute_code"><?php esc_html_e( 'Code Execution', 'bricks' ); ?></label>
						<p class="description"><?php esc_html_e( 'Allow code execution for a specific user or role via the "Code" element. Only add code that you consider safe and grant permission to users you fully trust.', 'bricks' ); ?></p>
					</th>
					<td>

						<input type="checkbox" name="executeCodeDisabled" id="execute_code_disabled" <?php checked( isset( $settings['executeCodeDisabled'] ) ); ?>>
						<label for="execute_code_disabled"><?php esc_html_e( 'Disable code execution', 'bricks' ); ?></label>

						<div class="separator"></div>

						<?php
						$roles = wp_roles()->get_names();

						foreach ( $roles as $key => $label ) {
							$role = get_role( $key );

							// Exclude subscriber and other very limited roles
							if ( ! $role->has_cap( 'edit_posts' ) ) {
								continue;
							}
							?>

							<input type="checkbox" name="executeCodeCapabilities[]" id="execute_code_<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $key ); ?>" <?php checked( $role->has_cap( Capabilities::EXECUTE_CODE ) ); ?> <?php disabled( isset( $settings['executeCodeDisabled'] ) ); ?>>
							<label for="execute_code_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
							<br>
						<?php } ?>
					</td>
				</tr>

			</tbody>
		</table>

		<table id="tab-templates">
			<tbody>
				<tr>
					<th><label><?php esc_html_e( 'My templates', 'bricks' ); ?></label></th>
					<td>
						<input type="checkbox" name="defaultTemplatesDisabled" id="defaultTemplatesDisabled" <?php checked( isset( $settings['defaultTemplatesDisabled'] ) ); ?>>
						<label for="defaultTemplatesDisabled"><?php esc_html_e( 'Disable default templates', 'bricks' ); ?></label>
						<p class="description"><?php esc_html_e( 'If no template conditions are set Bricks shows published templates (header, footer, etc.) on the frontend of your site. Select this setting to disable this behavior. Make sure to set template conditions instead.', 'bricks' ); ?></p>

						<div class="separator"></div>

						<input type="checkbox" name="publicTemplates" id="publicTemplates" <?php checked( isset( $settings['publicTemplates'] ) ); ?>>
						<label for="publicTemplates"><?php esc_html_e( 'Public templates', 'bricks' ); ?></label>
						<p class="description"><?php esc_html_e( 'Enable to make your templates public and viewable by anyone online. Disable to allow only logged-in users to view your templates.', 'bricks' ); ?></p>

						<div class="separator"></div>

						<!-- <label for="myTemplatesAccess" class="sub"><?php esc_html_e( 'My Templates Access', 'bricks' ); ?></label><br> -->
						<input type="checkbox" name="myTemplatesAccess" id="myTemplatesAccess" <?php checked( isset( $settings['myTemplatesAccess'] ) ); ?>>
						<label for="myTemplatesAccess"><?php esc_html_e( 'My Templates Access', 'bricks' ); ?></label>
						<p class="description"><?php esc_html_e( 'Allow other sites to browse and insert your templates from their template library. Restrict template access via "Whitelist URLs" and "Password Protection" settings below.', 'bricks' ); ?></p>

						<div class="separator"></div>

						<label for="myTemplatesWhitelist" class="sub"><?php esc_html_e( 'Whitelist URLs', 'bricks' ); ?></label>
						<textarea rows="3" name="myTemplatesWhitelist" id="myTemplatesWhitelist"><?php echo isset( $settings['myTemplatesWhitelist'] ) ? $settings['myTemplatesWhitelist'] : ''; ?></textarea>
						<p class="description"><?php esc_html_e( 'Only grant access to your templates to the websites entered above. One URL per line.', 'bricks' ); ?></p>

						<div class="separator"></div>

						<label for="myTemplatesPassword" class="sub"><?php esc_html_e( 'Password Protection', 'bricks' ); ?></label>
						<input type="text" name="myTemplatesPassword" id="myTemplatesPassword" value="<?php echo isset( $settings['myTemplatesPassword'] ) ? $settings['myTemplatesPassword'] : ''; ?>" spellcheck="false">
						<p class="description"><?php esc_html_e( 'Password protect your templates.', 'bricks' ); ?></p>
					</td>
				</tr>

				<tr>
					<th>
						<label>
							<?php
							echo esc_html__( 'Remote templates', 'bricks' );

							echo Helpers::article_link( 'remote-templates', '<i class="dashicons dashicons-editor-help"></i>' );
							?>
						</label>
						<p class="description"><?php echo esc_html__( 'Load templates from any another Bricks installation you have access to in your template library.', 'bricks' ); ?></p>
					</th>

					<td>
						<label for="remoteTemplatesUrl" class="sub"><?php esc_html_e( 'Remote templates URL', 'bricks' ); ?></label>
						<input type="text" name="remoteTemplatesUrl" id="remoteTemplatesUrl" value="<?php echo isset( $settings['remoteTemplatesUrl'] ) ? esc_url( $settings['remoteTemplatesUrl'] ) : ''; ?>" spellcheck="false" autocomplete="new-password">
						<p class="description"><?php esc_html_e( 'Make sure the remote website entered above has granted you "My Templates Access".', 'bricks' ); ?></p>

						<div class="separator"></div>

						<label for="remoteTemplatesPassword" class="sub"><?php esc_html_e( 'Remote Templates Password', 'bricks' ); ?></label>
						<input type="password" name="remoteTemplatesPassword" id="remoteTemplatesPassword" value="<?php echo isset( $settings['remoteTemplatesPassword'] ) ? $settings['remoteTemplatesPassword'] : ''; ?>" spellcheck="false" autocomplete="new-password">
						<p class="description"><?php esc_html_e( 'Copy & paste the "My Templates Access" password provided by the remote site in here.', 'bricks' ); ?></p>
					</td>
				</tr>

				<tr>
					<th>
						<label><?php esc_html_e( 'Miscellaneous', 'bricks' ); ?></label>
					</th>

					<td>
						<input type="checkbox" name="convertTemplates" id="convertTemplates" <?php checked( isset( $settings['convertTemplates'] ) ); ?>>
						<label for="convertTemplates"><?php esc_html_e( 'Convert templates', 'bricks' ); ?></label>
						<p class="description">
						<?php
						echo esc_html__( 'Convert template on import/insert from Container to new layout elements structure', 'bricks' );

						$_layout_elements_link_text = esc_html__( 'Section', 'bricks' ) . ' > ' . esc_html__( 'Container', 'bricks' ) . ' > ' . esc_html__( 'Block', 'bricks' ) . ' / Div';

						echo ' (' . Helpers::article_link( 'layout', $_layout_elements_link_text ) . ').';
						?>
						</p>
					</td>
				</tr>
			</tbody>
		</table>

		<table id="tab-builder">
			<tbody>
				<tr>
					<th><label><?php esc_html_e( 'Disable autosave', 'bricks' ); ?></label></th>
					<td>
						<input type="checkbox" name="builderAutosaveDisabled" id="builderAutosaveDisabled" <?php checked( isset( $settings['builderAutosaveDisabled'] ) ); ?>>
						<label for="builderAutosaveDisabled"><?php esc_html_e( 'Disable autosave', 'bricks' ); ?></label>
				</tr>

				<?php if ( ! isset( $settings['builderAutosaveDisabled'] ) ) { ?>
				<tr>
					<th><label for="builderAutosaveInterval"><?php esc_html_e( 'Autosave interval (seconds)', 'bricks' ); ?></label></th>
					<td>
						<input type="number" name="builderAutosaveInterval" id="builderAutosaveInterval" value="<?php echo isset( $settings['builderAutosaveInterval'] ) ? esc_attr( $settings['builderAutosaveInterval'] ) : ''; ?>" min="15" placeholder="60">
						<p class="description"><?php esc_html_e( 'Default: 60 seconds. Minimum autosave interval is 15 seconds.', 'bricks' ); ?></p>
					</td>
				</tr>
				<?php } ?>

				<?php
				$builder_mode = empty( $settings['builderMode'] ) ? '' : $settings['builderMode'];
				?>

				<tr>
					<th><label><?php esc_html_e( 'Builder mode', 'bricks' ); ?></label></th>
					<td>
						<select name="builderMode" id="builderMode">
							<option value="dark" <?php selected( $builder_mode === 'dark', true, true ); ?>><?php esc_html_e( 'Dark', 'bricks' ); ?></option>
							<option value="light" <?php selected( $builder_mode === 'light', true, true ); ?>><?php esc_html_e( 'Light', 'bricks' ); ?></option>
							<option value="custom" <?php selected( $builder_mode === 'custom', true, true ); ?>><?php esc_html_e( 'Custom', 'bricks' ); ?></option>
						</select>
					</td>
				</tr>

				<?php if ( $builder_mode === 'custom' ) { ?>
				<tr>
					<th>
						<label><?php echo esc_html__( 'Builder mode', 'bricks' ) . ' (' . esc_html__( 'Custom', 'bricks' ) . ')'; ?></label>
						<p class="description">
							<?php
							echo esc_html__( 'Create your own builder mode via CSS variables.', 'bricks' );
							echo ' (' . Helpers::article_link( 'builder-mode', esc_html__( 'Learn more', 'bricks' ) ) . ')'; // TODO: Create article & video
							?>
						</p>
					</th>
					<td>
						<textarea class="bricks-code" name="builderModeCss" id="builderModeCss" cols="30" rows="20" spellcheck="false"><?php echo empty( $settings['builderModeCss'] ) ? '' : stripslashes_deep( $settings['builderModeCss'] ); ?></textarea>
					</td>
				</tr>
				<?php } ?>

				<?php
				$languages      = get_available_languages();
				$builder_locale = ! empty( $settings['builderLocale'] ) ? $settings['builderLocale'] : false;

				if ( $builder_locale === 'en_US' ) {
					$builder_locale = '';
				} elseif ( ! $builder_locale || ! in_array( $builder_locale, $languages, true ) ) {
					$builder_locale = 'site-default';
				}
				?>
				<tr>
					<th><label><?php esc_html_e( 'Language', 'bricks' ); ?></label></th>
					<td>
						<?php
						wp_dropdown_languages(
							[
								'name'                     => 'builderLocale',
								'id'                       => 'builderLocale',
								'selected'                 => $builder_locale,
								'languages'                => $languages,
								'show_available_translations' => false,
								'show_option_site_default' => true,
								'show_option_en_us'        => true,
							]
						);
						?>
						<p class="description"><?php esc_html_e( 'Set the builder language.', 'bricks' ); ?></p>

						<div class="separator"></div>

						<select name="builderLanguageDirection" id="builderLanguageDirection">
							<?php
							$builder_language_direction_options = [
								''    => esc_html__( 'Auto', 'bricks' ),
								'ltr' => esc_html__( 'Left to right', 'bricks' ),
								'rtl' => esc_html__( 'Right to left', 'bricks' ),
							];

							$builder_language_direction_current_value = ! empty( $settings['builderLanguageDirection'] ) ? $settings['builderLanguageDirection'] : '';

							foreach ( $builder_language_direction_options as $key => $label ) {
								echo '<option value="' . $key . '" ' . selected( $key, $builder_language_direction_current_value, true, false ) . '>' . $label . '</option>';
							}
							?>
						</select>

						<p class="description"><?php esc_html_e( 'Set the builder language direction.', 'bricks' ); ?></p>
					</td>
				</tr>

				<?php
				$toolbar_link_options = [
					'current'   => esc_html__( 'Preview', 'bricks' ),
					'home'      => esc_html__( 'Home page', 'bricks' ),
					'dashboard' => esc_html__( 'Dashboard', 'bricks' ),
					'postType'  => esc_html__( 'Post type', 'bricks' ),
					'wp'        => esc_html__( 'Edit in WordPress', 'bricks' ),
					'none'      => esc_html__( 'No link', 'bricks' )
				];

				$toolbar_link_current_value = isset( $settings['builderToolbarLogoLink'] ) ? $settings['builderToolbarLogoLink'] : false;

				$builder_wrap_element   = ! empty( $settings['builderWrapElement'] ) ? $settings['builderWrapElement'] : '';
				$builder_insert_element = ! empty( $settings['builderInsertElement'] ) ? $settings['builderInsertElement'] : '';
				$builder_insert_layout  = ! empty( $settings['builderInsertLayout'] ) ? $settings['builderInsertLayout'] : '';
				?>
				<tr>
					<th><label><?php esc_html_e( 'Toolbar logo link', 'bricks' ); ?></label></th>
					<td>
						<select name="builderToolbarLogoLink" id="builderToolbarLogoLink">
							<?php foreach ( $toolbar_link_options as $key => $label ) { ?>
								<option value="<?php echo $key; ?>" <?php selected( $key, $toolbar_link_current_value, true ); ?>><?php echo $label; ?></option>
							<?php } ?>
						</select>

						<p class="description"><?php esc_html_e( 'Set custom link destination for builder toolbar logo.', 'bricks' ); ?></p>

						<div class="separator"></div>

						<?php if ( $toolbar_link_current_value !== 'none' ) { ?>
						<input type="checkbox" name="builderToolbarLogoLinkNewTab" id="builderToolbarLogoLinkNewTab" <?php checked( isset( $settings['builderToolbarLogoLinkNewTab'] ) ); ?>>
						<label for="builderToolbarLogoLinkNewTab"><?php esc_html_e( 'Open in new tab', 'bricks' ); ?></label>
						<?php } ?>
					</td>
				</tr>

				<tr>
					<th><label><?php esc_html_e( 'Control panel', 'bricks' ); ?></label></th>
					<td>
						<input type="checkbox" name="builderDisableGlobalClassesInterface" id="builderDisableGlobalClassesInterface" <?php checked( isset( $settings['builderDisableGlobalClassesInterface'] ) ); ?>>
						<label for="builderDisableGlobalClassesInterface"><?php echo esc_html__( 'Disable global classes', 'bricks' ) . ' (' . esc_html__( 'Interface', 'bricks' ) . ')'; ?></label>
					</td>
				</tr>

				<tr>
					<th><label><?php esc_html_e( 'Canvas', 'bricks' ); ?></label></th>
					<td>
						<input type="checkbox" name="disableElementSpacing" id="disableElementSpacing" <?php checked( isset( $settings['disableElementSpacing'] ) ); ?>>
						<label for="disableElementSpacing"><?php esc_html_e( 'Disable element spacing', 'bricks' ) . ' (margin & padding)'; ?></label>
					</td>
				</tr>

				<tr>
					<th><label><?php esc_html_e( 'Structure panel', 'bricks' ); ?></label></th>
					<td>
						<div class="title"><?php esc_html_e( 'Element actions', 'bricks' ); ?>:</div>

						<input type="checkbox" name="structureDuplicateElement" id="structureDuplicateElement" <?php checked( isset( $settings['structureDuplicateElement'] ) ); ?>>
						<label for="structureDuplicateElement"><?php esc_html_e( 'Duplicate', 'bricks' ); ?></label>
						<br>

						<input type="checkbox" name="structureDeleteElement" id="structureDeleteElement" <?php checked( isset( $settings['structureDeleteElement'] ) ); ?>>
						<label for="structureDeleteElement"><?php esc_html_e( 'Delete', 'bricks' ); ?></label>

						<div class="separator"></div>

						<input type="checkbox" name="structureCollapsed" id="structureCollapsed" <?php checked( isset( $settings['structureCollapsed'] ) ); ?>>
						<label for="structureCollapsed"><?php esc_html_e( 'Collapse on page load', 'bricks' ); ?></label>
						<br>

						<input type="checkbox" name="structureAutoSync" id="structureAutoSync" <?php checked( isset( $settings['structureAutoSync'] ) ); ?>>
						<label for="structureAutoSync"><?php esc_html_e( 'Expand active element & scroll into view', 'bricks' ); ?></label>
						<br>
					</td>
				</tr>

				<tr>
					<th><label><?php esc_html_e( 'Element actions', 'bricks' ); ?></label></th>
					<td>
						<div class="title"><?php esc_html_e( 'Wrap element', 'bricks' ); ?>:</div>

						<select name="builderWrapElement" id="builderWrapElement">
							<option value=""><?php esc_html_e( 'Block', 'bricks' ); ?></option>
							<option value="div" <?php selected( 'div', $builder_wrap_element ); ?>>Div</option>
							<option value="container" <?php selected( 'container', $builder_wrap_element ); ?>><?php esc_html_e( 'Container', 'bricks' ); ?></option>
						</select>

						<div class="description"><?php esc_html_e( 'Available via keyboard shortcut & right-click context menu.', 'bricks' ); ?></div>

						<div class="separator"></div>

						<div class="title"><?php esc_html_e( 'Insert element', 'bricks' ); ?>:</div>

						<select name="builderInsertElement" id="builderInsertElement">
							<option value=""><?php esc_html_e( 'Block', 'bricks' ); ?></option>
							<option value="div" <?php selected( 'div', $builder_insert_element ); ?>>Div</option>
							<option value="container" <?php selected( 'container', $builder_insert_element ); ?>><?php esc_html_e( 'Container', 'bricks' ); ?></option>
						</select>

						<div class="description"><?php esc_html_e( 'Available via "+" action icon & right-click context menu.', 'bricks' ); ?></div>

						<div class="separator"></div>

						<div class="title"><?php esc_html_e( 'Insert layout', 'bricks' ); ?>:</div>

						<select name="builderInsertLayout" id="builderInsertLayout">
							<option value=""><?php esc_html_e( 'Block', 'bricks' ); ?></option>
							<option value="div" <?php selected( 'div', $builder_insert_layout ); ?>>Div</option>
							<option value="container" <?php selected( 'container', $builder_insert_layout ); ?>><?php esc_html_e( 'Container', 'bricks' ); ?></option>
						</select>

						<div class="description"><?php esc_html_e( 'Available via "Layout" action icon.', 'bricks' ); ?></div>
					</td>
				</tr>

				<tr>
					<th>
						<label><?php esc_html_e( 'Disable WP REST API render', 'bricks' ); ?></label>
						<span class="badge"><?php esc_html_e( 'experimental', 'bricks' ); ?></span>
					</th>
					<td>
						<input type="checkbox" name="builderDisableRestApi" id="builderDisableRestApi" <?php checked( isset( $settings['builderDisableRestApi'] ) ); ?>>
						<label for="builderDisableRestApi"><?php esc_html_e( 'Disable WP REST API render', 'bricks' ); ?></label>
						<p class="description"><?php esc_html_e( 'Use AJAX instead of WP REST API calls to render elements.', 'bricks' ); ?></p>
					</td>
				</tr>

				<tr>
					<th>
						<label><?php esc_html_e( 'Dynamic data', 'bricks' ); ?></label>
						<span class="badge"><?php esc_html_e( 'experimental', 'bricks' ); ?></span>
					</th>
					<td>
						<input type="checkbox" name="enableDynamicDataPreview" id="enableDynamicDataPreview" <?php checked( isset( $settings['enableDynamicDataPreview'] ) ); ?>>
						<label for="enableDynamicDataPreview"><?php esc_html_e( 'Render dynamic data text on canvas', 'bricks' ); ?></label>
						<p class="description"><?php esc_html_e( 'Enable to render the dynamic data text on the canvas to improve the preview experience.', 'bricks' ); ?></p>

						<div class="separator"></div>

						<input type="checkbox" name="builderDisableWpCustomFields" id="builderDisableWpCustomFields" <?php checked( isset( $settings['builderDisableWpCustomFields'] ) ); ?>>
						<label for="builderDisableWpCustomFields"><?php esc_html_e( 'Disable WordPress custom fields in dropdown', 'bricks' ); ?></label>
						<p class="description"><?php esc_html_e( 'Disable for better in-builder performance. Using dynamic data tags like {cf_my_wordpress_field} is still possible.', 'bricks' ); ?></p>

						<div class="separator"></div>

						<ul>
							<li>
								<div class="title"><?php esc_html_e( 'Dropdown', 'bricks' ); ?>:</div>
							</li>
							<li>
								<input type="checkbox" name="builderDynamicDropdownKey" id="builderDynamicDropdownKey" <?php checked( isset( $settings['builderDynamicDropdownKey'] ) ); ?>>
								<label for="builderDynamicDropdownKey"><?php esc_html_e( 'Show dynamic data key in dropdown', 'bricks' ); ?></label>
							</li>

							<li>
								<input type="checkbox" name="builderDynamicDropdownNoLabel" id="builderDynamicDropdownNoLabel" <?php checked( isset( $settings['builderDynamicDropdownNoLabel'] ) ); ?>>
								<label for="builderDynamicDropdownNoLabel"><?php esc_html_e( 'Hide dynamic data label in dropdown', 'bricks' ); ?></label>
							</li>

							<li>
								<input type="checkbox" name="builderDynamicDropdownExpand" id="builderDynamicDropdownExpand" <?php checked( isset( $settings['builderDynamicDropdownExpand'] ) ); ?>>
								<label for="builderDynamicDropdownExpand"><?php esc_html_e( 'Expand panel when dropdown is visible', 'bricks' ); ?></label>
							</li>
						</ul>
					</td>
				</tr>
			</tbody>
		</table>

		<table id="tab-performance">
			<tbody>
				<tr>
					<th>
						<label for="disableEmojis"><?php esc_html_e( 'Disable emojis', 'bricks' ); ?></label>
					</th>
					<td>
						<input type="checkbox" name="disableEmojis" id="disableEmojis" <?php checked( isset( $settings['disableEmojis'] ) ); ?>>
						<label for="disableEmojis"><?php esc_html_e( 'Disable emojis', 'bricks' ); ?></label>
						<p class="description"><?php esc_html_e( 'Set for better performance if you don\'t use emojis on your site.', 'bricks' ); ?></p>
					</td>
				</tr>

				<tr>
					<th>
						<label for="disableEmbed"><?php esc_html_e( 'Disable embed', 'bricks' ); ?></label>
					</th>
					<td>
						<input type="checkbox" name="disableEmbed" id="disableEmbed" <?php checked( isset( $settings['disableEmbed'] ) ); ?>>
						<label for="disableEmbed"><?php esc_html_e( 'Disable embed', 'bricks' ); ?></label>
						<p class="description"><?php esc_html_e( 'Set for better performance if you don\'t use embeds, such as YouTube videos, on your site.', 'bricks' ); ?></p>
					</td>
				</tr>

				<tr>
					<th>
						<label for="disableGoogleFonts"><?php esc_html_e( 'Disable Google Fonts', 'bricks' ); ?></label>
					</th>
					<td>
						<input type="checkbox" name="disableGoogleFonts" id="disableGoogleFonts" <?php checked( isset( $settings['disableGoogleFonts'] ) ); ?>>
						<label for="disableGoogleFonts"><?php esc_html_e( 'Disable Google Fonts', 'bricks' ); ?></label>
						<p class="description"><?php esc_html_e( 'Set if you don\'t use Google Fonts or you\'ve uploaded and self-host Google Fonts as "Custom Fonts".', 'bricks' ); ?></p>
					</td>
				</tr>

				<?php
				$lazy_load_disabled = isset( $settings['disableLazyLoad'] );
				$lazy_load_offset   = isset( $settings['offsetLazyLoad'] ) ? $settings['offsetLazyLoad'] : '';
				?>

				<tr>
					<th>
						<label for="disableLazyLoad"><?php esc_html_e( 'Disable lazy loading', 'bricks' ); ?></label>
					</th>
					<td>
						<input type="checkbox" name="disableLazyLoad" id="disableLazyLoad" <?php checked( $lazy_load_disabled ); ?>>
						<label for="disableLazyLoad"><?php esc_html_e( 'Disable lazy loading', 'bricks' ); ?></label>
						<p class="description"><?php esc_html_e( 'Set if you have problems with Bricks built-in lazy loading.', 'bricks' ); ?></p>

						<?php if ( ! $lazy_load_disabled ) { ?>
						<div class="separator"></div>

						<input type="number" name="offsetLazyLoad" id="offsetLazyLoad" value="<?php echo $lazy_load_offset; ?>" placeholder="300">
						<p class="description"><?php esc_html_e( 'Lazy load offset', 'bricks' ); ?> (px)</p>
						<?php } ?>
					</td>
				</tr>

				<tr>
					<th>
						<label for="disableJqueryMigrate"><?php esc_html_e( 'Disable jQuery migrate', 'bricks' ); ?></label>
					</th>
					<td>
						<input type="checkbox" name="disableJqueryMigrate" id="disableJqueryMigrate" <?php checked( isset( $settings['disableJqueryMigrate'] ) ); ?>>
						<label for="disableJqueryMigrate"><?php esc_html_e( 'Disable jQuery migrate', 'bricks' ); ?></label>
						<p class="description"><?php esc_html_e( 'Set for better performance if you don\'t run any jQuery code older than version 1.9.', 'bricks' ); ?></p>
					</td>
				</tr>

				<tr>
					<th>
						<label for="cacheQueryLoops"><?php esc_html_e( 'Cache query loops', 'bricks' ); ?></label>
						<span class="badge"><?php esc_html_e( 'experimental', 'bricks' ); ?></span>
					</th>
					<td>
						<input type="checkbox" name="cacheQueryLoops" id="cacheQueryLoops" <?php checked( isset( $settings['cacheQueryLoops'] ) ); ?>>
						<label for="cacheQueryLoops"><?php esc_html_e( 'Cache query loops', 'bricks' ); ?></label>
					</td>
				</tr>

				<tr>
					<th>
						<label for="elementAttsAsNeeded"><?php esc_html_e( 'Element ID & class', 'bricks' ); ?></label>
						<span class="badge"><?php esc_html_e( 'experimental', 'bricks' ); ?></span>
					</th>
					<td>
						<input type="checkbox" name="elementAttsAsNeeded" id="elementAttsAsNeeded" <?php checked( isset( $settings['elementAttsAsNeeded'] ) ); ?>>
						<label for="elementAttsAsNeeded"><?php esc_html_e( 'Add Element ID & class as needed', 'bricks' ); ?></label>
						<p class="description"><?php esc_html_e( 'Element ID & class gets added to every element by default.', 'bricks' ); ?></p>
					</td>
				</tr>

				<tr>
					<th>
						<label for="disableClassChaining"><?php esc_html_e( 'Disable class chaining', 'bricks' ); ?></label>
						<span class="badge"><?php esc_html_e( 'experimental', 'bricks' ); ?></span>
					</th>
					<td>
						<input type="checkbox" name="disableClassChaining" id="disableClassChaining" <?php checked( isset( $settings['disableClassChaining'] ) ); ?>>
						<label for="disableClassChaining"><?php esc_html_e( 'Disable chaining element & global class', 'bricks' ); ?></label>
					</td>
				</tr>

				<?php // @since 1.3.4: Part of asset-optimization ?>
				<tr>
					<th>
						<label for="cssLoading">
							<?php
							esc_html_e( 'CSS loading method', 'bricks' );
							echo Helpers::article_link( 'asset-loading', '<i class="dashicons dashicons-editor-help"></i>' );
							?>
						</label>
						<p class="description"><?php esc_html_e( 'Page-specific styles are loaded inline by default. Select "External files" to load required CSS only and to allow for stylesheet caching.', 'bricks' ); ?></p>
					</th>
					<td>
						<?php
						$css_loading_method          = ! empty( $settings['cssLoading'] ) ? $settings['cssLoading'] : 'inline';
						$wp_uploads_dir_is_writeable = is_writable( Assets::$wp_uploads_dir );

						// Check: Is WP 'uploads' directory writeable?
						if ( $wp_uploads_dir_is_writeable ) {
							echo '<select name="cssLoading" id="cssLoading">';
							echo '<option value="">' . esc_html__( 'Inline styles (default)', 'bricks' ) . '</option>';
							echo '<option value="file" ' . selected( 'file', $css_loading_method, true ) . '>' . esc_html__( 'External files', 'bricks' ) . '</option>';
							echo '</select>';

							// Regenerate CSS files (AJAX callback: get_css_files_list)
							if ( $css_loading_method === 'file' ) {
								echo '<div id="bricks-css-loading-generate">';

								// No '/bricks/css' file directory created yet: Show notification
								if ( ! is_dir( Assets::$css_dir ) ) {
									echo '<div class="info">' . esc_html__( 'Please click the button below to generate all required CSS files.', 'bricks' ) . '</div>';
								}

								echo '<button class="ajax button button-secondary">';
								echo '<span class="text">' . esc_html__( 'Regenerate CSS files', 'bricks' ) . '</span>';
								echo '<span class="spinner is-active"></span>';
								echo '</button>';

								// HTML for generated CSS files
								echo '<div class="results hide">';
								echo '<div><strong><span class="count"></span> ' . esc_html__( 'CSS files processed', 'bricks' ) . ':</strong></div>';
								echo '<ul></ul>';
								echo '</div>';

								echo '</div>';
							}
						} else {
							echo '<div>' . esc_html__( 'Your uploads directory writing permissions are insufficient.', 'bricks' ) . '</div>';
						}
						?>
					</td>
				</tr>

				<?php
				// @since 1.3.4: Part of asset-optimization
				if ( ! Helpers::google_fonts_disabled() ) {
					?>
				<tr>
					<th>
						<label for="webfontLoading"><?php esc_html_e( 'Webfont loading method', 'bricks' ); ?></label>
						<p class="description"><?php esc_html_e( 'Webfonts (such as Google Fonts) are loaded via stylesheets by default. Select "Webfont Loader" to avoid FOUT (Flash of unstyled text) by hiding your website content until all webfonts are loaded.', 'bricks' ); ?></p>
					</th>
					<td>
						<?php
						$webfont_loading_method = ! empty( $settings['webfontLoading'] ) ? $settings['webfontLoading'] : '';

						echo '<select name="webfontLoading" id="webfontLoading">';
						echo '<option value="">' . esc_html__( 'Stylesheets (default)', 'bricks' ) . '</option>';
						echo '<option value="webfontloader" ' . selected( 'webfontloader', $webfont_loading_method, true ) . '>Webfont Loader (JS)</option>';
						echo '</select>';
						?>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>

		<table id="tab-api-keys">
			<?php
			function encipher_api_key( $settings, $setting_key ) {
				$api_key    = isset( $settings[ $setting_key ] ) ? esc_attr( $settings[ $setting_key ] ) : '';
				$length     = strlen( $api_key );
				$characters = str_split( $api_key );
				$enciphered = '';

				foreach ( $characters as $index => $character ) {
					$enciphered .= $index > 4 && $index < $length - 4 ? 'x' : $character;
				}

				return $enciphered;
			}
			?>
			<tbody>
				<tr>
					<th><label for="apiKeyUnsplash"><?php esc_html_e( 'Unsplash API key', 'bricks' ); ?></label></th>
					<td>
						<input type="text" name="apiKeyUnsplash" id="apiKeyUnsplash" value="<?php echo encipher_api_key( $settings, 'apiKeyUnsplash' ); ?>" spellcheck="false">
						<p class="description"><?php echo Helpers::article_link( 'unsplash', esc_html__( 'How to get your Unsplash API key', 'bricks' ) ); ?></p>
					</td>
				</tr>

				<tr>
					<th><label for="apiKeyGoogleMaps"><?php esc_html_e( 'Google Maps API keys', 'bricks' ); ?></label></th>
					<td>
						<input type="text" name="apiKeyGoogleMaps" id="apiKeyGoogleMaps" value="<?php echo encipher_api_key( $settings, 'apiKeyGoogleMaps' ); ?>" spellcheck="false">
						<p class="description"><?php echo '<a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank" rel="noopener">' . esc_html__( 'How to get your Google Maps API key', 'bricks' ) . '</a>'; ?></p>
					</td>
				</tr>

				<tr>
					<th><label for="apiKeyGoogleRecaptcha"><?php esc_html_e( 'Google reCAPTCHA v3 API Site key', 'bricks' ); ?></label></th>
					<td>
						<input type="text" name="apiKeyGoogleRecaptcha" id="apiKeyGoogleRecaptcha" value="<?php echo encipher_api_key( $settings, 'apiKeyGoogleRecaptcha' ); ?>" spellcheck="false">
						<p class="description"><?php echo '<a href="https://developers.google.com/recaptcha/docs/v3" target="_blank" rel="noopener">' . esc_html__( 'How to get your Google reCAPTCHA v3 API Site key', 'bricks' ) . '</a>'; ?></p>
					</td>
				</tr>

				<tr>
					<th><label for="apiSecretKeyGoogleRecaptcha"><?php esc_html_e( 'Google reCAPTCHA v3 API Secret key', 'bricks' ); ?></label></th>
					<td>
						<input type="text" name="apiSecretKeyGoogleRecaptcha" id="apiSecretKeyGoogleRecaptcha" value="<?php echo encipher_api_key( $settings, 'apiSecretKeyGoogleRecaptcha' ); ?>" spellcheck="false">
						<p class="description"><?php echo '<a href="https://developers.google.com/recaptcha/docs/v3" target="_blank" rel="noopener">' . esc_html__( 'How to get your Google reCAPTCHA v3 API Secret key', 'bricks' ) . '</a>'; ?></p>
					</td>
				</tr>

				<?php
				/*
				<tr>
					<th><label for="recaptchaLanguage"><?php esc_html_e( 'Google reCAPTCHA language', 'bricks' ); ?></label></th>
					<td>
						<input type="text" name="recaptchaLanguage" id="recaptchaLanguage" value="<?php echo isset( $settings['recaptchaLanguage'] ) ? esc_attr( $settings['recaptchaLanguage'] ) : ''; ?>" spellcheck="false">
						<p class="description"><?php printf( esc_html__( 'Auto-detect the user\'s language if not set.', 'bricks' ) . ' <a href="https://developers.google.com/recaptcha/docs/language" target="_blank">' . esc_html__( 'Language codes', 'bricks' ) .'</a>' ); ?></p>
					</td>
				</tr>

				*/
				?>

				<tr>
					<th>
						<label for="apiKeyMailchimp"><?php esc_html_e( 'MailChimp API key', 'bricks' ); ?></label>
					</th>
					<td>
						<input type="text" name="apiKeyMailchimp" id="apiKeyMailchimp" value="<?php echo encipher_api_key( $settings, 'apiKeyMailchimp' ); ?>" spellcheck="false">
						<p class="description"><?php echo esc_html( 'Save settings to sync lists.', 'bricks' ) . ' <a href="https://mailchimp.com/help/about-api-keys/" target="_blank" rel="noopener">' . esc_html__( 'How to get your MailChimp API key', 'bricks' ) . '</a>'; ?></p>
					</td>
				</tr>

				<tr>
					<th><label for="apiKeySendgrid"><?php esc_html_e( 'Sendgrid API key', 'bricks' ); ?></label></th>
					<td>
						<input type="text" name="apiKeySendgrid" id="apiKeySendgrid" value="<?php echo encipher_api_key( $settings, 'apiKeySendgrid' ); ?>" spellcheck="false">
						<p class="description"><?php echo esc_html( 'Save settings to sync lists.', 'bricks' ) . ' <a href="https://sendgrid.com/docs/ui/account-and-settings/api-keys/#creating-an-api-key" target="_blank" rel="noopener">' . esc_html__( 'How to get your SenGrid API key', 'bricks' ) . '</a>'; ?></p>
					</td>
				</tr>

				<tr>
					<th><label for="facebookAppId"><?php esc_html_e( 'Facebook App ID', 'bricks' ); ?></label></th>
					<td>
						<input type="text" name="facebookAppId" id="facebookAppId" value="<?php echo encipher_api_key( $settings, 'facebookAppId' ); ?>" spellcheck="false">
						<p class="description"><?php echo '<a href="https://developers.facebook.com/docs/apps#register" target="_blank" rel="noopener">' . esc_html__( 'How to get your Facebook App ID', 'bricks' ) . '</a>'; ?></p>
					</td>
				</tr>
			</tbody>
		</table>

		<table id="tab-custom-code">
			<thead>
				<tr>
					<td><?php echo esc_html__( 'Any custom code below will load globally on all pages of your website. Wrap your scripts below in &lt;script&gt; tags.', 'bricks' ) . ' ' . Helpers::article_link( 'custom-code', esc_html__( 'Use the builder to add custom code to a specific page.', 'bricks' ) ); ?></td>
				</tr>
			</thead>

			<tbody>
				<tr>
					<th><label for="customCss"><?php esc_html_e( 'Custom CSS', 'bricks' ); ?></label></th>
					<td>
						<textarea class="bricks-code" name="customCss" id="customCss" cols="30" rows="10" spellcheck="false"><?php echo isset( $settings['customCss'] ) ? $settings['customCss'] : ''; ?></textarea>
						<p class="description"><?php printf( __( 'Inline styles (CSS) are added to the %s tag.', 'bricks' ), htmlspecialchars( '<head>' ) ); ?></p>
					</td>
				</tr>

				<tr>
					<th><label for="customScriptsHeader"><?php esc_html_e( 'Header scripts', 'bricks' ); ?></label></th>
					<td>
						<textarea class="bricks-code" name="customScriptsHeader" id="customScriptsHeader" cols="30" rows="10" spellcheck="false"><?php echo isset( $settings['customScriptsHeader'] ) ? stripslashes_deep( $settings['customScriptsHeader'] ) : ''; ?></textarea>
						<p class="description"><?php printf( __( 'Header scripts are added right before closing %s tag. Perfect for tracking scripts such as Google Analytics, etc.', 'bricks' ), htmlspecialchars( '</head>' ) ); ?></p>
					</td>
				</tr>

				<tr>
					<th><label for="customScriptsBodyHeader"><?php esc_html_e( 'Body (header) scripts', 'bricks' ); ?></label></th>
					<td>
						<textarea class="bricks-code" name="customScriptsBodyHeader" id="customScriptsBodyHeader" cols="30" rows="10" spellcheck="false"><?php echo isset( $settings['customScriptsBodyHeader'] ) ? stripslashes_deep( $settings['customScriptsBodyHeader'] ) : ''; ?></textarea>
						<p class="description"><?php printf( __( 'Body scripts are added right after opening %s tag.', 'bricks' ), htmlspecialchars( '<body>' ) ); ?></p>
					</td>
				</tr>

				<tr>
					<th><label for="customScriptsBodyFooter"><?php esc_html_e( 'Body (footer) scripts', 'bricks' ); ?></label></th>
					<td>
						<textarea class="bricks-code" name="customScriptsBodyFooter" id="customScriptsBodyFooter" cols="30" rows="10" spellcheck="false"><?php echo isset( $settings['customScriptsBodyFooter'] ) ? stripslashes_deep( $settings['customScriptsBodyFooter'] ) : ''; ?></textarea>
						<p class="description"><?php printf( __( 'Footer scripts are added right before closing %s tag.', 'bricks' ), htmlspecialchars( '</body>' ) ); ?></p>
					</td>
				</tr>
			</tbody>
		</table>

		<?php
		if ( class_exists( 'woocommerce' ) ) {
			$badge_sale = ! empty( $settings['woocommerceBadgeSale'] ) ? $settings['woocommerceBadgeSale'] : '';
			$badge_new  = ! empty( $settings['woocommerceBadgeNew'] ) ? $settings['woocommerceBadgeNew'] : '';
			?>
		<table id="tab-woocommerce">
			<tbody>
				<tr>
					<th>
						<label for="woocommerceDisableBuilder"><?php esc_html_e( 'Miscellaneous', 'bricks' ); ?></label>
					</th>
					<td>
						<div class="setting-wrapper">
							<input type="checkbox" name="woocommerceDisableBuilder" id="woocommerceDisableBuilder" <?php checked( isset( $settings['woocommerceDisableBuilder'] ) ); ?>>
							<label for="woocommerceDisableBuilder"><?php esc_html_e( 'Disable WooCommerce builder', 'bricks' ); ?></label>
						</div>
					</td>
				</tr>

				<tr>
					<th>
						<label for="woocommerceBadgeSale"><?php esc_html_e( 'Products', 'bricks' ); ?></label>
					</th>
					<td>
						<section>
							<label for="woocommerceBadgeSale"><?php esc_html_e( 'Product badge "Sale"', 'bricks' ); ?></label>
							<select name="woocommerceBadgeSale" id="woocommerceBadgeSale">
								<option value=""><?php esc_html_e( 'None', 'bricks' ); ?></option>
								<option value="text" <?php selected( 'text', $badge_sale, true ); ?>><?php esc_html_e( 'Text', 'bricks' ); ?></option>
								<option value="percentage" <?php selected( 'percentage', $badge_sale, true ); ?>><?php esc_html_e( 'Percentage', 'bricks' ); ?></option>
							</select>
						</section>

						<section>
						<label for="woocommerceBadgeNew"><?php esc_html_e( 'Product badge "New"', 'bricks' ); ?></label>
							<input type="number" name="woocommerceBadgeNew" id="woocommerceBadgeNew" value="<?php echo $badge_new; ?>">
							<p class="description"><?php esc_html_e( 'Show badge if product is less than .. days old.', 'bricks' ); ?></p>
						</section>
					</td>
				</tr>

				<tr>
					<th>
						<label for="woocommerceBadgeSale"><?php esc_html_e( 'Single product', 'bricks' ); ?></label>
					</th>
					<td>
						<section>
							<div class="setting-wrapper">
								<input type="checkbox" name="woocommerceDisableProductGalleryZoom" id="woocommerceDisableProductGalleryZoom" <?php checked( isset( $settings['woocommerceDisableProductGalleryZoom'] ) ); ?>>
								<label for="woocommerceDisableProductGalleryZoom"><?php esc_html_e( 'Disable product gallery zoom', 'bricks' ); ?></label>
							</div>
						</section>

						<section>
							<div class="setting-wrapper">
								<input type="checkbox" name="woocommerceDisableProductGalleryLightbox" id="woocommerceDisableProductGalleryLightbox" <?php checked( isset( $settings['woocommerceDisableProductGalleryLightbox'] ) ); ?>>
								<label for="woocommerceDisableProductGalleryLightbox"><?php esc_html_e( 'Disable product gallery lightbox', 'bricks' ); ?></label>
							</div>
						</section>

						<section>
							<div class="setting-wrapper">
								<input type="checkbox" name="woocommerceEnableAjaxAddToCart" id="woocommerceEnableAjaxAddToCart" <?php checked( isset( $settings['woocommerceEnableAjaxAddToCart'] ) ); ?>>
								<label for="woocommerceEnableAjaxAddToCart"><?php esc_html_e( 'Enable AJAX add to cart', 'bricks' ); ?></label>
								<p class="description"><?php esc_html__( 'Make sure ticked "Enable AJAX add to cart buttons on archives" in WooCommerce > Settings > Products', 'bricks' ); ?>
								</p>
							</div>
						</section>
					</td>
				</tr>
			</tbody>
		</table>
		<?php } ?>

		<div class="submit-wrapper">
			<input type="submit" name="save" value="<?php esc_html_e( 'Save Settings', 'bricks' ); ?>" class="button button-primary button-large">
			<input type="submit" name="reset" value="<?php esc_html_e( 'Reset Settings', 'bricks' ); ?>" class="button button-secondary button-large">
		</div>

		<span class="spinner saving"></span>
	</form>
</div>
