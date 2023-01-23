<?php

namespace TuleapSkin;

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
		if ( is_array( $styles ) ) {
			return $styles[ 'content' ];
		}
		return '';
	}

	/**
	 *
	 * @return string
	 */
	public function getConfiguration() {
		$config = $this->connection->getIntegrationData( $this->id, 'project_sidebar' );
		if ( is_array( $config ) ) {
			return $config[ 'config' ];
		}
		return '';
	}

	/**
	 *
	 * @return bool
	 */
	public function isCollapsed() {
		$config = $this->connection->getIntegrationData( $this->id, 'project_sidebar' );
		if ( is_array( $config ) ) {
			return $config[ 'is_collapsed' ];
		}
		return false;
	}

	/**
	 *
	 * @return string
	 */
	public function getTheme() {
		$theme = $this->connection->getIntegrationData( $this->id, 'styles' );
		if ( is_array( $theme ) ) {
			return $theme[ 'variant_name' ];
		}
		return 'orange';
	}
}
