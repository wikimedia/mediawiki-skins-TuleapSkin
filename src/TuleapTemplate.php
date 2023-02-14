<?php

namespace TuleapSkin;

use BaseTemplate;
use Config;
use TemplateParser;

class TuleapTemplate extends BaseTemplate {

	/** @var TemplateParser */
	private $templateParser;

	/**
	 * @param Config $config
	 * @param TemplateParser $templateParser
	 */
	public function __construct( Config $config, TemplateParser $templateParser ) {
		parent::__construct( $config );

		$this->templateParser = $templateParser;
	}

	public function execute() {
		echo $this->templateParser->processTemplate(
			'skin',
			$this->getSkinData()
		);
	}

	/**
	 *
	 * @return array
	 */
	private function getSkinData(): array {
		return $this->getSkin()->getTemplateData();
	}
}
