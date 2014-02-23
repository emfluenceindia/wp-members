<?php
/**
 * WP-Members Dialog Functions
 *
 * Handles functions that output front-end dialogs to end users.
 * 
 * This file is part of the WP-Members plugin by Chad Butler
 * You can find out more about this plugin at http://rocketgeek.com
 * Copyright (c) 2006-2013  Chad Butler (email : plugins@butlerblog.com)
 * WP-Members(tm) is a trademark of butlerblog.com
 *
 * @package WordPress
 * @subpackage WP-Members
 * @author Chad Butler
 * @copyright 2006-2013
 */


/**
 * include the form building functions
 */
include_once( 'forms.php' );


if ( ! function_exists( 'wpmem_inc_loginfailed' ) ):
/**
 * Login Failed Dialog
 *
 * Returns the login failed error message.
 *
 * @since 1.8
 *
 * @uses apply_filters Calls 'wpmem_login_failed_args' filter to change the default values
 * @uses apply_filters Calls 'wpmem_login_failed' filter to change the failed login message
 *
 * @return string $str the generated html for the login failed message
 */
function wpmem_inc_loginfailed() 
{ 
	// defaults
	$defaults = array(
		'div_before'     => '<div align="center" id="wpmem_msg">',
		'div_after'      => '</div>', 
		'heading_before' => '<h2>',
		'heading'        => __( 'Login Failed!', 'wp-members' ),
		'heading_after'  => '</h2>',
		'p_before'       => '<p>',
		'message'        => __( 'You entered an invalid username or password.', 'wp-members' ),
		'p_after'        => '</p>',
		'link'           => '<a href="' . $_SERVER['REQUEST_URI'] . '">' . __( 'Click here to continue.', 'wp-members' ) . '</a>'
	);
	
	// filter $args
	$args = apply_filters( 'wpmem_login_failed_args', '' );
	
	// merge $args with defaults and extract
	extract( wp_parse_args( $args, $defaults ) );
	
	$str = $div_before 
		. $heading_before . $heading . $heading_after 
		. $p_before . $message . $p_after 
		. $p_before . $link . $p_after
		. $div_after;
	
	$str = apply_filters( 'wpmem_login_failed', $str );

	return $str;
}
endif;


if ( ! function_exists( 'wpmem_inc_regmessage' ) ):
/**
 * Message Dialog
 *
 * Returns various dialogs and error messages.
 *
 * @since 1.8
 *
 * @uses apply_filters Calls 'wpmem_msg_defaults' filter to filter the default tags
 * @uses apply_filters Calls 'wpmem_msg_dialog' filter to filter the message dialog
 *
 * @param  string $toggle error message toggle to look for specific error messages
 * @param  string $msg a message that has no toggle that is passed directly to the function
 * @return string $str The final HTML for the message
 */
function wpmem_inc_regmessage( $toggle, $msg = '' )
{
	// defaults
	$defaults = array(
		'div_before' => '<div class="wpmem_msg" align="center">',
		'div_after'  => '</div>', 
		'p_before'   => '<p>',
		'p_after'    => '</p>',
		'toggles'    => array( 
							'user', 
							'email', 
							'success', 
							'editsuccess', 
							'pwdchangerr', 
							'pwdchangesuccess', 
							'pwdreseterr', 
							'pwdresetsuccess' 
						)
	);
	
	// filter $args
	$args = apply_filters( 'wpmem_msg_args', '' );
	
	// merge $args with defaults and extract
	extract( wp_parse_args( $args, $defaults ) );

	// get dialogs set in the db
	$dialogs = get_option( 'wpmembers_dialogs' );

	for( $r = 0; $r < count( $toggles ); $r++ ) {
		if( $toggle == $toggles[$r] ) {
			$msg = __( stripslashes( $dialogs[$r+1] ), 'wp-members' );
			break;
		}
	}

	$str = $div_before . $p_before . stripslashes( $msg ) . $p_after . $div_after;

	return apply_filters( 'wpmem_msg_dialog', $str );

}
endif;


if( ! function_exists( 'wpmem_inc_memberlinks' ) ):
/**
 * Member Links Dialog
 *
 * Outputs the links used on the members area.
 *
 * @since 2.0
 *
 * @uses apply_filters Calls 'wpmem_logout_link' filter to change the logout link
 * @uses apply_filters Calls 'wpmem_member_links' filter to change the links shown on the logged in state of the user profile page (user-profile/members-area shortcode page)
 * @uses apply_filters Calls 'wpmem_register_links' filter to change the links shown on the logged in state of the registration form (shortcode page)
 * @uses apply_filters Calls 'wpmem_login_links' filter to change the links shown on the logged in state of the login form (shortcode page)
 *
 * @param  string $page
 * @return string $str
 */
function wpmem_inc_memberlinks( $page = 'members' ) 
{
	global $user_login; 
	
	$link = wpmem_chk_qstr();
	
	$logout = apply_filters( 'wpmem_logout_link', $link . 'a=logout' );
	
	switch( $page ) {
	
	case 'members':
		$str  = '<ul><li><a href="'  .$link . 'a=edit">' . __( 'Edit My Information', 'wp-members' ) . '</a></li>
				<li><a href="' . $link . 'a=pwdchange">' . __( 'Change Password', 'wp-members' ) . '</a></li>';
		if( WPMEM_USE_EXP == 1 && function_exists( 'wpmem_user_page_detail' ) ) { $str .= wpmem_user_page_detail(); }
		$str.= '</ul>';
		$str = apply_filters( 'wpmem_member_links', $str );
		break;
		
	case 'register':	
		$str = '<p>' . sprintf( __( 'You are logged in as %s', 'wp-members' ), $user_login ) . '</p>
			<ul>
				<li><a href="' . $logout . '">' . __( 'Click here to logout.', 'wp-members' ) . '</a></li>
				<li><a href="' . get_option('home') . '">' . __( 'Begin using the site.', 'wp-members' ) . '</a></li>
			</ul>';
		$str = apply_filters( 'wpmem_register_links', $str );
		break;	
	
	case 'login':

		$str = '<p>
		  	' . sprintf( __( 'You are logged in as %s', 'wp-members' ), $user_login ) . '<br />
		  	<a href="' . $logout . '">' . __( 'click here to logout', 'wp-members' ) . '</a>
			</p>';
		$str = apply_filters( 'wpmem_login_links', $str );
		break;	
			
	case 'status':
		$str ='<p>
			' . sprintf( __( 'You are logged in as %s', 'wp-members' ), $user_login ) . '  | 
			<a href="' . $logout . '">' . __( 'click here to logout', 'wp-members' ) . '</a>
			</p>';
		break;
	
	}
	
	return $str;
}
endif;


if ( ! function_exists( 'wpmem_page_pwd_reset' ) ):
/**
 * Password reset forms
 *
 * This function creates both password reset and forgotten
 * password forms for page=password shortcode.
 *
 * @since 2.7.6
 *
 * @param  string $wpmem_regchk
 * @param  string $content
 * @return string $content
 */
function wpmem_page_pwd_reset( $wpmem_regchk, $content )
{
	if( is_user_logged_in() ) {
	
		switch( $wpmem_regchk ) { 
				
		case "pwdchangempty":
			$content = wpmem_inc_regmessage( $wpmem_regchk, __( 'Password fields cannot be empty', 'wp-members' ) );
			$content = $content . wpmem_inc_changepassword();
			break;

		case "pwdchangerr":
			$content = wpmem_inc_regmessage( $wpmem_regchk );
			$content = $content . wpmem_inc_changepassword();
			break;

		case "pwdchangesuccess":
			$content = $content . wpmem_inc_regmessage( $wpmem_regchk );
			break;

		default:
			$content = $content . wpmem_inc_changepassword();
			break;				
		}
	
	} else {
	
		switch( $wpmem_regchk ) {

		case "pwdreseterr":
			$content = $content 
				. wpmem_inc_regmessage( $wpmem_regchk )
				. wpmem_inc_resetpassword();
			$wpmem_regchk = ''; // clear regchk
			break;

		case "pwdresetsuccess":
			$content = $content . wpmem_inc_regmessage( $wpmem_regchk );
			$wpmem_regchk = ''; // clear regchk
			break;

		default:
			$content = $content . wpmem_inc_resetpassword();
			break;
		}
		
	}
	
	return $content;

}
endif;


if ( ! function_exists( 'wpmem_page_user_edit' ) ):
/**
 * Creates a user edit page
 *
 * @since 2.7.6
 *
 * @uses apply_filters Calls 'wpmem_user_edit_heading' filter to change the user profile edit heading for the user edit shortcode page
 *
 * @param  string $wpmem_regchk
 * @param  string $content
 * @return string $content
 */
function wpmem_page_user_edit( $wpmem_regchk, $content )
{
	global $wpmem_a, $wpmem_themsg;
	
	$heading = apply_filters( 'wpmem_user_edit_heading', __( 'Edit Your Information', 'wp-members' ) );
	
	if( $wpmem_a == "update") { $content.= wpmem_inc_regmessage( $wpmem_regchk, $wpmem_themsg ); }
	$content = $content . wpmem_inc_registration( 'edit', $heading );
	
	return $content;
}
endif;

/** End of File **/