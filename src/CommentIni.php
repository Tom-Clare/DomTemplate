<?php
namespace Gt\DomTemplate;

use Gt\Dom\Comment;
use Gt\Dom\Document;
use Gt\Dom\Element;
use Gt\Dom\NodeFilter;
use Throwable;

class CommentIni {
	/** @var array<string, array<string, string>|string>|null */
	private ?array $iniData;

	public function __construct(
		Document|Element $context
	) {
		if($context instanceof Document) {
			$context = $context->documentElement;
		}
		/** @var Element $context */

		$walker = $context->ownerDocument->createTreeWalker(
			$context,
			NodeFilter::SHOW_COMMENT
		);

		$ini = null;
		$commentNodeToRemove = null;

		/** @var Element|Comment $commentNode */
		foreach($walker as $commentNode) {
			if(!$commentNode instanceof Comment) {
				continue;
			}

			$data = trim($commentNode->data);

			try {
				$ini = parse_ini_string($data, true);
				$commentNodeToRemove = $commentNode;
			}
			catch(Throwable) {
				$ini = null;
			}
			if(!$ini) {
				break;
			}

// At this point, the ini has successfully parsed.
			$context = $commentNode;
			while($context = $context->previousSibling) {
				if(trim($context->textContent ?? "") !== "") {
					throw new CommentIniInvalidDocumentLocationException("A Comment INI must only appear as the first node of the HTML.");
				}
			}
		}

		if($commentNodeToRemove) {
			$commentNodeToRemove->parentNode->removeChild($commentNodeToRemove);
		}

		$this->iniData = $ini;
	}

	public function get(string $variable):?string {
		$parts = explode(".", $variable);

		$var = $this->iniData;
		foreach($parts as $part) {
			$var = $var[$part] ?? null;
		}

		return $var;
	}

	public function containsIniData():bool {
		return !empty($this->iniData);
	}
}
