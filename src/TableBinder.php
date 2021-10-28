<?php
namespace Gt\DomTemplate;

use Gt\Dom\Document;
use Gt\Dom\Element;
use Gt\Dom\HTMLElement\HTMLTableCellElement;
use Gt\Dom\HTMLElement\HTMLTableElement;
use Gt\Dom\HTMLElement\HTMLTableRowElement;
use Gt\Dom\HTMLElement\HTMLTableSectionElement;
use Stringable;

class TableBinder {
	private ?ElementBinder $elementBinder;
	private ?BindableCache $bindableCache;

	public function __construct(
		private ?TemplateCollection $templateCollection = null,
		?ElementBinder $elementBinder = null,
		?BindableCache $bindableCache = null
	) {
		$this->elementBinder = $elementBinder;
		$this->bindableCache = $bindableCache;
	}

	/**
	 * @param array<int, array<int, string>>|array<int, array<int|string, string|array<int, mixed>>> $tableData
	 * @param Element $context
	 */
	public function bindTableData(
		array $tableData,
		Document|Element $context
	):void {
		if($context instanceof Document) {
			$context = $context->documentElement;
		}

		/** @var array<HTMLTableElement> $tableArray */
		$tableArray = [$context];
		if(!$context instanceof HTMLTableElement) {
			$tableArray = [];
			foreach($context->querySelectorAll("table") as $table) {
				array_push($tableArray, $table);
			}
		}

		if(empty($tableArray)) {
			throw new TableElementNotFoundInContextException();
		}

		/** @var null|HTMLTableRowElement $theadTrElement */
		$theadTrElement = $tableArray[0]->tHead->rows[0];
		$tableData = $this->normaliseTableData(
			$tableData,
			$theadTrElement
		);

		if($theadTrElement) {
			$headerRow = [];
			foreach($theadTrElement->cells as $cell) {
				array_push(
					$headerRow,
					$cell->hasAttribute("data-table-key")
						? $cell->getAttribute("data-table-key")
						: trim($cell->textContent)
				);
			}
		}
		else {
			$headerRow = array_shift($tableData);
		}

		foreach($tableArray as $table) {
			/** @var HTMLTableElement $table */

			$tHead = $table->tHead;
			if(!$tHead) {
				$tHead = $table->createTHead();
				$theadTr = $tHead->insertRow();

				foreach($headerRow as $value) {
					$th = $theadTr->insertCell();
					$th->textContent = $value;
				}
			}

			/** @var ?HTMLTableSectionElement $tbody */
			$tbody = $table->tBodies[0] ?? null;
			if(!$tbody) {
				$tbody = $table->createTBody();
			}

			foreach($tableData as $rowData) {
				$template = null;

				try {
					$template = $this->templateCollection->get($tbody);
					$tr = $template->insertTemplate();
				}
				catch(TemplateElementNotFoundInContextException) {
					$tr = $tbody->insertRow();
				}
				/** @var int|string|null $firstKey */
				$firstKey = key($rowData);

				foreach($headerRow as $rowIndex => $header) {
					$cellTypeToCreate = "td";

					if(is_string($firstKey)) {
						if($rowIndex === 0) {
							$columnValue = $firstKey;
							$cellTypeToCreate = "th";
						}
						else {
							$columnValue = $rowData[$firstKey][$rowIndex - 1];
						}
					}
					else {
						$columnValue = $rowData[$rowIndex];
					}

					if($template) {
						$tr->cells[$rowIndex]->textContent = $columnValue;
					}
					else {
						$cellElement = $tr->ownerDocument->createElement($cellTypeToCreate);
						$cellElement->textContent = $columnValue;
						$tr->appendChild($cellElement);
					}

				}
			}
		}
	}

	/**
	 * @param iterable<int,iterable<int,string>>|iterable<string,iterable<int, string>>|iterable<string,iterable<string, iterable<int, string>>> $bindValue
	 * The three structures allowed by this method are:
	 * 1) If $bindValue has int keys and non-iterable values, the first
	 * value must represent an iterable of columnHeaders, and subsequent
	 * values must represent an iterable of columnValues.
	 * 2) If $bindValue has string keys, the keys must represent the column
	 * headers and the value must be an iterable of columnValues.
	 * 3) If columnValues has int keys, each item represents the value of
	 * a column <td> element.
	 * 4) If columnValues has a string keys, each key represents a <th> and
	 * each sub-iterable represents the remaining column values.
	 * @param ?HTMLTableRowElement $theadTrElement The first <tr> element
	 * within the first <thead> of the first <table> in the context, if any.
	 * @return array<int, array<int|string, string|Stringable>> A
	 * two-dimensional array where the outer array represents the rows, the
	 * inner array represents the columns.
	 */
	private function normaliseTableData(
		iterable $bindValue,
		?HTMLTableRowElement $theadTrElement
	):array {
		$normalised = [];

		reset($bindValue);
		$key = key($bindValue);

		if(is_int($key)) {
			foreach($bindValue as $i => $value) {
				if(!is_iterable($value)) {
					throw new IncorrectTableDataFormat("Row $i data is not iterable.");
				}
				$row = [];

				foreach($value as $j => $columnValue) {
// A string key within the inner array indicates "double header" table data.
					if(is_string($j)) {
						$doubleHeader = [$j => []];
						if(is_iterable($columnValue)) {
							foreach($columnValue as $cellValue) {
								array_push($doubleHeader[$j], $cellValue);
							}
							array_push($normalised, $doubleHeader);
						}
						else {
							$row = $value;
							break;
						}

					}
					else {
						array_push($row, $columnValue);
					}
				}
				if(!empty($row)) {
					array_push($normalised, $row);
				}
			}
		}
		else {
			array_push($normalised, array_keys($bindValue));
			$rows = [];

			foreach($bindValue as $colName => $colValueList) {
				if(!is_iterable($colValueList)) {
					throw new IncorrectTableDataFormat("Column data \"$colName\" is not iterable.");
				}

				foreach($colValueList as $i => $colValue) {
					if(!isset($rows[$i])) {
						$rows[$i] = [];
					}

					array_push($rows[$i], $colValue);
				}
			}

			array_push($normalised, ...$rows);
		}

		if(!$this->arrayContainsOnlyLists($normalised)) {
// TODO: Throw if $theadTrElement is null at this point.
			$headerFields = [];
			foreach($theadTrElement->cells as $cell) {
				array_push($headerFields, trim($cell->textContent));
			}

			$fixedNormalised = [];
			foreach($normalised as $kvp) {
				$row = [];
				foreach($kvp as $key => $value) {
					$index = array_search($key, $headerFields) ?? 0;
					$row[$index] = $value;
				}
				ksort($row);
				array_push(
					$fixedNormalised,
					$row
				);
// TODO: Throw exception if index is not found.
			}

			$normalised = $fixedNormalised;
		}

		return $normalised;
	}

	private function arrayContainsOnlyLists(array $normalised):bool {
		foreach($normalised as $array) {
			$index = 0;
			foreach($array as $key => $value) {
				if($key !== $index) {
					return false;
				}

				$index ++;
			}
		}

		return true;
	}
}
