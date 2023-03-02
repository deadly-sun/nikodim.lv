<?php
namespace Bricks\Integrations\Form\Actions;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Login extends Base {

	/**
	 * User login
	 *
	 * @since 1.0
	 */
	public function run( $form ) {

		$form_settings = $form->get_settings();
		$form_fields   = $form->get_fields();

		$user_login    = isset( $form_fields[ "form-field-{$form_settings['loginName']}" ] ) ? $form_fields[ "form-field-{$form_settings['loginName']}" ] : false;
		$user_password = isset( $form_fields[ "form-field-{$form_settings['loginPassword']}" ] ) ? $form_fields[ "form-field-{$form_settings['loginPassword']}" ] : false;

		// Login response: WP_User on success, WP_Error on failure
		$login_response = wp_signon(
			[
				'user_login'    => $user_login,
				'user_password' => $user_password,
				'remember'      => false,
			]
		);

		if ( is_wp_error( $login_response ) ) {
			// Login error
			$form->set_result(
				[
					'action'  => $this->name,
					'type'    => 'danger',
					'message' => $login_response->get_error_message(),
				]
			);
			return;
		}

		$form->set_result(
			[
				'action'         => $this->name,
				'type'           => 'success',
				'message'        => 'OK',
				'login_response' => $login_response,
			]
		);

	}

}
