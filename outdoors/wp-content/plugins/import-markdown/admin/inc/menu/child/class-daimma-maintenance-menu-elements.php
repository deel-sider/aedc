<?php
/**
 * Class used to implement the back-end functionalities of the "Maintenance" menu.
 *
 * @package import-markdown
 */

/**
 * Class used to implement the back-end functionalities of the "Maintenance" menu.
 */
class Daimma_Maintenance_Menu_Elements extends Daimma_Menu_Elements {

	/**
	 * Constructor.
	 *
	 * @param object $shared The shared class.
	 * @param string $page_query_param The page query parameter.
	 * @param string $config The config parameter.
	 */
	public function __construct( $shared, $page_query_param, $config ) {

		parent::__construct( $shared, $page_query_param, $config );

		$this->menu_slug      = 'maintenance';
		$this->slug_plural    = 'maintenance';
		$this->label_singular = __( 'Maintenance', 'import-markdown' );
		$this->label_plural   = __( 'Maintenance', 'import-markdown' );
	}

	/**
	 * Process the add/edit form submission of the menu. Specifically the following tasks are performed:
	 *
	 * 1. Sanitization
	 * 2. Validation
	 * 3. Database update
	 *
	 * @return void
	 */
	public function process_form() {

		// Preliminary operations ---------------------------------------------------------------------------------------------.
		global $wpdb;

		if ( isset( $_POST['form_submitted'] ) ) {

			// Nonce verification.
			check_admin_referer( 'daimma_execute_task', 'daimma_execute_task_nonce' );

			// Sanitization ---------------------------------------------------------------------------------------------------.
			$data['task'] = isset( $_POST['task'] ) ? intval( $_POST['task'], 10 ) : null;

			// Validation -----------------------------------------------------------------------------------------------------.

			$invalid_data_message = '';
			$invalid_data         = false;

			if ( false === $invalid_data ) {

				switch ( $data['task'] ) {

					// Delete log data.
					case 0:
						// Delete data in the 'log' table.
						global $wpdb;

						// phpcs:ignore WordPress.DB.DirectDatabaseQuery
						$query_result_log = $wpdb->query( "DELETE FROM {$wpdb->prefix}daimma_log" );

						if ( false !== $query_result_log ) {

							if ( $query_result_log > 0 ) {

								$this->shared->save_dismissible_notice(
									array(
										'elements' => array(
											array(
												'type' => 'text',
												'text' => intval(
													$query_result_log,
													10
												) . ' ' . __( 'records have been successfully deleted.', 'import-markdown' ),
											),
										),
										'class'    => 'updated',
									)
								);

							} else {

								$this->shared->save_dismissible_notice(
									array(
										'elements' => array(
											array(
												'type' => 'text',
												'text' => __( 'There are no log data.', 'import-markdown' ),
											),
										),
										'class'    => 'error',
									)
								);

							}
						}

						break;

					// Reset plugin options.
					case 1:
						// Set the default values of the options.
						$this->shared->reset_plugin_options();

						$this->shared->save_dismissible_notice(
							array(
								'elements' => array(
									array(
										'type' => 'text',
										'text' => __( 'The plugin options have been successfully set to their default values.', 'import-markdown' ),
									),
								),
								'class'    => 'updated',
							)
						);

						break;

				}
			}
		}
	}

	/**
	 * Display the form.
	 *
	 * @return void
	 */
	public function display_custom_content() {

		?>

		<div class="daimma-admin-body">

			<?php

			// Display the dismissible notices.
			$this->shared->display_dismissible_notices();

			?>

			<div class="daimma-main-form">

				<form id="form-maintenance" method="POST"
						action="admin.php?page=<?php echo esc_attr( $this->shared->get( 'slug' ) ); ?>-maintenance"
						autocomplete="off">

				<div class="daimma-main-form__daext-form-section">

					<div class="daimma-main-form__daext-form-section-body">

							<input type="hidden" value="1" name="form_submitted">

							<?php wp_nonce_field( 'daimma_execute_task', 'daimma_execute_task_nonce' ); ?>

							<?php

							// Task.
							$this->select_field(
								'task',
								'Task',
								__( 'The task that should be performed.', 'import-markdown' ),
								array(
									'0' => __( 'Delete Log Data', 'import-markdown' ),
									'1' => __( 'Reset Plugin Options', 'import-markdown' ),
								),
								null,
								'main'
							);

							?>

							<!-- submit button -->
							<div class="daext-form-action">
								<input id="execute-task" class="daimma-btn daimma-btn-primary" type="submit"
										value="<?php esc_attr_e( 'Execute Task', 'import-markdown' ); ?>">
							</div>

						</div>

					</div>

				</form>

			</div>

		</div>

		<!-- Dialog Confirm -->
		<div id="dialog-confirm" title="<?php esc_attr_e( 'Maintenance Task', 'import-markdown' ); ?>" class="daext-display-none">
			<p><?php esc_html_e( 'Do you really want to proceed?', 'import-markdown' ); ?></p>
		</div>

		<?php
	}
}
