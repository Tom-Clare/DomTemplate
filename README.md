# Bind dynamic data to reusable HTML components.

Built on top of [PHP.Gt/Dom][dom], this project provides simple view templating and dynamic data binding.

Directly manipulating the DOM in your code can lead to tightly coupling the logic and view. Binding data using custom elements and data attributes leads to highly readable, maintainable view files that are loosely coupled to the application logic.  

***

<a href="https://circleci.com/gh/PhpGt/DomTemplate" target="_blank">
	<img src="https://badge.status.php.gt/domtemplate-build.svg" alt="Build status" />
</a>
<a href="https://scrutinizer-ci.com/g/PhpGt/DomTemplate" target="_blank">
	<img src="https://badge.status.php.gt/domtemplate-quality.svg" alt="Code quality" />
</a>
<a href="https://scrutinizer-ci.com/g/PhpGt/DomTemplate" target="_blank">
	<img src="https://badge.status.php.gt/domtemplate-coverage.svg" alt="Code coverage" />
</a>
<a href="https://packagist.org/packages/PhpGt/DomTemplate" target="_blank">
	<img src="https://badge.status.php.gt/domtemplate-version.svg" alt="Current version" />
</a>
<a href="http://www.php.gt/domtemplate" target="_blank">
	<img src="https://badge.status.php.gt/domtemplate-docs.svg" alt="PHP.G/DomTemplate documentation" />
</a>

## Example usage: Bind dynamic data to a template element

Consider a page with an unordered list (`<ul>`). Within the list there should be a list item (`<li>`) for every element of an array of data.

In this example, we will simply use an array to contain the data, but the data can come from a data source such as a database.

### Source HTML (`example.html`)

```html
<!doctype html>
<h1>Shopping list</h1>

<shopping-list id="groceries"></shopping-list>

<p>The use of a custom element is more useful on more complex pages, but is shown above as an example.</p>
```

### Custom element HTML (`_component/shopping-list.html`)

```html
<ul>
	<li data-template data-bind:text="title" data-bind:data-id="id">Item name</li>
</ul>
```

### PHP used to inject the list

```php
<?php
require "vendor/autoload.php";

$html = file_get_contents("example.html");
$document = new \Gt\DomTemplate\HTMLDocument($html, "./_component");
$document->prepareTemplates();

$data = [
	["id" => 1, "title" => "Tomatoes"],
	["id" => 2, "title" => "Noodles"],
	["id" => 3, "title" => "Cheese"],
	["id" => 4, "title" => "Broccoli"],
];

$outputTo = $document->getElementById("groceries");
$outputTo->bind($data);
```

### Output:

```html
<!doctype html>
<h1>Shopping list</h1>

<ul id="shopping-list">
	<li data-id="1">Tomatoes</li>
	<li data-id="2">Noodles</li>
	<li data-id="3">Cheese</li>
	<li data-id="4">Broccoli</li>
</ul>

<p>The use of a custom element is more useful on more complex pages, but is shown above as an example.</p>
```

[dom]: https://www.php.gt/dom
