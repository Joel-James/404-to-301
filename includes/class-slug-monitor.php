<?php
/**
 * Auto-redirect on slug change.
 *
 * When the "Monitor slug changes" setting is on, a published post whose
 * permalink changes (typically because its slug was edited) leaves its
 * old URL dangling — every existing link, bookmark and search-engine
 * result now points at a 404. This service watches post updates and
 * creates a 301 from the old URL to the new one so those links keep
 * working.
 *
 * Gated to public, published post types: drafts have no live URL to
 * preserve, and non-public types aren't reachable by visitors.
 *
 * @package DuckDev\FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour;

use WP_Post;
use DuckDev\FourNotFour\Models\Redirects;
use DuckDev\FourNotFour\Utils\Singleton;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

/**
 * Class Slug_Monitor
 *
 * @since   4.0.0
 * @package DuckDev\FourNotFour
 */
class Slug_Monitor extends Singleton {

	/**
	 * Register hooks.
	 *
	 * `post_updated` fires after the row is written and hands us both the
	 * pre- and post-update objects, which is exactly what we need to
	 * compare the old and new permalinks. It fires for admin edits, REST
	 * (block editor) saves and WP-CLI alike, so the feature isn't tied to
	 * the classic edit screen.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	protected function init(): void {
		add_action( 'post_updated', array( $this, 'maybe_create_redirect' ), 10, 3 );
	}

	/**
	 * Create a redirect from the old URL to the new one when a published
	 * post's permalink changes.
	 *
	 * @since 4.0.0
	 *
	 * @param int     $post_id     Updated post id.
	 * @param WP_Post $post_after  Post object after the update.
	 * @param WP_Post $post_before Post object before the update.
	 *
	 * @return void
	 */
	public function maybe_create_redirect( int $post_id, WP_Post $post_after, WP_Post $post_before ): void {
		unset( $post_id );

		// Feature gate.
		$settings = Core::instance()->settings();
		if ( ! $settings || ! $settings->get( 'monitor_post_slug', false ) ) {
			return;
		}

		// Ignore autosaves and revisions — neither is a real slug change.
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_after ) ) {
			return;
		}

		// Only posts that were live before and remain live after: a draft
		// has no public URL to preserve, and redirecting to an unpublished
		// target would send visitors to a 404 of a different kind.
		if ( 'publish' !== $post_before->post_status || 'publish' !== $post_after->post_status ) {
			return;
		}

		// Public, redirectable post types only.
		$type = get_post_type_object( $post_after->post_type );
		if ( null === $type || empty( $type->public ) ) {
			return;
		}

		$old_url = get_permalink( $post_before );
		$new_url = get_permalink( $post_after );

		// Nothing to do when the permalink is unchanged (eg. a plain
		// `?p=123` permalink structure, or an edit that didn't touch the
		// slug) or either lookup failed.
		if ( ! is_string( $old_url ) || ! is_string( $new_url ) || '' === $old_url || $old_url === $new_url ) {
			return;
		}

		/**
		 * Filter whether to auto-create a redirect for this slug change.
		 *
		 * Return false to skip a specific post — eg. to exclude a custom
		 * post type or a bulk slug-normalisation run.
		 *
		 * @since 4.0.0
		 *
		 * @param bool    $create      Whether to create the redirect.
		 * @param WP_Post $post_after  Post after the update.
		 * @param WP_Post $post_before Post before the update.
		 */
		if ( ! apply_filters( '404_to_301_monitor_slug_create', true, $post_after, $post_before ) ) {
			return;
		}

		$this->upsert_redirect( $old_url, $new_url );
	}

	/**
	 * Create — or refresh — the redirect for an old → new URL pair.
	 *
	 * Stores the source as the old URL's path so it matches the request
	 * URI regardless of host. When a redirect already exists for the old
	 * URL (eg. the post was renamed twice) its target is updated rather
	 * than inserting a duplicate, which the unique `source_hash` index
	 * would reject anyway.
	 *
	 * @since 4.0.0
	 *
	 * @param string $old_url Previous permalink (full URL).
	 * @param string $new_url New permalink (full URL).
	 *
	 * @return void
	 */
	private function upsert_redirect( string $old_url, string $new_url ): void {
		$source = (string) wp_parse_url( $old_url, PHP_URL_PATH );
		if ( '' === $source ) {
			return;
		}

		$model    = Redirects::instance();
		$existing = $model->find_exact( $source );

		$data = array(
			'target_type'   => 'link',
			'target_url'    => $new_url,
			'redirect_type' => 301,
			'match_type'    => 'exact',
			'is_active'     => 1,
		);

		if ( $existing && isset( $existing->id ) ) {
			// Don't clobber a redirect an admin built by hand — only
			// refresh rows we previously auto-created.
			if ( 'slug-monitor' !== (string) $existing->notes ) {
				return;
			}
			$model->update( (int) $existing->id, $data );
			return;
		}

		$data['source'] = $source;
		$data['notes']  = 'slug-monitor';
		$model->create( $data );

		// Flatten any chain we'd otherwise create. If the post was renamed
		// A -> B before and is now B -> C, the A -> B row should be
		// repointed to C so visitors (and search engines) follow a single
		// 301 instead of a hop chain. Only our own auto-created rows are
		// touched.
		$this->repoint_chains( $old_url, $new_url );
	}

	/**
	 * Repoint previously auto-created redirects that targeted the old URL
	 * so they point at the new one, collapsing 301 chains into single
	 * hops.
	 *
	 * @since 4.0.0
	 *
	 * @param string $old_url Previous permalink (now itself redirected).
	 * @param string $new_url New permalink.
	 *
	 * @return void
	 */
	private function repoint_chains( string $old_url, string $new_url ): void {
		global $wpdb;

		$table = $wpdb->prefix . '404_to_301_redirects';

		// Read-only lookup of our own auto-created rows that still target
		// the now-redirected old URL. There's no model "find by target"
		// helper, hence the direct prepared SELECT; the writes below go
		// back through the model so its cache flush + audit event fire.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$ids = $wpdb->get_col(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name is internal, values are prepared.
				"SELECT id FROM {$table} WHERE target_url = %s AND notes = %s",
				$old_url,
				'slug-monitor'
			)
		);

		if ( empty( $ids ) ) {
			return;
		}

		$model = Redirects::instance();
		foreach ( $ids as $id ) {
			$model->update( (int) $id, array( 'target_url' => $new_url ) );
		}
	}
}
