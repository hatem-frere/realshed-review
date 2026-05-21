<?php
/**
 * The Main Plugin Manager Class
 *
 * Path: wp-content/plugins/realshed-core/includes/class-realshed-core.php
 *
 * @package Realshed_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Realshed_Core' ) ) {

	final class Realshed_Core {

		/**
		 * Class instance.
		 *
		 * @var Realshed_Core|null
		 */
		private static $instance = null;

		/**
		 * Redux parent menu/page slug.
		 *
		 * Must match Redux page_slug in:
		 * wp-content/plugins/realshed-core/redux/config-template.php
		 *
		 * @var string
		 */
		private $redux_parent_slug = 'realshed_options';

		/**
		 * Get instance.
		 *
		 * @return Realshed_Core
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * Important:
		 * self::$instance is assigned before init() because redux/config-template.php
		 * may call Realshed_Core::instance() while this object is still loading.
		 */
		private function __construct() {
			self::$instance = $this;
			$this->init();
		}

		/**
		 * Initialize plugin systems.
		 */
		public function init() {
			$this->load_helpers();

			add_action( 'init', array( $this, 'register_cpts' ), 5 );

			$this->load_shortcodes();

			/*
			 * Load Redux later than the plugin constructor/init flow.
			 *
			 * This prevents fatal errors caused by calling user/capability functions
			 * such as current_user_can() too early, before WordPress has loaded the
			 * pluggable user functions.
			 */
			add_action( 'plugins_loaded', array( $this, 'load_redux' ), 20 );

			/*
			 * Keep Homepage Settings logically synced:
			 * - If the required Home page is missing, disable Custom Homepage mode.
			 * - If the wizard creates Home, the wizard enables Custom Homepage mode.
			 *
			 * This sync only runs when Redux Framework is active, because the option
			 * belongs to the Redux options panel.
			 */
			add_action( 'admin_init', array( $this, 'sync_custom_homepage_mode_with_homepage' ), 5 );

			add_action( 'admin_init', array( $this, 'handle_permalink_settings_return_redirect' ) );
			add_action( 'admin_notices', array( $this, 'required_pages_admin_notice' ) );
			add_action( 'admin_post_realshed_create_required_pages', array( $this, 'handle_create_required_pages_request' ) );

			add_action( 'widgets_init', array( $this, 'register_widgets' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		}

		/**
		 * Check whether Redux Framework is available.
		 *
		 * The Setup Wizard lives inside Redux. Therefore, any notice or URL pointing
		 * to the Setup Wizard must not appear before Redux Framework is installed
		 * and activated.
		 *
		 * @return bool
		 */
		public function is_redux_framework_active() {
			return class_exists( 'Redux' );
		}

		/**
		 * Load shortcodes.
		 */
		public function load_shortcodes() {
			if ( file_exists( REALSHED_CORE_PATH . 'includes/class-realshed-shortcodes.php' ) ) {
				require_once REALSHED_CORE_PATH . 'includes/class-realshed-shortcodes.php';

				if ( class_exists( 'Realshed_Shortcodes' ) ) {
					new Realshed_Shortcodes();
				}
			}
		}

		/**
		 * Return the pages required by the Realshed ecosystem.
		 *
		 * This structure is intentionally extensible.
		 *
		 * To add a new required page later, add another item:
		 *
		 * 'new-page-slug' => array(
		 *     'title'                       => esc_html__( 'New Page Title', 'realshed-core' ),
		 *     'content'                     => '',
		 *     'template'                    => '',
		 *     'set_as_front_page'           => false,
		 *     'enable_custom_homepage_mode' => false,
		 * ),
		 *
		 * @return array
		 */
		public function get_required_pages() {
			return array(
				'home'              => array(
					'title'                       => esc_html__( 'Home', 'realshed-core' ),
					'content'                     => '',
					'template'                    => '',
					'set_as_front_page'           => true,
					'enable_custom_homepage_mode' => true,
				),
				'properties'        => array(
					'title'                       => esc_html__( 'Properties', 'realshed-core' ),
					'content'                     => '',
					'template'                    => '',
					'set_as_front_page'           => false,
					'enable_custom_homepage_mode' => false,
				),
				'search-properties' => array(
					'title'                       => esc_html__( 'Search Properties', 'realshed-core' ),
					'content'                     => '',
					'template'                    => '',
					'set_as_front_page'           => false,
					'enable_custom_homepage_mode' => false,
				),
				'agents'            => array(
					'title'                       => esc_html__( 'Agents', 'realshed-core' ),
					'content'                     => '',
					'template'                    => '',
					'set_as_front_page'           => false,
					'enable_custom_homepage_mode' => false,
				),
				'compare'           => array(
					'title'                       => esc_html__( 'Compare Properties', 'realshed-core' ),
					'content'                     => '',
					'template'                    => '',
					'set_as_front_page'           => false,
					'enable_custom_homepage_mode' => false,
				),
			);
		}

		/**
		 * Check whether all required pages already exist.
		 *
		 * @return bool
		 */
		public function required_pages_exist() {
			foreach ( $this->get_required_pages() as $slug => $page_config ) {
				unset( $page_config );

				$page = $this->get_existing_required_page( $slug );

				if ( ! $page || empty( $page->ID ) ) {
					return false;
				}
			}

			return true;
		}

		/**
		 * Check whether the required Home page is missing.
		 *
		 * @return bool
		 */
		public function is_required_homepage_missing() {
			$home_page = $this->get_existing_required_page( 'home' );

			return ( ! $home_page || empty( $home_page->ID ) );
		}

		/**
		 * Keep Custom Homepage mode synced with the required Home page.
		 *
		 * Requirement:
		 * If the required Home page is missing, Realshed Options > Homepage Settings
		 * > Enable Custom Homepage must automatically be turned OFF.
		 */
		public function sync_custom_homepage_mode_with_homepage() {
			if ( ! $this->is_redux_framework_active() ) {
				return;
			}

			if ( ! function_exists( 'current_user_can' ) || ! current_user_can( 'manage_options' ) ) {
				return;
			}

			if ( $this->is_required_homepage_missing() ) {
				$this->disable_custom_homepage_mode();
			}
		}

		/**
		 * Return the Redux Setup Wizard tab URL.
		 *
		 * @return string
		 */
		public function get_setup_wizard_url() {
			return admin_url( 'admin.php?page=' . $this->redux_parent_slug . '&tab=setup_wizard' );
		}

		/**
		 * Return the secure direct URL that creates missing required pages.
		 *
		 * This intentionally uses a link instead of a nested form, because Redux
		 * Options pages already render their own form. Nested forms are invalid
		 * HTML and can cause the Create button to submit Redux settings instead
		 * of calling admin-post.php.
		 *
		 * @return string
		 */
		public function get_create_required_pages_url() {
			return wp_nonce_url(
				admin_url( 'admin-post.php?action=realshed_create_required_pages' ),
				'realshed_create_required_pages',
				'realshed_setup_nonce'
			);
		}

		/**
		 * Return the Permalinks settings URL.
		 *
		 * The redirect back to Redux is handled after the user clicks Save Changes
		 * on the Permalinks screen.
		 *
		 * @return string
		 */
		public function get_permalink_settings_url() {
			return admin_url( 'options-permalink.php' );
		}

		/**
		 * Check if current admin request is the Redux Setup Wizard tab.
		 *
		 * @return bool
		 */
		public function is_current_setup_wizard_tab() {
			$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
			$tab  = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : '';

			return ( $this->redux_parent_slug === $page && 'setup_wizard' === $tab );
		}

		/**
		 * Show mandatory admin notice while required pages are missing.
		 */
		public function required_pages_admin_notice() {
			if ( ! $this->is_redux_framework_active() ) {
				return;
			}

			if ( ! function_exists( 'current_user_can' ) || ! current_user_can( 'manage_options' ) || $this->required_pages_exist() ) {
				return;
			}

			if ( $this->is_current_setup_wizard_tab() ) {
				return;
			}
			?>
			<div class="notice notice-error">
				<p>
					<strong><?php esc_html_e( 'Realshed setup is required:', 'realshed-core' ); ?></strong>
					<?php esc_html_e( 'Required pages are missing. Please complete the Setup Wizard before continuing theme configuration.', 'realshed-core' ); ?>
				</p>
				<p>
					<a class="button button-primary" href="<?php echo esc_url( $this->get_setup_wizard_url() ); ?>">
						<?php esc_html_e( 'Open Realshed Setup Wizard', 'realshed-core' ); ?>
					</a>
				</p>
			</div>
			<?php
		}

		/**
		 * Render the full Setup Wizard content inside the Redux Setup Wizard tab.
		 *
		 * @return string
		 */
		public function get_setup_wizard_redux_content() {
			if ( ! $this->is_redux_framework_active() ) {
				return '';
			}

			if ( ! function_exists( 'current_user_can' ) || ! current_user_can( 'manage_options' ) ) {
				return '';
			}

			$created_count         = isset( $_GET['realshed_pages_created'] ) ? absint( wp_unslash( $_GET['realshed_pages_created'] ) ) : null;
			$failed_count          = isset( $_GET['realshed_pages_failed'] ) ? absint( wp_unslash( $_GET['realshed_pages_failed'] ) ) : 0;
			$completed             = isset( $_GET['realshed_setup_completed'] ) ? absint( wp_unslash( $_GET['realshed_setup_completed'] ) ) : 0;
			$permalink_rules_saved = isset( $_GET['realshed_permalink_rules_saved'] ) ? absint( wp_unslash( $_GET['realshed_permalink_rules_saved'] ) ) : 0;
			$errors                = $this->get_setup_errors_for_current_user();

			$show_permalink_modal = ( 1 === $completed && 0 === $failed_count );

			ob_start();
			?>
			<div class="realshed-redux-setup-wizard">
				<div class="realshed-setup-hero">
					<h2><?php esc_html_e( 'Realshed Setup Wizard', 'realshed-core' ); ?></h2>
					<p>
						<?php esc_html_e( 'Create the required pages used by the homepage, properties archive, search results, agents, and comparison system. The Home page will automatically be assigned as the static WordPress homepage, and Custom Homepage mode will be enabled automatically.', 'realshed-core' ); ?>
					</p>
				</div>

				<?php if ( $permalink_rules_saved ) : ?>
					<div class="notice notice-success realshed-setup-notice">
						<p>
							<strong><?php esc_html_e( 'Permalink rules refreshed successfully.', 'realshed-core' ); ?></strong>
							<?php esc_html_e( 'You have been returned to Realshed Options. You can now continue configuring the theme.', 'realshed-core' ); ?>
						</p>
					</div>
				<?php endif; ?>

				<?php if ( $completed && $failed_count > 0 ) : ?>
					<div class="notice notice-error realshed-setup-notice">
						<p>
							<?php
							printf(
								/* translators: 1: created pages count, 2: failed pages count. */
								esc_html__( 'Setup finished with warnings. Created %1$d page(s), but %2$d page(s) failed.', 'realshed-core' ),
								absint( $created_count ),
								absint( $failed_count )
							);
							?>
						</p>

						<?php if ( ! empty( $errors ) ) : ?>
							<ul class="realshed-setup-error-list">
								<?php foreach ( $errors as $error ) : ?>
									<li><?php echo esc_html( $error ); ?></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<div class="notice notice-info realshed-setup-notice">
					<p>
						<strong><?php esc_html_e( 'Mandatory setup:', 'realshed-core' ); ?></strong>
						<?php esc_html_e( 'These pages are required for the Realshed theme ecosystem to work correctly. The Home page will also be selected automatically in Settings > Reading as the static homepage, and the Homepage Settings switch “Enable Custom Homepage” will be turned on. If the Home page is deleted or missing later, that switch will automatically be turned off.', 'realshed-core' ); ?>
					</p>
				</div>

				<div class="realshed-setup-table-wrap">
					<table class="widefat realshed-pages-table">
						<thead>
							<tr>
								<th><?php esc_html_e( 'Page', 'realshed-core' ); ?></th>
								<th><?php esc_html_e( 'Slug', 'realshed-core' ); ?></th>
								<th><?php esc_html_e( 'Purpose', 'realshed-core' ); ?></th>
								<th><?php esc_html_e( 'Setup Action', 'realshed-core' ); ?></th>
								<th><?php esc_html_e( 'Status', 'realshed-core' ); ?></th>
								<th><?php esc_html_e( 'Action', 'realshed-core' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $this->get_required_pages() as $slug => $page_config ) : ?>
								<?php
								$page          = $this->get_existing_required_page( $slug );
								$title         = isset( $page_config['title'] ) ? $page_config['title'] : ucwords( str_replace( '-', ' ', $slug ) );
								$purpose       = $this->get_required_page_purpose( $slug );
								$setup_action  = $this->get_required_page_setup_action( $slug, $page_config );
								$page_view_url = $this->get_required_page_view_url( $page, $slug );
								?>
								<tr>
									<td class="realshed-page-title"><?php echo esc_html( $title ); ?></td>
									<td><code><?php echo esc_html( $slug ); ?></code></td>
									<td><?php echo esc_html( $purpose ); ?></td>
									<td><?php echo esc_html( $setup_action ); ?></td>
									<td>
										<?php if ( $page && ! empty( $page->ID ) ) : ?>
											<span class="realshed-status-badge realshed-status-badge--exists">
												<?php esc_html_e( 'Exists', 'realshed-core' ); ?>
											</span>
										<?php else : ?>
											<span class="realshed-status-badge realshed-status-badge--missing">
												<?php esc_html_e( 'Missing', 'realshed-core' ); ?>
											</span>
										<?php endif; ?>
									</td>
									<td>
										<?php if ( $page && ! empty( $page->ID ) ) : ?>
											<div class="realshed-table-actions">
												<a href="<?php echo esc_url( get_edit_post_link( $page->ID ) ); ?>">
													<?php esc_html_e( 'Edit', 'realshed-core' ); ?>
												</a>
												<?php if ( ! empty( $page_view_url ) ) : ?>
													<span aria-hidden="true">|</span>
													<a href="<?php echo esc_url( $page_view_url ); ?>" target="_blank" rel="noopener noreferrer">
														<?php esc_html_e( 'View', 'realshed-core' ); ?>
													</a>
												<?php endif; ?>
											</div>
										<?php else : ?>
											<span class="realshed-table-empty">&mdash;</span>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>

				<div class="realshed-setup-form">
					<p class="submit">
						<a class="button button-primary" href="<?php echo esc_url( $this->get_create_required_pages_url() ); ?>">
							<?php esc_html_e( 'Create Missing Required Pages', 'realshed-core' ); ?>
						</a>
					</p>
				</div>
			</div>

			<?php if ( $show_permalink_modal ) : ?>
				<div class="realshed-setup-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="realshed-setup-modal-title">
					<div class="realshed-setup-modal">
						<div class="realshed-setup-modal__header">
							<h2 id="realshed-setup-modal-title">
								<?php esc_html_e( 'Required pages were created successfully. Flush rewrite rules now.', 'realshed-core' ); ?>
							</h2>
						</div>

						<div class="realshed-setup-modal__body">
							<p>
								<?php esc_html_e( 'The required Realshed pages now exist. The Home page has also been set automatically as the static WordPress homepage, and Custom Homepage mode has been enabled. To make sure the new URLs work correctly, refresh WordPress permalink rules from the Permalinks settings page.', 'realshed-core' ); ?>
							</p>

							<ol>
								<li><?php esc_html_e( 'Click the button below to open the Permalinks settings page.', 'realshed-core' ); ?></li>
								<li><?php esc_html_e( 'Do not change anything unless you intentionally want to change your permalink structure.', 'realshed-core' ); ?></li>
								<li><?php esc_html_e( 'Scroll down and click “Save Changes”.', 'realshed-core' ); ?></li>
								<li><?php esc_html_e( 'After saving, you will be redirected back to Realshed Options automatically.', 'realshed-core' ); ?></li>
							</ol>

							<p>
								<strong><?php esc_html_e( 'Important:', 'realshed-core' ); ?></strong>
								<?php esc_html_e( 'This dialog intentionally has no close button. The next required step is to visit the Permalinks page.', 'realshed-core' ); ?>
							</p>
						</div>

						<div class="realshed-setup-modal__footer">
							<a class="button button-primary button-hero" href="<?php echo esc_url( $this->get_permalink_settings_url() ); ?>">
								<?php esc_html_e( 'Go to Permalinks', 'realshed-core' ); ?>
							</a>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php
			return ob_get_clean();
		}

		/**
		 * Return a human-readable purpose for each required page.
		 *
		 * @param string $slug Page slug.
		 * @return string
		 */
		public function get_required_page_purpose( $slug ) {
			$purposes = array(
				'home'              => esc_html__( 'Main website homepage.', 'realshed-core' ),
				'properties'        => esc_html__( 'Main property archive and browse page.', 'realshed-core' ),
				'search-properties' => esc_html__( 'Dedicated property search results page.', 'realshed-core' ),
				'agents'            => esc_html__( 'Agents listing page.', 'realshed-core' ),
				'compare'           => esc_html__( 'Property comparison page.', 'realshed-core' ),
			);

			return isset( $purposes[ $slug ] ) ? $purposes[ $slug ] : esc_html__( 'Required theme page.', 'realshed-core' );
		}

		/**
		 * Return a human-readable setup action for each required page.
		 *
		 * @param string $slug        Page slug.
		 * @param array  $page_config Page configuration.
		 * @return string
		 */
		public function get_required_page_setup_action( $slug, $page_config ) {
			unset( $slug );

			if ( ! empty( $page_config['set_as_front_page'] ) && ! empty( $page_config['enable_custom_homepage_mode'] ) ) {
				return esc_html__( 'Create page, set as the static homepage, and enable Custom Homepage mode.', 'realshed-core' );
			}

			if ( ! empty( $page_config['set_as_front_page'] ) ) {
				return esc_html__( 'Create page and automatically set as the static homepage.', 'realshed-core' );
			}

			if ( ! empty( $page_config['template'] ) ) {
				return esc_html__( 'Create page and assign the configured page template.', 'realshed-core' );
			}

			return esc_html__( 'Create required page if missing.', 'realshed-core' );
		}

		/**
		 * Safely return a public view URL for a required page.
		 *
		 * This method intentionally avoids calling get_permalink() during early
		 * Redux loading because WordPress rewrite globals may not be ready yet.
		 *
		 * @param WP_Post|null $page Page object.
		 * @param string       $slug Required page slug.
		 * @return string
		 */
		public function get_required_page_view_url( $page, $slug ) {
			if ( ! $page || empty( $page->ID ) ) {
				return '';
			}

			$slug = sanitize_title( $slug );

			if ( empty( $slug ) ) {
				return '';
			}

			return home_url( '/' . trailingslashit( $slug ) );
		}

		/**
		 * Handle the explicit page creation request from the Redux Setup Wizard tab.
		 */
		public function handle_create_required_pages_request() {
			if ( ! function_exists( 'current_user_can' ) || ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'You do not have permission to perform this action.', 'realshed-core' ) );
			}

			if ( ! $this->is_redux_framework_active() ) {
				wp_die( esc_html__( 'Redux Framework must be active before running the Realshed Setup Wizard.', 'realshed-core' ) );
			}

			check_admin_referer( 'realshed_create_required_pages', 'realshed_setup_nonce' );

			$result = $this->create_required_pages();

			if ( ! empty( $result['errors'] ) ) {
				$this->store_setup_errors_for_current_user( $result['errors'] );
			}

			if ( 0 === absint( $result['failed'] ) ) {
				$this->schedule_permalink_return_redirect();
			}

			wp_safe_redirect(
				add_query_arg(
					array(
						'page'                     => $this->redux_parent_slug,
						'tab'                      => 'setup_wizard',
						'realshed_pages_created'   => absint( $result['created'] ),
						'realshed_pages_failed'    => absint( $result['failed'] ),
						'realshed_setup_completed' => 1,
					),
					admin_url( 'admin.php' )
				)
			);
			exit;
		}

		/**
		 * Return an existing required page by slug.
		 *
		 * Trashed pages are ignored intentionally.
		 *
		 * @param string $slug Page slug.
		 * @return WP_Post|null
		 */
		public function get_existing_required_page( $slug ) {
			$pages = get_posts(
				array(
					'name'           => sanitize_title( $slug ),
					'post_type'      => 'page',
					'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'future' ),
					'posts_per_page' => 1,
					'fields'         => 'all',
					'no_found_rows'  => true,
				)
			);

			return ! empty( $pages[0] ) ? $pages[0] : null;
		}

		/**
		 * Create missing required pages on explicit administrator request.
		 *
		 * @return array
		 */
		public function create_required_pages() {
			$created_count = 0;
			$failed_count  = 0;
			$errors        = array();

			foreach ( $this->get_required_pages() as $slug => $page_config ) {
				$page_check = $this->get_existing_required_page( $slug );

				if ( $page_check && ! empty( $page_check->ID ) ) {
					$this->maybe_apply_required_page_settings( $page_check->ID, $slug, $page_config );
					continue;
				}

				$title   = isset( $page_config['title'] ) ? $page_config['title'] : ucwords( str_replace( '-', ' ', $slug ) );
				$content = isset( $page_config['content'] ) ? $page_config['content'] : '';

				$page_id = wp_insert_post(
					array(
						'post_title'   => wp_strip_all_tags( $title ),
						'post_name'    => sanitize_title( $slug ),
						'post_status'  => 'publish',
						'post_type'    => 'page',
						'post_author'  => get_current_user_id(),
						'post_content' => $content,
					),
					true
				);

				if ( is_wp_error( $page_id ) ) {
					$failed_count++;
					$errors[] = sprintf(
						/* translators: 1: page title, 2: error message. */
						esc_html__( '%1$s could not be created: %2$s', 'realshed-core' ),
						$title,
						$page_id->get_error_message()
					);
					continue;
				}

				if ( ! $page_id ) {
					$failed_count++;
					$errors[] = sprintf(
						/* translators: %s: page title. */
						esc_html__( '%s could not be created for an unknown reason.', 'realshed-core' ),
						$title
					);
					continue;
				}

				$this->maybe_apply_required_page_settings( $page_id, $slug, $page_config );

				$created_count++;
			}

			if ( $created_count > 0 ) {
				flush_rewrite_rules();
			}

			return array(
				'created' => $created_count,
				'failed'  => $failed_count,
				'errors'  => $errors,
			);
		}

		/**
		 * Apply optional settings for a required page.
		 *
		 * @param int    $page_id     Page ID.
		 * @param string $slug        Page slug.
		 * @param array  $page_config Page config.
		 */
		public function maybe_apply_required_page_settings( $page_id, $slug, $page_config ) {
			unset( $slug );

			$page_id = absint( $page_id );

			if ( ! $page_id ) {
				return;
			}

			if ( ! empty( $page_config['template'] ) ) {
				update_post_meta( $page_id, '_wp_page_template', sanitize_text_field( $page_config['template'] ) );
			}

			if ( ! empty( $page_config['set_as_front_page'] ) ) {
				$this->set_static_homepage( $page_id );
			}

			if ( ! empty( $page_config['enable_custom_homepage_mode'] ) ) {
				$this->enable_custom_homepage_mode();
			}
		}

		/**
		 * Set a page as the static WordPress homepage.
		 *
		 * This updates:
		 * Settings > Reading > Your homepage displays > A static page
		 * Settings > Reading > Homepage
		 *
		 * @param int $page_id Page ID.
		 */
		public function set_static_homepage( $page_id ) {
			$page_id = absint( $page_id );

			if ( ! $page_id ) {
				return;
			}

			$page = get_post( $page_id );

			if ( ! $page || 'page' !== $page->post_type ) {
				return;
			}

			if ( 'trash' === $page->post_status ) {
				return;
			}

			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $page_id );
		}

		/**
		 * Enable the custom homepage mode inside Redux options.
		 *
		 * This updates:
		 * Realshed Options > Homepage Settings > Enable Custom Homepage
		 *
		 * It intentionally preserves all existing Redux option values.
		 */
		public function enable_custom_homepage_mode() {
			if ( ! $this->is_redux_framework_active() ) {
				return;
			}

			$options = get_option( $this->redux_parent_slug, array() );

			if ( ! is_array( $options ) ) {
				$options = array();
			}

			$options['enable_custom_homepage'] = true;

			update_option( $this->redux_parent_slug, $options );
		}

		/**
		 * Disable the custom homepage mode inside Redux options.
		 *
		 * This updates:
		 * Realshed Options > Homepage Settings > Enable Custom Homepage
		 *
		 * It intentionally preserves all existing Redux option values.
		 */
		public function disable_custom_homepage_mode() {
			if ( ! $this->is_redux_framework_active() ) {
				return;
			}

			$options = get_option( $this->redux_parent_slug, array() );

			if ( ! is_array( $options ) ) {
				$options = array();
			}

			if ( isset( $options['enable_custom_homepage'] ) && false === (bool) $options['enable_custom_homepage'] ) {
				return;
			}

			$options['enable_custom_homepage'] = false;

			update_option( $this->redux_parent_slug, $options );
		}

		/**
		 * Return the current user's transient key for permalink return flow.
		 *
		 * @return string
		 */
		public function get_permalink_return_transient_key() {
			return 'realshed_permalink_return_' . get_current_user_id();
		}

		/**
		 * Schedule a temporary return redirect after permalink rules are saved.
		 */
		public function schedule_permalink_return_redirect() {
			set_transient(
				$this->get_permalink_return_transient_key(),
				1,
				15 * MINUTE_IN_SECONDS
			);
		}

		/**
		 * Check whether the current user is expected to return to Realshed Options
		 * after saving Permalinks.
		 *
		 * @return bool
		 */
		public function should_return_after_permalink_save() {
			return (bool) get_transient( $this->get_permalink_return_transient_key() );
		}

		/**
		 * Delete the temporary permalink return flag.
		 */
		public function clear_permalink_return_redirect() {
			delete_transient( $this->get_permalink_return_transient_key() );
		}

		/**
		 * Redirect the user back to Realshed Options after saving Permalinks.
		 */
		public function handle_permalink_settings_return_redirect() {
			global $pagenow;

			if ( ! function_exists( 'current_user_can' ) || ! current_user_can( 'manage_options' ) ) {
				return;
			}

			if ( ! $this->is_redux_framework_active() ) {
				return;
			}

			if ( 'options-permalink.php' !== $pagenow ) {
				return;
			}

			$settings_updated = isset( $_GET['settings-updated'] ) ? sanitize_key( wp_unslash( $_GET['settings-updated'] ) ) : '';

			if ( 'true' !== $settings_updated ) {
				return;
			}

			if ( ! $this->should_return_after_permalink_save() ) {
				return;
			}

			$this->clear_permalink_return_redirect();

			wp_safe_redirect(
				add_query_arg(
					array(
						'page'                           => $this->redux_parent_slug,
						'tab'                            => 'setup_wizard',
						'realshed_permalink_rules_saved' => 1,
					),
					admin_url( 'admin.php' )
				)
			);
			exit;
		}

		/**
		 * Store setup errors temporarily for the current admin user.
		 *
		 * @param array $errors Error messages.
		 */
		public function store_setup_errors_for_current_user( $errors ) {
			set_transient(
				'realshed_setup_errors_' . get_current_user_id(),
				array_map( 'sanitize_text_field', (array) $errors ),
				MINUTE_IN_SECONDS * 5
			);
		}

		/**
		 * Retrieve and delete setup errors for the current admin user.
		 *
		 * @return array
		 */
		public function get_setup_errors_for_current_user() {
			$transient_key = 'realshed_setup_errors_' . get_current_user_id();
			$errors        = get_transient( $transient_key );

			if ( false !== $errors ) {
				delete_transient( $transient_key );
				return (array) $errors;
			}

			return array();
		}

		/**
		 * Load helper files.
		 */
		public function load_helpers() {
			if ( file_exists( REALSHED_CORE_PATH . 'includes/helpers/class-helpers.php' ) ) {
				require_once REALSHED_CORE_PATH . 'includes/helpers/class-helpers.php';
			}

			if ( file_exists( REALSHED_CORE_PATH . 'includes/helpers/class-search-helper.php' ) ) {
				require_once REALSHED_CORE_PATH . 'includes/helpers/class-search-helper.php';
			}
		}

		/**
		 * Register custom post types and related systems.
		 */
		public function register_cpts() {
			require_once REALSHED_CORE_PATH . 'includes/cpt/property/class-property-cpt.php';
			require_once REALSHED_CORE_PATH . 'includes/cpt/property/class-property-meta.php';

			require_once REALSHED_CORE_PATH . 'includes/cpt/property/class-property-search-handler.php';

			if ( class_exists( 'Realshed_Property_Search_Handler' ) ) {
				new Realshed_Property_Search_Handler();
			}

			require_once REALSHED_CORE_PATH . 'includes/cpt/agent/class-agent-cpt.php';
			require_once REALSHED_CORE_PATH . 'includes/cpt/testimonial/class-testimonial-cpt.php';
			require_once REALSHED_CORE_PATH . 'includes/cpt/team/class-team-cpt.php';
		}

		/**
		 * Register widgets.
		 */
		public function register_widgets() {
			require_once REALSHED_CORE_PATH . 'includes/widgets/recent-properties/class-recent-properties-widget.php';
			require_once REALSHED_CORE_PATH . 'includes/widgets/property-search/class-property-search-widget.php';
			require_once REALSHED_CORE_PATH . 'includes/widgets/contact-info/class-contact-info-widget.php';

			register_widget( 'Realshed_Property_Search_Widget' );

			if ( class_exists( 'Recent_Properties_Widget' ) ) {
				register_widget( 'Recent_Properties_Widget' );
			}

			if ( class_exists( 'Contact_Info_Widget' ) ) {
				register_widget( 'Contact_Info_Widget' );
			}
		}

		/**
		 * Enqueue frontend assets.
		 */
		public function enqueue_frontend_assets() {
			wp_enqueue_style(
				'realshed-widgets',
				REALSHED_CORE_URL . 'assets/css/widgets.css',
				array(),
				REALSHED_CORE_VERSION
			);

			wp_enqueue_script(
				'realshed-property-search',
				REALSHED_CORE_URL . 'assets/js/property-search.js',
				array( 'jquery', 'jquery-ui-slider' ),
				REALSHED_CORE_VERSION,
				true
			);
		}

		/**
		 * Enqueue admin assets.
		 */
		public function enqueue_admin_assets() {
			wp_enqueue_style(
				'realshed-admin',
				REALSHED_CORE_URL . 'assets/css/admin.css',
				array(),
				REALSHED_CORE_VERSION
			);

			wp_enqueue_script(
				'realshed-shortcodes-admin',
				REALSHED_CORE_URL . 'assets/js/realshed-shortcodes-admin.js',
				array( 'jquery' ),
				REALSHED_CORE_VERSION,
				true
			);
		}

		/**
		 * Load Redux configuration.
		 */
		public function load_redux() {
			if ( ! $this->is_redux_framework_active() ) {
				return;
			}

			if ( file_exists( REALSHED_CORE_PATH . 'redux/config-template.php' ) ) {
				require_once REALSHED_CORE_PATH . 'redux/config-template.php';
			}
		}
	}
}
