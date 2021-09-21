<?php
namespace Gt\DomTemplate;

use Gt\Dom\Element;
use Gt\Dom\Node;
use Gt\Dom\XPathResult;

class HTMLAttributeCollection {
	public function find(Element $context):XPathResult {
		return $context->ownerDocument->evaluate(
			"descendant-or-self::*[@*[starts-with(name(), 'data-bind')]]",
			$context
		);
	}

	/** @param iterable<Element|Node> $collection */
	public function clean(iterable $collection):void {
		foreach($collection as $item) {
			foreach($item->attributes as $name => $attr) {
				$attr->remove();
			}
		}
	}
}
