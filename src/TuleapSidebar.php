<?php

namespace TuleapSkin;

use TuleapIntegration\TuleapConnection;

class TuleapSidebar {

	/**
	 *
	 * @var TuleapConnection
	 */
	private $connection = null;

	/**
	 *
	 * @var int
	 */
	private $id = 0;

	/**
	 *
	 * @param TuleapConnection $connection
	 * @param int $id
	 */
	public function __construct( $connection, $id ) {
		$this->connection = $connection;
		$this->id = $id;
	}

	/**
	 *
	 * @return string
	 */
	public function getStyles() {
		$styles = $this->connection->getIntegrationData( $this->id, 'styles' );
		if ( !is_array( $styles ) ) {
			return '';
		}
		if ( !isset( $styles[ 'content' ] ) || !is_string( $styles[ 'content' ] ) ) {
			return '';
		}
		return $styles[ 'content' ];
	}

	/**
	 *
	 * @return string
	 */
	public function getConfiguration() {
		$config = $this->connection->getIntegrationData( $this->id, 'project_sidebar' );
		if ( !is_array( $config ) ) {
			return '';
		}
		if ( !isset( $config[ 'config' ] ) || !is_string( $config[ 'config' ] ) ) {
			return '';
		}
		return $config[ 'config' ];
	}

	/**
	 *
	 * @return bool
	 */
	public function isCollapsed() {
		$config = $this->connection->getIntegrationData( $this->id, 'project_sidebar' );
		if ( !is_array( $config ) ) {
			return false;
		}
		if ( !isset( $config[ 'is_collapsed' ] ) || !( $config[ 'config' ] ) ) {
			return false;
		}
		return $config[ 'is_collapsed' ];
	}

	/**
	 *
	 * @return string
	 */
	public function getTheme() {
		$theme = $this->connection->getIntegrationData( $this->id, 'styles' );
		if ( !is_array( $theme ) ) {
			return 'orange';
		}
		if ( !isset( $theme[ 'variant_name' ] ) || !is_string( $theme[ 'variant_name' ] ) ) {
			return 'orange';
		}
		return $theme[ 'variant_name' ];
	}

	public function shouldUseThemeFavicon(): bool {
		$styles = $this->connection->getIntegrationData( $this->id, 'styles' );
		if ( !is_array( $styles ) ) {
			return false;
		}
		return $styles[ 'should_display_favicon_variant' ] ?? false;
	}
}
