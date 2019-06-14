<?php
namespace Gt\DomTemplate;

use Gt\Dom\Attr;
use Gt\Dom\Element as BaseElement;
use DOMNode;
use StdClass;
use Gt\Dom\HTMLCollection as BaseHTMLCollection;

/**
 * In WebEngine, all Elements in the DOM are Bindable by default. A Bindable
 * Element is a ParentNode that can have data injected into it via this Trait's
 * bind* functions.
 */
trait Bindable {
	/**
	 * Bind a single key-value-pair within $this Element.
	 * Elements state their bindable key using the data-bind HTML attribute.
	 * There may be multiple Elements with the matching attribute, in which
	 * case they will all have their data set.
	 */
	public function bindKeyValue(
		string $key,
		string $value
	):void {
		/** @var BaseElement $element */
		$element = $this;
		if($element instanceof HTMLDocument) {
			$element = $element->documentElement;
		}

		$data = [
			$key => $value,
		];

		$this->injectDataIntoAttributeValues($element, $data);
		$this->injectDataIntoBindProperties($element, $data);

		$this->cleanBindAttributes($element);
	}

	/**
	 * Bind multiple key-value-pairs within $this Element, calling
	 * bindKeyValue for each key-value-pair in the iterable $kvp object.
	 * @see self::bindKeyValue
	 */
	public function bindData(
		iterable $kvp
	):void {
		foreach($kvp as $key => $value) {
			$this->bindKeyValue($key, $value);
		}
	}

	/**
	 * $kvpList is a nested iterable object. The outer iterable contains
	 * zero or more inner iterables. The inner iterables contain data in the
	 * form of an iterable key-value-pair array (typically an associative
	 * array or data object).
	 *
	 * For each iteration of the outer iterable object, a new clone will be
	 * made of the template element with the given name. The cloned element
	 * will have the inner iterable data bound to it before being added into
	 * the DOM in the position that it was originally extracted.
	 *
	 * TODO: Enforce the following:
	 * When $templateName is not provided, the data within $kvpList will be
	 * bound to an element that has a data-template attribute with no value.
	 * If there are multiple un-named template elements, an exception is
	 * thrown - in this case, you will need to use bindNestedList
	 *
	 * @throws TODO: Name an exception
	 * @see self::bindNestedList
	 */
	public function bindList(
		iterable $kvpList,
		string $templateName = null
	):void {
		/** @var BaseElement $element */
		$element = $this;
		if($element instanceof HTMLDocument) {
			$element = $element->documentElement;
		}

		// TODO: Do the looping here.
	}

	/**
	 * When data needs binding to a nested DOM structure, a BindIterator is
	 * necessary to link each child list with the correct template.
	 *
	 * TODO: Implement.
	 */
	public function bindNestedList(BindIterator $iterator):void {

	}

	protected function injectDataIntoBindProperties(
		Element $parent,
		$data
	):void {
		$childrenWithBindAttribute = $this->getChildrenWithBindAttribute($parent);

		foreach($childrenWithBindAttribute as $element) {
			$this->setData($element, $data);
		}

		$this->bindAttributes($parent, $data);
	}

	protected function bindAttributes(BaseElement $element, $data):void {
		foreach($element->attributes as $attr) {
			preg_match(
				"/{(.+)}/",
				$attr->value,
				$matches
			);
			if(empty($matches)) {
				continue;
			}

			list($placeholder, $dataKey) = $matches;

			if(!isset($data[$dataKey])) {
				continue;
			}

			$attr->value = str_replace(
				$placeholder,
				$data[$dataKey],
				$attr->value
			);
		}
	}

	protected function bindTemplates(
		DOMNode $element,
		$data,
		string $templateName = null
	):void {
		if($element instanceof \DOMDocumentFragment) {
			return;
		}

		$namesToMatch = [];

		if(is_null($templateName)) {
			$namesToMatch []= $element->getNodePath();

		}
		else {
			$namesToMatch []= $templateName;
		}

		/** @var HTMLDocument $rootDocument */
		$rootDocument = $this->getRootDocument();
		/** @var DocumentFragment[] $templateChildren */
		$templateChildren = $rootDocument->getNamedTemplateChildren(
			...$namesToMatch
		);

		foreach($data as $rowIndex => $row) {
			foreach($templateChildren as $childNumber => $fragment) {
				$insertInto = null;

				if($fragment->templateParentNode !== $element) {
					$insertInto = $element;
				}

				$newNode = $fragment->insertTemplate($insertInto);
				$this->injectDataIntoBindProperties($newNode, $row);
				$this->injectDataIntoAttributeValues(
					$newNode,
					$row
				);
			}
		}

		if(is_null($rowIndex)) {
			$trimmed = trim($element->innerHTML);
			if($trimmed === "") {
				$element->innerHTML = "";
			}
		}
	}

	protected function setData(BaseElement $element, $data):void {
		if(is_array($data)) {
			$data = $this->convertArrayToObject($data);
		}

		foreach($element->attributes as $attr) {
			$matches = [];
			if(!preg_match(
				"/(?:data-bind:)(.+)/",
				$attr->name,
				$matches)
			) {
				continue;
			}
			$bindProperty = $matches[1];

			if($bindProperty === "class") {
				$this->handleClassData(
					$attr,
					$element,
					$data
				);
			}
			else {
				$this->handlePropertyData(
					$attr,
					$bindProperty,
					$element,
					$data
				);
			}
		}
	}

	protected function handlePropertyData(
		Attr $attr,
		string $bindProperty,
		BaseElement $element,
		$data
	):void {
		$dataKeyMatch = $this->getKeyFromAttribute($element, $attr);
		$dataValue = $dataKeyMatch->getValue($data) ?? "";

		switch($bindProperty) {
		case "html":
		case "innerhtml":
			$element->innerHTML = $dataValue;
			break;

		case "text":
		case "innertext":
		case "textcontent":
			$element->innerText = $dataValue;
			break;

		case "value":
			$element->value = $dataValue;
			break;

		default:
			$element->setAttribute($bindProperty, $dataValue);
			break;
		}
	}

	protected function injectDataIntoAttributeValues(
		BaseElement $element,
		$data
	):void {
		if(is_array($data)) {
			$data = (object)$data;
		}

		foreach($element->xPath("//*[@*[contains(.,'{')]]")
		as $elementWithBraceInAttributeValue) {
			foreach($elementWithBraceInAttributeValue->attributes as $attr) {
				preg_match_all(
					"/{([^}]+)}/",
					$attr->value,
					$matches
				);

				if(empty($matches[0])) {
					continue;
				}

				foreach($matches[0] as $i => $match) {
					$key = $matches[1][$i];

					if(!isset($data->{$key})) {
						continue;
					}


					$value = str_replace(
						$match,
						$data->{$key},
						$attr->value
					);

					$attr->ownerElement->setAttribute($attr->name, $value);
				}
			}
		}
	}

	protected function handleClassData(
		Attr $attr,
		BaseElement $element,
		$data
	):void {
		$classList = explode(" ", $attr->value);
		$this->setClassFromData(
			$element,
			$data, ...
			$classList
		);
	}

	protected function setClassFromData(
		BaseElement $element,
		$data,
		string...$classList
	):void {
		foreach($classList as $class) {
			if(!strstr($class, ":")) {
				$class = "$class:$class";
			}

			list($keyMatch, $className) = explode(":", $class);

			if(!isset($data->{$keyMatch})) {
				continue;
			}

			if($data->{$keyMatch}) {
				$element->classList->add($className);
			}
			else {
				$element->classList->remove($className);
			}
		}
	}

	protected function getKeyFromAttribute(BaseElement $element, Attr $attr):DataKeyMatch {
		$required = true;
		$key = $attr->value;

		if($key[0] === "?") {
			$required = false;
			$key = substr($key, 1);
		}

		if($key[0] === "@") {
			$key = substr($key, 1);
			$attributeValue = $element->getAttribute($key);
			if(is_null($attributeValue)) {
				throw new BoundAttributeDoesNotExistException($attr->name);
			}

			$key = $attributeValue;
		}

		return new DataKeyMatch($key, $required);
	}

	protected function getTemplateNamesForElement(BaseElement $element):array {
		$templateNames = [];
		$nodePath = $element->getNodePath();

		foreach($this->templateFragmentMap as $key => $templateFragment) {
			if(strpos($key, $nodePath) === 0) {
				$templateNames []= $key;
			}
		}

		return $templateNames;
	}

	protected function getChildrenWithBindAttribute(BaseElement $parent):BaseHTMLCollection {
		return $parent->xPath(
			"descendant-or-self::*[@*[starts-with(name(), 'data-bind')]]"
		);
	}

	protected function cleanBindAttributes(DOMNode $element):void {
		$elementsToClean = [$element];
		$childrenWithBindAttribute = $this->getChildrenWithBindAttribute($element);
		foreach($childrenWithBindAttribute as $child) {
			$elementsToClean []= $child;
		}

		foreach($elementsToClean as $cleanMe) {
			if(!$cleanMe->attributes) {
				continue;
			}

			$attributesToRemove = [];
			foreach($cleanMe->attributes as $attr) {
				if(strpos($attr->name, "data-bind") === 0) {
					$attributesToRemove []= $attr->name;
				}
			}

			foreach($attributesToRemove as $attrName) {
				$cleanMe->removeAttribute($attrName);
			}
		}
	}

	protected function convertArrayToObject(array $array) {
		$object = new StdClass();
		foreach($array as $key => $value) {
			$object->$key = $value;
		}

		return $object;
	}
}
