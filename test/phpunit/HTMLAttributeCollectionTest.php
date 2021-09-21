<?php
namespace Gt\DomTemplate\Test;

use Gt\Dom\XPathResult;
use Gt\DomTemplate\HTMLAttributeCollection;
use PHPUnit\Framework\TestCase;

class HTMLAttributeCollectionTest extends TestCase {
	public function testClean():void {
		$xpathResult = self::createMock(XPathResult::class);
		$sut = new HTMLAttributeCollection();
		$sut->clean($xpathResult);
	}
}
