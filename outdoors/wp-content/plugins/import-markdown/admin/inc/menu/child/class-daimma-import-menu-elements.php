<?php
/**
 * Class used to implement the back-end functionalities of the "Tools" menu.
 *
 * @package import-markdown
 */

/**
 * Class used to implement the back-end functionalities of the "Import" menu.
 */
class Daimma_Import_Menu_Elements extends Daimma_Menu_Elements {

	/**
	 * Constructor.
	 *
	 * @param object $shared The shared class.
	 * @param string $page_query_param The page query parameter.
	 * @param string $config The config parameter.
	 */
	public function __construct( $shared, $page_query_param, $config ) {

		parent::__construct( $shared, $page_query_param, $config );

		$this->menu_slug      = 'import';
		$this->slug_plural    = 'import';
		$this->label_singular = __( 'Import', 'import-markdown' );
		$this->label_plural   = __( 'Import', 'import-markdown' );
	}

	/**
	 * Process the add/edit form submission of the menu. Specifically the following tasks are performed:
	 *
	 *  1. Sanitization
	 *  2. Validation
	 *  3. Database update
	 *
	 * @return false|void
	 */
	public function process_form() {

		// Process the Markdown file upload (import) ------------------------------------------------------------------.
		if ( isset( $_FILES['file_to_upload'] ) ) {

			// Nonce verification.
			check_admin_referer( 'daimma_import', 'daimma_import_nonce' );

			//phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.InputNotValidated -- The sanitization is performed with sanitize_uploaded_file().
			$file_data = $this->shared->sanitize_uploaded_file( $_FILES['file_to_upload'] );

			// Get the markdown content of the file.
			$file_content = $this->shared->get_markdown_file_content( $file_data );

			// Get the title from the file name.
			$title = sanitize_text_field( $this->shared->get_title_from_file_name( $file_data['name'] ) );

			// Get the HTML content from the markdown content.
			$html_content = $this->shared->get_html_from_markdown( $file_content );

			// Create a new post with the provided title and content.
			$result = $this->shared->create_new_post( $title, $html_content );

			$this->shared->save_log( $result, $file_data['name'] );

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

			<div class="daimma-import-menu">

				<div class="daimma-main-form">

					<div class="daimma-main-form__wrapper-half">

						<!-- Import form -->

						<div class="daimma-main-form__daext-form-section">

							<div class="daimma-main-form__section-header">
								<div class="daimma-main-form__section-header-title">
									<?php $this->shared->echo_icon_svg( 'upload-01' ); ?>
									<div class="daimma-main-form__section-header-title-text"><?php esc_html_e( 'Upload', 'import-markdown' ); ?></div>
								</div>
							</div>

							<form enctype="multipart/form-data" id="import-upload-form" method="post"
									class="wp-upload-form daimma-import-menu__import-upload-form"
									action="admin.php?page=daimma-<?php echo esc_attr( $this->slug_plural ); ?>">

								<div class="daimma-main-form__daext-form-section-body">

									<?php
									wp_nonce_field( 'daimma_import', 'daimma_import_nonce' );
									?>

									<div class="daextulma-main-form__daext-form-field">

										<?php
										esc_html_e(
											'Choose one Markdown file (.md .markdown .mdown .mkdn .mkd .mdwn .mdtxt .mdtext .text .txt) to upload, then click Upload files and import.',
											'import-markdown'
										);
										?>

									</div>

									<div class="daimma-main-form__daext-form-field">
										<div class="daimma-input-wrapper">
											<label for="upload"
													class="custom-file-upload"><?php esc_html_e( 'Choose file', 'import-markdown' ); ?></label>
											<div class="custom-file-upload-text"
												id="upload-text"><?php esc_html_e( 'No file chosen', 'import-markdown' ); ?></div>
											<input type="file" id="upload" name="file_to_upload"
													accept=".md,.markdown,.mdown,.mkdn,.mkd,.mdwn,.mdtxt,.mdtext,.text,.txt"
													class="custom-file-upload-input">
										</div>
									</div>

									<div>
										<input type="submit" name="submit" id="submit"
												class="daimma-btn daimma-btn-primary"
												value="<?php esc_attr_e( 'Upload files and import', 'import-markdown' ); ?>">
									</div>
								</div>

							</form>

						</div>

					</div>

				</div>

			</div>

		</div>

		<?php
	}
}
