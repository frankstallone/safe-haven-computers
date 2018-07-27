<?php

final class ITSEC_Mail {
	private $name;
	private $content = '';
	private $groups = array();
	private $current_group;
	private $deferred = '';
	private $subject = '';
	private $recipients = array();
	private $attachments = array();
	private $template_path = '';

	public function __construct( $name = '' ) {
		$this->template_path = dirname( __FILE__ ) . '/mail-templates/';
		$this->name          = $name;
	}

	public function add_header( $title, $banner_title, $use_site_logo = false ) {
		$header = $this->get_template( 'header.html' );

		if ( $use_site_logo ) {
			$logo = $this->get_site_logo_url();
		} elseif ( ITSEC_Core::is_pro() ) {
			$logo = $this->get_image_url( 'pro_logo' );
		} else {
			$logo = $this->get_image_url( 'logo' );
		}

		$replacements = array(
			'lang'         => esc_attr( get_bloginfo( 'language' ) ),
			'charset'      => esc_attr( get_bloginfo( 'charset' ) ),
			'title_tag'    => $title,
			'banner_title' => $banner_title,
			'logo'         => $logo,
			'title'        => $title,
		);

		$this->add_html( $this->replace_all( $header, $replacements ), 'header' );
	}

	public function add_footer() {
		$footer = '';

		if ( ! ITSEC_Core::is_pro() ) {
			$callout = $this->get_template( 'pro-callout.html' );

			$replacements = array(
				'two_factor' => esc_html__( 'Want two-factor authentication, scheduled malware scanning, ticketed support and more?', 'it-l10n-ithemes-security-pro' ),
				'get_pro'    => esc_html__( 'Get iThemes Security Pro', 'it-l10n-ithemes-security-pro' ),
				'why_pro'    => sprintf( wp_kses( __( 'Why go Pro? <a href="%s">Check out the Free/Pro comparison chart.</a>', 'it-l10n-ithemes-security-pro' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( 'https://ithemes.com/security/why-go-pro/' ) ),
			);

			$footer .= $this->replace_all( $callout, $replacements );
		} else {
			$this->add_divider();
		}

		$footer .= $this->get_template( 'footer.html' );

		$settings = esc_url( self::filter_admin_page_url( ITSEC_Core::get_settings_page_url() ) );

		$replacements = array(
			'security_resources'     => esc_html__( 'Security Resources', 'it-l10n-ithemes-security-pro' ),
			'articles'               => esc_html__( 'Articles', 'it-l10n-ithemes-security-pro' ),
			'articles_content'       => sprintf( wp_kses( __( 'Read the latest in WordPress Security news, tips, and updates on <a href="%s">iThemes Blog</a>.', 'it-l10n-ithemes-security-pro' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( 'https://ithemes.com/category/wordpress-security/' ) ),
			'tutorials'              => esc_html__( 'Tutorials', 'it-l10n-ithemes-security-pro' ),
			'tutorials_content'      => sprintf( wp_kses( __( 'Make the most of iThemes Security features with our <a href="%s">free iThemes Security tutorials</a>.', 'it-l10n-ithemes-security-pro' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( 'https://ithemes.com/tutorial/category/ithemes-security/' ) ),
			'help_and_support'       => esc_html__( 'Help & Support', 'it-l10n-ithemes-security-pro' ),
			'documentation'          => esc_html__( 'Documentation', 'it-l10n-ithemes-security-pro' ),
			'documentation_content'  => sprintf( wp_kses( __( 'Read iThemes Security documentation and Frequently Asked Questions on <a href="%s">the Codex</a>.', 'it-l10n-ithemes-security-pro' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( 'http://ithemes.com/codex/page/IThemes_Security' ) ),
			'support'                => esc_html__( 'Support', 'it-l10n-ithemes-security-pro' ),
			'pro'                    => esc_html__( 'Pro', 'it-l10n-ithemes-security-pro' ),
			'support_content'        => sprintf( wp_kses( __( 'Pro customers can contact <a href="%s">iThemes Helpdesk</a> for help. Our support team answers questions Monday – Friday, 8am – 5pm (CST).', 'it-l10n-ithemes-security-pro' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( 'https://members.ithemes.com/panel/helpdesk.php' ) ),
			'security_settings_link' => $settings,
			'unsubscribe_link_text'  => esc_html__( 'This email was generated by the iThemes Security plugin.', 'it-l10n-ithemes-security-pro' ) . '<br>' . sprintf( esc_html__( 'To unsubscribe from these updates, visit the %1$sSettings page%2$s in the iThemes Security plugin menu.', 'it-l10n-ithemes-security-pro' ), "<a href=\"{$settings}\" style=\"color: #0084CB\">", '</a>' ),
			'security_guide'         => esc_html__( 'Free WordPress Security Guide', 'it-l10n-ithemes-security-pro' ),
			'security_guide_content' => sprintf( wp_kses( __( 'Learn simple WordPress security tips — including 3 kinds of security your site needs and 4 best security practices for keeping your WordPress site safe with our <a href="%s">free guide</a>.', 'it-l10n-ithemes-security-pro' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( 'https://ithemes.com/publishing/wordpress-security/' ) ),

		);

		$this->add_html( $this->replace_all( $footer, $replacements ) );

		if ( defined( 'ITSEC_DEBUG' ) && ITSEC_DEBUG ) {
			$this->include_debug_info();
		}

		$this->add_html( $this->get_template( 'close.html' ), 'footer' );
	}

	public function add_user_footer() {

		$link_text = sprintf( esc_html__( 'This email was generated by the iThemes Security plugin on behalf of %s.', 'it-l10n-ithemes-security-pro' ), get_bloginfo( 'name', 'display' ) ) . '<br>';
		$link_text .= sprintf(
			esc_html__( 'To unsubscribe from these notifications, please %1$scontact the site administrator%2$s.', 'it-l10n-ithemes-security-pro' ),
			'<a href="' . esc_url( site_url() ) . '" style="color: #0084CB">', '</a>'
		);

		$footer = $this->replace_all( $this->replace_images( $this->get_template( 'footer-user.html' ) ), array(
			'unsubscribe_link_text' => $link_text,
		) );

		$footer .= $this->get_template( 'close.html' );
		$this->add_html( $footer, 'user-footer' );
	}

	public function add_text( $content ) {
		$this->add_html( $this->get_text( $content ) );
	}

	public function get_text( $content ) {
		$module = $this->get_template( 'text.html' );
		$module = $this->replace( $module, 'content', $content );

		return $module;
	}

	public function add_divider() {
		$this->add_html( $this->get_divider() );
	}

	public function get_divider() {
		return $this->get_template( 'divider.html' );
	}

	public function add_large_text( $content ) {
		$this->add_html( $this->get_large_text( $content ) );
	}

	public function get_large_text( $content ) {
		$module = $this->get_template( 'large-text.html' );
		$module = $this->replace( $module, 'content', $content );

		return $module;
	}

	public function add_info_box( $content, $icon_type = 'info' ) {
		$this->add_html( $this->get_info_box( $content, $icon_type ) );
	}

	public function get_info_box( $content, $icon_type = 'info' ) {
		$icon_url = $this->get_image_url( $icon_type === 'warning' ? 'warning_icon_yellow' : "{$icon_type}_icon" );

		$module = $this->get_template( 'info-box.html' );
		$module = $this->replace_all( $module, compact( 'content', 'icon_url' ) );

		return $module;
	}

	public function add_details_box( $content ) {
		$this->add_html( $this->get_details_box( $content ) );
	}

	public function get_details_box( $content ) {
		$module = $this->get_template( 'details-box.html' );
		$module = $this->replace( $module, 'content', $content );

		return $module;
	}

	public function add_large_code( $content ) {
		$this->add_html( $this->get_large_code( $content ) );
	}

	public function get_large_code( $content ) {
		$module = $this->get_template( 'large-code.html' );
		$module = $this->replace( $module, 'content', $content );

		return $module;
	}

	public function add_section_heading( $content, $icon_type = false ) {
		$this->add_html( $this->get_section_heading( $content, $icon_type ) );
	}

	public function get_section_heading( $content, $icon_type = false ) {
		if ( empty( $icon_type ) ) {
			$heading = $this->get_template( 'section-heading.html' );
			$heading = $this->replace_all( $heading, compact( 'content' ) );
		} else {
			$icon_url = $this->get_image_url( "icon_{$icon_type}" );

			$heading = $this->get_template( 'section-heading-with-icon.html' );
			$heading = $this->replace_all( $heading, compact( 'content', 'icon_url' ) );
		}

		return $heading;
	}

	public function add_lockouts_summary( $user_count, $host_count ) {
		$lockouts = $this->get_template( 'lockouts-summary.html' );

		$replacements = array(
			'users_text' => esc_html__( 'Users', 'it-l10n-ithemes-security-pro' ),
			'hosts_text' => esc_html__( 'Hosts', 'it-l10n-ithemes-security-pro' ),
			'user_count' => $user_count,
			'host_count' => $host_count,
		);

		$lockouts = $this->replace_all( $lockouts, $replacements );

		$this->add_html( $lockouts, 'lockouts-summary' );
	}

	public function add_file_change_summary( $added, $removed, $modified ) {
		$lockouts = $this->get_template( 'file-change-summary.html' );

		$replacements = array(
			'added_text'     => esc_html_x( 'Added', 'Files added', 'it-l10n-ithemes-security-pro' ),
			'removed_text'   => esc_html_x( 'Removed', 'Files removed', 'it-l10n-ithemes-security-pro' ),
			'modified_text'  => esc_html_x( 'Modified', 'Files modified', 'it-l10n-ithemes-security-pro' ),
			'added_count'    => $added,
			'removed_count'  => $removed,
			'modified_count' => $modified,
		);

		$lockouts = $this->replace_all( $lockouts, $replacements );

		$this->add_html( $lockouts, 'file-change-summary' );
	}

	public function add_button( $link_text, $href ) {
		$this->add_html( $this->get_button( $link_text, $href ) );
	}

	public function get_button( $link_text, $href ) {

		$module = $this->get_template( 'module-button.html' );
		$module = $this->replace( $module, 'href', $href );
		$module = $this->replace( $module, 'link_text', $link_text );

		return $module;
	}

	public function add_lockouts_table( $lockouts ) {
		$entry   = $this->get_template( 'lockouts-entry.html' );
		$entries = '';

		foreach ( $lockouts as $lockout ) {
			if ( 'user' === $lockout['type'] ) {
				/* translators: 1: Username */
				$lockout['description'] = sprintf( wp_kses( __( '<b>User:</b> %1$s', 'it-l10n-ithemes-security-pro' ), array( 'b' => array() ) ), $lockout['id'] );
			} else {
				/* translators: 1: Hostname */
				$lockout['description'] = sprintf( wp_kses( __( '<b>Host:</b> %1$s', 'it-l10n-ithemes-security-pro' ), array( 'b' => array() ) ), $lockout['id'] );
			}

			$entries .= $this->replace_all( $entry, $lockout );
		}

		$table = $this->get_template( 'lockouts-table.html' );

		$replacements = array(
			'heading_types'  => __( 'Host/User', 'it-l10n-ithemes-security-pro' ),
			'heading_until'  => __( 'Lockout in Effect Until', 'it-l10n-ithemes-security-pro' ),
			'heading_reason' => __( 'Reason', 'it-l10n-ithemes-security-pro' ),
			'entries'        => $entries,
		);

		$table = $this->replace_all( $table, $replacements );

		$this->add_html( $table, 'lockouts-table' );
	}

	/**
	 * Add a generic table.
	 *
	 * @param string[] $headers
	 * @param array[]  $entries
	 */
	public function add_table( $headers, $entries ) {
		$this->add_html( $this->get_table( $headers, $entries ) );
	}

	public function get_table( $headers, $entries ) {

		$template = $this->get_template( 'table.html' );
		$html     = $this->build_table_header( $headers );

		foreach ( $entries as $entry ) {
			$html .= $this->build_table_row( $entry, count( $headers ) );
		}

		return $this->replace( $template, 'html', $html );
	}

	/**
	 * Build the table header.
	 *
	 * @param array $headers
	 *
	 * @return string
	 */
	private function build_table_header( $headers ) {

		$html = '<tr>';

		foreach ( $headers as $header ) {
			$html .= '<th style="text-align: left;font-weight: bold;padding:5px 10px;border:1px solid #cdcece;color: #666f72;">';
			$html .= $header;
			$html .= '</th>';
		}

		$html .= '</tr>';

		return $html;
	}

	/**
	 * Build a table row.
	 *
	 * @param array|string $columns
	 * @param int          $count
	 *
	 * @return string
	 */
	private function build_table_row( $columns, $count ) {
		$html = '<tr>';

		if ( is_array( $columns ) ) {
			foreach ( $columns as $i => $column ) {
				$style = 'border:1px solid #cdcece;padding:10px;';

				if ( 0 === $i ) {
					$style .= 'font-style:italic;';
					$el    = 'th';
				} else {
					$el = 'td';
				}

				$html .= "<{$el} style=\"{$style}\">";
				$html .= $column;
				$html .= "</{$el}>";
			}
		} else {
			$html .= "<td style=\"border:1px solid #cdcece;padding:10px;\" colspan=\"{$count}\">{$columns}</td>";
		}

		$html .= '</tr>';

		return $html;
	}

	/**
	 * Add an HTML list to an email.
	 *
	 * @param string[] $items
	 * @param bool     $bold_first Whether to emphasize the first item of the list.
	 */
	public function add_list( $items, $bold_first = false ) {
		$this->add_html( $this->get_list( $items, $bold_first ) );
	}

	public function get_list( $items, $bold_first = false ) {

		$template = $this->get_template( 'list.html' );
		$html     = '';

		foreach ( $items as $i => $item ) {
			$html .= $this->build_list_item( $item, $bold_first && 0 === $i );
		}

		return $this->replace( $template, 'html', $html );
	}

	private function build_list_item( $item, $bold = false ) {
		$bold_tag = $bold ? 'font-weight: bold;' : '';

		return "<li style=\"margin: 0; padding: 5px 10px;{$bold_tag}\">{$item}</li>";
	}

	/**
	 * Add a section of HTML to the email.
	 *
	 * @param string      $html
	 * @param string|null $identifier
	 */
	public function add_html( $html, $identifier = null ) {

		if ( null !== $this->current_group ) {
			$this->deferred .= $html;
		} elseif ( null !== $identifier ) {
			$this->groups[ $identifier ] = $html;
		} else {
			$this->groups[] = $html;
		}
	}

	public function start_group( $identifier ) {
		$this->current_group = $identifier;
	}

	public function end_group() {
		$group    = $this->current_group;
		$deferred = $this->deferred;

		$this->current_group = null;
		$this->deferred      = '';

		$this->add_html( $deferred, $group );
	}

	/**
	 * Include debug info in the email.
	 *
	 * This is automatically included in non-user emails if ITSEC_DEBUG is turned on.
	 */
	public function include_debug_info() {

		if ( ( defined( 'DOING_CRON' ) && DOING_CRON ) || ( function_exists( 'wp_doing_cron' ) && wp_doing_cron() ) ) {
			$page = 'WP-Cron';
		} elseif ( defined( 'WP_CLI' ) && WP_CLI ) {
			$page = 'WP-CLI';
		} elseif ( isset( $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI'] ) ) {
			$page = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		} else {
			$page = 'unknown';
		}

		$this->add_text( sprintf( esc_html__( 'Debug info (source page): %s', 'it-l10n-ithemes-security-pro' ), esc_html( $page ) ) );
	}

	/**
	 * Get the site URL formatted for display in emails.
	 *
	 * This strips out the URL scheme, but keeps the path in case of multisite.
	 *
	 * @return string
	 */
	public function get_display_url() {

		$url    = network_site_url();
		$parsed = parse_url( $url );

		$display = $parsed['host'];

		if ( ! empty( $parsed['path'] ) ) {
			$display .= $parsed['path'];
		}

		// Escape URL will force a scheme.
		return esc_html( $display );
	}

	public function set_content( $content ) {
		$this->content = $content;
	}

	public function get_content() {

		$groups = $this->groups;

		if ( $this->name ) {
			/**
			 * Filter the HTML groups before building the content.
			 *
			 * @param array      $groups
			 * @param ITSEC_Mail $this
			 */
			$groups = apply_filters( "itsec_mail_{$this->name}", $groups, $this );
		}

		return implode( '', $groups );
	}

	public function set_subject( $subject, $add_site_url = true ) {
		if ( $add_site_url ) {
			$subject = $this->prepend_site_url_to_subject( $subject );
		}

		$this->subject = esc_html( $subject );
	}

	public function prepend_site_url_to_subject( $subject ) {
		/* translators: 1: site URL, 2: email subject */
		return sprintf( __( '[%1$s] %2$s', 'it-l10n-ithemes-security-pro' ), $this->get_display_url(), $subject );
	}

	public function set_default_subject() {
		return __( 'New Notification from iThemes Security', 'it-l10n-ithemes-security-pro' );
	}

	public function get_subject() {
		return $this->subject;
	}

	public function set_recipients( $recipients ) {
		$this->recipients = array();

		foreach ( (array) $recipients as $recipient ) {
			$recipient = trim( $recipient );

			if ( is_email( $recipient ) ) {
				$this->recipients[] = $recipient;
			}
		}
	}

	public function set_default_recipients() {
		$recipients = ITSEC_Modules::get_setting( 'global', 'notification_email' );
		$this->set_recipients( $recipients );
	}

	public function get_recipients() {
		return $this->recipients;
	}

	public function set_attachments( $attachments ) {
		$this->attachments = $attachments;
	}

	public function add_attachment( $attachment ) {
		$this->attachments[] = $attachment;
	}

	public function send() {
		if ( empty( $this->recipients ) ) {
			$this->set_default_recipients();
		}

		if ( empty( $this->subject ) ) {
			$this->set_default_subject();
		}

		$headers = array(
			'Content-Type: text/html; charset=UTF-8',
		);

		if ( $from = ITSEC_Modules::get_setting( 'notification-center', 'from_email' ) ) {
			$headers[] = "From: <{$from}>";
		}

		return wp_mail( $this->recipients, $this->get_subject(), $this->content ? $this->content : $this->get_content(), $headers, $this->attachments );
	}

	/**
	 * Get the URL to the site logo.
	 *
	 * @return string
	 */
	private function get_site_logo_url() {
		$custom_logo_id = get_theme_mod( 'custom_logo' );

		if ( ! $custom_logo_id ) {
			return '';
		}

		$image = wp_get_attachment_image_src( $custom_logo_id, array( 300, 127 ) );

		if ( ! $image || empty( $image[0] ) ) {
			return '';
		}

		return $image[0];
	}

	private function get_template( $template ) {
		return $this->replace_images( file_get_contents( $this->template_path . $template ) );
	}

	private function replace( $content, $variable, $value ) {
		return ITSEC_Lib::replace_tag( $content, $variable, $value );
	}

	private function replace_all( $content, $replacements ) {
		return ITSEC_Lib::replace_tags( $content, $replacements );
	}

	private function replace_images( $content ) {
		return preg_replace_callback( '/{! \$([a-zA-Z_][\w]*) }}/', array( $this, 'replace_image_callback' ), $content );
	}

	private function replace_image_callback( $matches ) {
		if ( empty( $matches ) || empty( $matches[1] ) ) {
			return '';
		}

		return esc_url( $this->get_image_url( $matches[1] ) );
	}

	private function get_image_url( $name ) {
		return plugin_dir_url( ITSEC_Core::get_core_dir() . 'img/mail/index.php' ) . "{$name}.png";
	}

	public static function filter_admin_page_url( $url ) {

		/**
		 * Filter admin page URLs so modules can add any necessary security tokens.
		 *
		 * @since 6.4.0
		 *
		 * @param string $url
		 */
		return apply_filters( 'itsec_notify_admin_page_url', $url );
	}
}
