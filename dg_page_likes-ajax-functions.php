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


class DgPageLikesAjax {

	public function setActions() {

		add_action( 'wp_ajax_dg_like', [ $this, 'dg_like' ] );
		add_action( 'wp_ajax_nopriv_dg_like', [ $this, 'dg_like' ] );

		add_action( 'wp_ajax_dg_get_like_counter', [ $this, 'dg_get_like_counter' ] );
		add_action( 'wp_ajax_nopriv_dg_get_like_counter', [ $this, 'dg_get_like_counter' ] );

		add_action( 'wp_ajax_dg_get_like_button', [ $this, 'dg_get_like_button' ] );
		add_action( 'wp_ajax_nopriv_dg_get_like_button', [ $this, 'dg_get_like_button' ] );
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
	 * Get a Like counter
	 */
	public function dg_get_like_counter() {
		/*
		 * Validate request
		 */
		check_ajax_referer( 'DgPageLikes', 'nonce' );

		/*
		 * Make sure there is a post id
		 */
		$post_id = esc_attr( $_POST['item_url'] );

		if ( ! is_numeric( $post_id ) ) {
			$post_id = url_to_postid( esc_url( $_POST['item_url'] ) );
		}

		/*
		 * the target div to update
		 */
		$target = ( isset( $_POST['target'] ) ) ? esc_attr( $_POST['target'] ) : 0;

		/*
		 * validate the post
		 */
		if ( ! $post_id ) {
			wp_send_json_error( __( 'Could not validate post ID', 'DgPageLikes' ) . $post_id );
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
		$post_id = esc_attr( $_POST['item_url'] );

		/*
		 * the target div to update
		 */
		$target = ( isset( $_POST['target'] ) ) ? esc_attr( $_POST['target'] ) : 0;

		/*
		 * validate the post
		 */
		if ( ! $post_id ) {
			wp_send_json_error( __( 'Could not validate post ID', 'DgPageLikes' ) . $post_id );
		};

		$html = apply_filters( 'add_page_likes_button', $post_id );


		wp_send_json_success( [
			                      'target' => $target,
			                      'html'   => $html,
		                      ] );
	}
}