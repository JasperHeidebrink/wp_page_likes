<?php
/**
 * @package DG Page likes
 * @version 1.0
 *
 * Required by dg_page_likes.php
 * This document contains all the front-end functions for the dragonet page like functionality
 */


if ( preg_match( '#' . basename( __FILE__ ) . '#', $_SERVER['PHP_SELF'] ) ) {
	die( 'You are not allowed to call this page directly.' );
}


class DgPageLikes {

	public function setActions() {
		add_action( 'wp_enqueue_scripts', [ $this, 'action__enqueue_scripts' ] );

		add_action( 'wp_ajax_dg_like', [ $this, 'dg_like' ] );
		add_action( 'wp_ajax_nopriv_dg_like', [ $this, 'dg_like' ] );
		add_action( 'wp_ajax_dg_get_like_button', [ $this, 'dg_get_like_button' ] );
		add_action( 'wp_ajax_nopriv_dg_get_like_button', [ $this, 'dg_get_like_button' ] );

		add_filter( 'get_page_likes_count', [ $this, 'get_page_likes_count' ], 10, 1 );
		add_filter( 'add_page_likes_button', [ $this, 'filter__add_page_likes_button' ], 10, 1 );
	}

	/**
	 * Enqueue the scripts for the ajax calls
	 */
	public function action__enqueue_scripts() {

		wp_register_script( 'DgPageLikes',
		                    plugin_dir_url( __FILE__ ) . '/assets/js/page_likes.js',
		                    array( 'jquery' ),
		                    '1.0.1',
		                    true );

		$i18n = array(
			'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
			'ajaxNonce' => wp_create_nonce( 'DgPageLikes' ),
		);
		wp_localize_script( 'DgPageLikes', 'DgPageLikes', $i18n );
		wp_enqueue_script( 'DgPageLikes' );


		wp_register_style( 'DgPageLikes-styles', plugin_dir_url( __FILE__ ) . 'assets/css/page_likes.css' );
		wp_enqueue_style( 'DgPageLikes-styles' );

	}

	/**
	 * Store the like
	 */
	public function dg_like() {
		/*
		 * Validate request
		 */
		check_ajax_referer( 'DgPageLikes', 'nonce' );

		/*
		 * Make sure there is a post id
		 */
		if ( ! isset( $_POST['post_id'] ) ) {
			wp_send_json_error( __( 'Could not load data', 'DgPageLikes' ) );
		}
		$post_id = esc_attr( $_POST['post_id'] );

		/*
		 * the target div to update
		 */
		$target = ( isset( $_POST['target'] ) ) ? esc_attr( $_POST['target'] ) : 0;

		/*
		 * validate the post
		 */
		if ( ! get_permalink( $post_id ) ) {
			wp_send_json_error( __( 'Could not validate post ID', 'DgPageLikes' ) );
		};

		/*
		 * Load the number of votes
		 */
		$votes = get_post_meta( $post_id, 'dg_votes', true );
		if ( ! is_array( $votes ) ) {
			$votes = [];
		}
		# Remove empty
		array_filter( $votes );

		/*
		 * Get the current user
		 * Guest user is unique based on the ip and browser
		 */
		$user_id = get_current_user_id();
		if ( 0 == $user_id ) {
			$user_id = md5( $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] );
		}

		/*
		 * Check if the current user is unique
		 */
		if ( in_array( $user_id, $votes ) ) {
			wp_send_json_error( [
				                    __( 'User already voted on this Post', 'DgPageLikes' ),
				                    'target' => $target,
				                    'votes'  => count( $votes ),
			                    ] );
		}

		/*
		 * Store the vote
		 */
		$votes[] = $user_id;
		update_post_meta( $post_id, 'dg_votes', $votes );


		wp_send_json_success( [
			                      'message' => __( 'Your vote has been processed', 'DgPageLikes' ),
			                      'target'  => $target,
			                      'votes'   => count( $votes ),
		                      ] );
	}

	/**
	 * Get a vote button
	 */
	public function dg_get_like_button() {
		/*
		 * Validate request
		 */
		check_ajax_referer( 'DgPageLikes', 'nonce' );

		/*
		 * Make sure there is a post id
		 */
		$post_id = url_to_postid( esc_url( $_POST['item_url'] ) );

		/*
		 * the target div to update
		 */
		$target = ( isset( $_POST['target'] ) ) ? esc_attr( $_POST['target'] ) : 0;

		/*
		 * validate the post
		 */
		if ( ! $post_id ) {
			wp_send_json_error( __( 'Could not validate post ID', 'DgPageLikes' ) );
		};

		/*
		 * Load the number of votes
		 * and remove empty
		 */
		$votes = get_post_meta( $post_id, 'dg_votes', true );
		if ( ! is_array( $votes ) ) {
			$votes = [];
		}
		array_filter( $votes );

		$html = '<div class="gallery_overview_like"><i class="fa fa-thumbs-up" aria-hidden="true"></i> ' . count( $votes ) . '</div>';


		wp_send_json_success( [
			                      'target' => $target,
			                      'html'   => $html,
		                      ] );
	}

	/**
	 * Get the number of likes
	 *
	 * @param $post_id
	 *
	 * @return int
	 */
	public function get_page_likes_count( $post_id ) {

		/*
		 * validate the post
		 */
		if ( ! get_permalink( $post_id ) ) {
			return 0;
		};

		/*
		 * Load the number of votes
		 */
		$votes = get_post_meta( $post_id, 'dg_votes', true );
		if ( ! is_array( $votes ) ) {
			return 0;
		}
		# Remove empty
		array_filter( $votes );

		return count( $votes );
	}

	/**
	 * Generate the page like button
	 *
	 * @param $object_id
	 *
	 * @return string
	 */
	public function filter__add_page_likes_button( $object_id ) {

		$html = '<div class="page_like_button" data-page="' . $object_id . '" id="page_like_button_' . $object_id . '">' .
		        '<i class="fa fa-thumbs-up" aria-hidden="true"></i> ' .
		        '<div class="page_like_counter">' . $this->get_page_likes_count( $object_id ) . '</div>' .
		        '</div>';

		return $html;
	}
}