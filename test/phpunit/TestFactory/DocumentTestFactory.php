<?php
namespace Gt\DomTemplate\Test\TestFactory;

use Gt\Dom\HTMLDocument;
use Gt\Dom\XMLDocument;

class DocumentTestFactory {
	const HTML_EMPTY = <<<HTML
<!doctype html>
HTML;

	const HTML_NO_BIND_PROPERTY = <<<HTML
<!doctype html>
<output data-bind>Nothing is bound</output>
HTML;

	const HTML_SINGLE_ELEMENT = <<<HTML
<!doctype html>
<output data-bind:text>Nothing is bound</output>
HTML;

	const HTML_SYNONYMOUS_BIND_PROPERTIES = <<<HTML
<!doctype html>
<output id="o1" data-bind:text>Nothing is bound</output>
<output id="o2" data-bind:textContent>Nothing is bound</output>
<output id="o3" data-bind:text-content>Nothing is bound</output>
<output id="o4" data-bind:innerText>Nothing is bound</output>
<output id="o5" data-bind:inner-text>Nothing is bound</output>

<output id="o6" data-bind:html>Nothing is bound</output>
<output id="o7" data-bind:HTML>Nothing is bound</output>
<output id="o8" data-bind:innerHTML>Nothing is bound</output>
<output id="o9" data-bind:inner-html>Nothing is bound</output>
HTML;


	const HTML_MULTIPLE_ELEMENTS = <<<HTML
<!doctype html>
<output id="o1" data-bind:text>First default</output>
<output id="o2" data-bind:text>Second default</output>
<output id="o3" data-bind:text>Third default</output>
HTML;

	const HTML_MULTIPLE_NESTED_ELEMENTS = <<<HTML
<!doctype html>
<div id="container1">
	<output id="o1" data-bind:text>First default</output>
	<output id="o2" data-bind:text>Second default</output>
	<output id="o3" data-bind:text>Third default</output>
</div>
<div id="container2">
	<output id="o4" data-bind:text>Fourth default</output>
	<output id="o5" data-bind:text>Fifth default</output>
	<output id="o6" data-bind:text>Sixth default</output>
</div>
<div id="container3">
	<h1 data-bind:text="title">Default title</h1>
	<output id="o7" data-bind:text>Seventh default</output>
	<p>
		You have just bound the <span data-bind:text="title">default title</span> title!
	</p>
</div>
HTML;

	const HTML_USER_PROFILE = <<<HTML
<!doctype html>
<h1>User profile</h1>
<dl>
	<dt>Username</dt>
	<dd id="dd1" data-bind:text="username">username123</dd>
	<dt>Email address</dt>
	<dd id="dd2" data-bind:text="email">you@example.com</dd>
	<dt>Category</dt>
	<dd id="dd3" data-bind:text="category">N/A</dd>
</dl>

<h2>Audit trail</h2>
<div id="audit-trail">
	<p>The following activity has been recorded on your account:</p>
	
	<ul></ul>
</div>
HTML;

	const HTML_DIFFERENT_BIND_PROPERTIES = <<<HTML
<!doctype html>
<img id="img1" class="main" src="/default.png" alt="Not bound" 
	data-bind:src="photoURL" 
	data-bind:alt="altText" 
	data-bind:class="size" />

<img id="img2" class="secondary" src="/default.png" alt="Not bound"
	data-bind:class=":is-selected" data-rebind />

<img id="img3" class="secondary" src="/default.png" alt="Not bound"
	data-bind:class=":isSelected selected-image" data-rebind />

<p id="p1" data-params="funny friendly" data-bind:data-params=":isMagic magical" data-rebind>Is this paragraph magical?</p>

<form id="form1">
	<button id="btn1" data-bind:disabled="?isBtn1Disabled" data-rebind></button>
	<button id="btn2" data-bind:disabled="?!isBtn2Enabled" data-rebind></button>
</form>
HTML;

	const HTML_TABLES = <<<HTML
<!doctype html>
<table id="tbl1" data-bind:table="tableData"></table>

<table id="tbl2" data-bind:table="tableData">
	<thead>
		<tr>
			<th data-table-key="firstName">First name</th>
			<th data-table-key="lastName">Last name</th>
			<th data-table-key="email">Email address</th>
		</tr>	
	</thead>
	<tbody>
		<tr>
<!-- This row already exists in the HTML and should be kept when new data is bound -->
			<td>Greg</td>
			<td>Bowler</td>
			<td>greg@php.gt</td>
		</tr>
	</tbody>
</table>

<table id="tbl3">
	<thead>
		<tr>
<!-- Here you can see the data's key is in the TH elements. -->
			<th>firstName</th>
			<th>lastName</th>
			<th>email</th>
		</tr>	
	</thead>
	<tbody>
		<tr>
<!-- This row already exists in the HTML and should be kept when new data is bound -->
			<td>Greg</td>
			<td>Bowler</td>
			<td>greg@php.gt</td>
		</tr>
	</tbody>
</table>

<div id="multi-table-container">
	<section id="s1">
		<p>First table:</p>
		<table data-bind:table="tableData"></table>	
	</section>
	
	<section id="s2">
		<p>Second table (different data):</p>
		<table data-bind:table="tableData2"></table>	
	</section>
	
	<section id="s3">
		<p>Third table (same data):</p>
		<table data-bind:table="tableData"></table>	
	</section>
</div>
HTML;
	const HTML_NO_TABLE = <<<HTML
<!doctype html>
<div data-bind:table="tableData">
	<p>There's no table in here, mate.</p>
</div>
HTML;

	const HTML_TABLE_NO_BIND_KEY = <<<HTML
<!doctype html>
<div data-bind:table>
	<table></table>
</div>
HTML;

	const HTML_TABLE_ID_NAME_CODE = <<<HTML
<!doctype html>
<table>
<thead>
	<tr>
		<th>ID</th>
		<th>Name</th>
		<th>Code</th>
		<th>Delete</th>
	</tr>
</thead>
</table>
HTML;

	const HTML_TABLE_EXISTING_CELLS = <<<HTML
<!doctype html>
<table>
<thead>
	<tr>
		<th>Delete</th>
		<th data-table-key="id">ID</th>
		<th data-table-key="name">Name</th>
		<th data-table-key="code">Code</th>
		<th>Flag</th>
	</tr>
</thead>
<tbody>
	<tr data-template>
		<td data-bind:class=":deleted">
			<form method="post">
				<input type="hidden" name="id" data-bind:value="@name" />
				<button name="do" value="delete">Delete</button>
			</form>
		</td>
		<td></td>
		<td></td>
		<td></td>
		<td>
			<form method="post">
				<input type="hidden" name="id" data-bind:value="@name" />
				<button name="do" value="flag">Flag</button>
			</form>
		</td>
	</tr>
</tbody>
</table>
HTML;


	const HTML_LIST_TEMPLATE = <<<HTML
<!doctype html>
<ul>
	<li data-template data-bind:text>Template item!</li>
</ul>
<ol>
	<li>This doesn't have a data template attribute</li>
</ol>
HTML;

	const HTML_TWO_LISTS = <<<HTML
<!doctype html>
<div id="favourites">
	<h1>My favourite programming languages</h1>
	<ul id="prog-lang-list">
		<li data-template="prog-lang" data-bind:text>Programming language goes here</li>
	</ul>
	
	<h1>My favourite video games</h1>
	<ul id="game-list">
		<li data-template="game" data-bind:text>Video game goes here</li>
	</ul>
</div>
HTML;

	const HTML_TWO_LISTS_WITH_UNNAMED_TEMPLATES = <<<HTML
<!doctype html>
<div id="favourites">
	<h1>My favourite programming languages</h1>
	<ul id="prog-lang-list">
		<li data-template data-bind:text>Programming language goes here</li>
	</ul>
	
	<h1>My favourite video games</h1>
	<ul id="game-list">
		<li data-template data-bind:text>Video game goes here</li>
	</ul>
</div>
HTML;

	const HTML_TWO_LISTS_WITH_UNNAMED_TEMPLATES_CLASS_PARENTS = <<<HTML
<!doctype html>
<div id="favourites">
	<h1>My favourite programming languages</h1>
	<ul class="favourite-list prog-lang">
		<li data-template data-bind:text>Programming language goes here</li>
	</ul>
	
	<h1>My favourite video games</h1>
	<ul class="favourite-list game">
		<li data-template data-bind:text>Video game goes here</li>
	</ul>
</div>
HTML;

	const HTML_USER_ORDER_LIST = <<<HTML
<!doctype html>
<div id="orders">
	<h1>Most active users</h1>
	<ul>
		<li id="user-{{userId}}" data-template>
			<h2>Username: <span data-bind:text="username">username</span></h2>
			<h3>ID: <span data-bind:text="userId">000</span></h3>
			<p>Number of orders: <span data-bind:text="orderCount">0</span>, <a href="/orders/{{userId}}">view</a></p>
		</li>
	</ul>
</div>
HTML;
	const HTML_PLACEHOLDER = <<<HTML
<!doctype html>
<main id="test1">
	<p>This example shows how to bind text into placeholders.</p>
	<p class="greeting">Hello, {{name}}!</p>
</main>
<main id="test2">
	<p>This example shows how to bind text into placeholders.</p>
	<p>Now with a default value!</p>
	<p class="greeting">Hello, {{name ?? you}}!</p>
</main>
<main id="test2a">
	<p>This example shows how to bind text into placeholders.</p>
	<p>Now with a default value, with a different use of white space!</p>
	<p class="greeting">Hello, {{name??you}}!</p>
</main>
<main id="test3">
	<p>This example shows how to bind text into attribute placeholders.</p>
	<p>For more information, <a href="https://www.php.gt/{{repoName}}">view the docs.</a></p>
</main>
<main id="test4">
	<p>This example shows how to bind text into attribute placeholders.</p>
	<p>For more information, <a href="https://www.php.gt/{{repoName ?? domtemplate}}">view the docs.</a></p>
</main>
<main id="test5">
	<p>Please consider sponsoring this project</p>
	<p><a href="https://github.com/sponsors/{{org}}/sponsorships?tier_id={{tierId}}">Sponsor via GitHub.</a></p>
</main>
HTML;
	const HTML_MUSIC_EXPLICIT_TEMPLATE_NAMES = <<<HTML
<!doctype html>
<h1>Music library</h1>

<ul>
	<li data-template="artist">
		<h2 data-bind:text>Artist name</h2>
		
		<ul>
			<li data-template="album">
				<h3 data-bind:text>Album name</h3>
				
				<ol>
					<li data-template="track" data-bind:text>Track name</li>
				</ol>
			</li>
		</ul>
	</li>
</ul>
HTML;

	const HTML_MUSIC_NO_TEMPLATE_NAMES = <<<HTML
<!doctype html>
<h1>Music library</h1>

<ul>
	<li data-template>
		<h2 data-bind:text>Artist name</h2>
		
		<ul>
			<li data-template>
				<h3 data-bind:text>Album name</h3>
				
				<ol>
					<li data-template data-bind:text>Track name</li>
				</ol>
			</li>
		</ul>
	</li>
</ul>
HTML;

	const HTML_STUDENT_LIST = <<<HTML
<!doctype html>
<h1>List of students:</h1>
<ul>
	<li data-template>
		<dl>
			<dt>Student name</dt>
			<dd class="name">{{firstName}} {{lastName}}</dd>
			
			<dt>Current modules</dt>
			<dd class="modules">
				<ul>
					<li data-template data-bind:text>Module name</li>
				</ul>		
			</dd>
		</dl>
	</li>
</ul>
HTML;

	const HTML_LANGUAGE = <<<HTML
<!doctype html>
<html data-bind:lang="language">
<head>
	<meta charset="utf-8" />
	<title>Language test</title>
</head>
<body>
	<h1>Just a simple language test</h1>
	<p>Take a look at the "lang" attribute of the HTML element.</p>
</body>
</html>
HTML;
	const HTML_SEQUENCES = <<<HTML
<!doctype html>
<h1>Numerical sequences</h1>
<ul>
	<li data-template>
		<h2 data-bind:text>Sequence name</h2>
		
		<ol>
			<li data-template data-bind:text>0</li>		
		</ol>	
	</li>
</ul>
HTML;
	const HTML_DATES = <<<HTML
<!doctype html>
<h1>Month starting days for each month this year:</h1>
<ul>
	<li data-template data-bind:text></li>
</ul>
HTML;

	const HTML_TODO = <<<HTML
<!doctype html>
<html>
<head>
	<meta charset="utf-8" />
	<title>TODO LIST!</title>
</head>
<body>
	<h1>TODO LIST!</h1>
	<ul>
		<li data-template data-bind:class=":completedAt completed">
			<form method="post">
				<input type="hidden" name="id" data-bind:value="@name" />
				<input name="title" data-bind:value="@name" />
				<button name="do" value="complete">Complete</button>
				<button name="do" value="delete">Delete</button>
			</form>
		</li>
	</ul>
</body>
</html>
HTML;

	const HTML_TODO_CUSTOM_ELEMENT = <<<HTML
<!doctype html>
<html>
<head>
	<meta charset="utf-8" />
	<title>TODO LIST!</title>
</head>
<body>
	<h1>TODO LIST!</h1>
	<todo-list />
</body>
</html>
HTML;

	const HTML_TODO_COMPONENT_TODO_LIST = <<<HTML
<ul>
	<todo-list-item data-template data-bind:class=":completedAt completed" />
</ul>
HTML;

	const HTML_TODO_COMPONENT_TODO_LIST_ITEM = <<<HTML
<li>
	<form method="post">
		<input type="hidden" name="id" data-bind:value="@name" />
		<input name="title" data-bind:value="@name" />
		<button name="do" value="complete">Complete</button>
		<button name="do" value="delete">Delete</button>
	</form>
</li>
HTML;

	const HTML_GOOD_BAD = <<<HTML
<!doctype html>
<p>The following list of "good" and "bad" templates is intentionally formatted like this.</p>
<p>There should be no whitespace, and therefore the template siblings must not include other template items.</p>
<ul><li data-template="good"><i>GOOD</i> <span data-bind:text>Good message</span></li><li data-template="bad"><i>BAD</i> <span data-bind:text>Bad message</span></li></ul>
HTML;

	const HTML_PARTIAL_VIEW = <<<HTML
<!doctype html>
<html>
<head>
	<meta charset="utf-8" />
	<title data-bind:test="title">My website</title>
</head>
<body>
	<header>
		<h1>My website!</h1>
	</header>
	<main data-partial>
		The page content will go in here.
	</main>
	<footer>
		<p>Thank you for visiting!</p>
	</footer>
</body>
</html>
HTML;

	const HTML_INCORRECT_PARTIAL_VIEW = <<<HTML
<!doctype html>
<html>
<head>
	<meta charset="utf-8" />
	<title data-bind:test="title">My website</title>
</head>
<body>
	<header>
		<h1>My website!</h1>
	</header>
	<main data-partial>
		The page content will go in here.
	</main>
	<div>
		<article data-partial>
			But, there's another partial element here, so things will break.
		</article>	
	</div>
	<footer>
		<p>Thank you for visiting!</p>
	</footer>
</body>
</html>
HTML;

	const HTML_EXTENDS_PARTIAL_VIEW = <<<HTML
<!-- 
extends=base-page

[vars]
title=My website, extended... 
-->
<article>
	<h1>Hello from within a sub-template!</h1>
	<p>This piece of HTML should be placed within the appropriate template.</p>
</article>
<aside>
	Some useful links can be put here.
</aside>
HTML;


	const HTML_INCORRECTLY_EXTENDS_PARTIAL_VIEW = <<<HTML
<article>
	<h1>Hello from within a sub-template!</h1>
	<p>This piece of HTML should be placed within the appropriate template.</p>
</article>
<!-- 
extends=base-page

[vars]
title=My website, extended... 
-->
<aside>
	Some useful links can be put here.
</aside>
HTML;

	const HTML_EXTENDS_PARTIAL_VIEW_RECURSIVE = <<<HTML
<!-- 
extends=extended-page
-->
<title>This title was set in the inner partial view.</title>
<div class="inner">
	<p>This is an inner DIV element that will be placed into the "extended page" partial.</p>
</div>
HTML;

	const HTML_EXTENDS_PARTIAL_VIEW_RECURSIVE_BASE = <<<HTML
<!-- extends=partial-base -->
<div class="outer">
	<p>This is the outer DIV element that will be placed into the base partial view.</p>
	<div data-partial></div>
</div>
HTML;

	const HTML_COMMENT_WITHOUT_INI_DATA_PARTIAL_VIEW = <<<HTML
<!-- 
This is just a message to test ini parsing. 
Oh yeah!
-->
<article>
	<h1>Hello from within a sub-template!</h1>
	<p>This piece of HTML should be placed within the appropriate template.</p>
</article>
<aside>
	Some useful links can be put here.
</aside>
HTML;
	const HTML_COMPONENT = <<<HTML
<!doctype html>
<h1>Component test</h1>
<p>This test shows how a custom element can be injected into the document.</p>
<custom-element />
<p>If there's matching partial content in the _component directory, the above element will be filled with its content.</p>
HTML;

	const HTML_TRANSPORT_ROUTES = <<<HTML
<!doctype html>
<h1>Transport Routes</h1>
<form>
	<label>
		<span>From:</span>
		<input name="from" data-bind:value="@name" required />
	</label>
	<label>
		<span>To:</span>
		<input name="to" data-bind:value="@name" required />
	</label>
	<button name="do" value="route">Route!</button>
</form>

<ul>
	<li data-template>
		<p data-bind:text="method">Type</p>
		<time data-bind:text="duration">Type</time>
		
		<ol>
			<li data-template>
				<a href="/route/step/{{}}">
					<time data-bind:text="time">00:00</time>
					<span data-bind:text="location">Somewhere</span>
				</a>
			</li>
		</ol>	
	</li>
</ul>
HTML;

	const HTML_SALES = <<<HTML
<!doctype html>
<h1>Sales</h1>
<ul>
	<li data-template>
		<p class="name">Item: <span data-bind:text="name">Item name</span></p>
		<p class="count">Sale count: <span data-bind:text="count">0</span></p>
		<p class="price">Price per item: £<span data-bind:text="price">0.00</span></p>
		<p class="cost">Cost per item: £<span data-bind:text="cost">0.00</span></p>
		<p class="profit">Total profit: £<span data-bind:text="profit">0.00</span></p>
	</li>
</ul>
HTML;

	const HTML_TWO_SUB_LISTS_SEPARATED_BY_ELEMENT = <<<HTML
<!doctype html>
<main>
	<dl>
		<dt class="blue">Shades of blue</dt>
		<dd data-template="blue" data-bind:text>Blue</dd>
		
		<dt class="red">Shades of red</dt>
		<dd data-template="red" data-bind:text>Red</dd>
	</dl>
</main>
HTML;

	const HTML_SELECT_OPTIONS_WITH_VALUE = <<<HTML
<!doctype html>
<form>
	<label>
		<span>Select your drink preference</span>
		<select name="drink" data-bind:value="drink">
			<option></option>
			<option value="coffee">Coffee</option>		
			<option value="tea">Tea</option>		
			<option value="chocolate">Chocolate</option>		
			<option value="soda">Soda</option>		
			<option value="water">Water</option>		
		</select>
	</label>
</form>
HTML;

	const HTML_SELECT_OPTIONS_WITHOUT_VALUE = <<<HTML
<!doctype html>
<form>
	<label>
		<span>Select your drink preference</span>
		<select name="drink" data-bind:value="drink">
			<option></option>
			<option>Coffee</option>		
			<option>Tea</option>		
			<option>Chocolate</option>		
			<option>Soda</option>		
			<option>Water</option>		
		</select>
	</label>
</form>
HTML;

	const HTML_TEMPLATE_ELEMENT_WITH_MULTIPLE_DIVS = <<<HTML
<!doctype html>
<h1>Test</h1>

<h2>This element will always be after the H1</h2>
<p data-template>This is the template element</p>
<div>This is the contents of the first DIV. The template should come before it.</div>
<div>This is the contents of the second DIV. The previous DIV should come before it.</div>
HTML;

	const HTML_TEMPLATES_WITH_SAME_XPATH = <<<HTML
<!doctype html>
<main class="rfi">
	<input type="radio" name="tab-control" id="tab-report" checked />
	<input type="radio" name="tab-control" id="tab-log"/>
	<input type="radio" name="tab-control" id="tab-emails"/>
	<input type="radio" name="tab-control" id="tab-attachments"/>

	<section id="report">
		<h1>Non Conformance Report (NCR)</h1>
		<a href="../" class="close"><span>Close</span></a>

		<form method="post" class="submission" enctype="multipart/form-data">
			<input type="hidden" name="issuerUuid" data-bind:value="userUuid" />

			<h2>NCR Submission</h2>

			<div class="actions">
				<div>
					<label>
						<span>Pass on to</span>
						<select name="pass-on-to" data-bind:value="passOnToUserUuid">
							<option></option>
							<option data-template data-bind:text="fullName" data-bind:value="uuid"></option>
						</select>
					</label>
					<label>
						<span>Assign User</span>
						<select name="tag-user" data-bind:value="tagUserUuid">
							<option></option>
							<option data-template data-bind:text="fullName" data-bind:value="uuid"></option>
						</select>
					</label>
				</div>

				<div class="buttons">
					<button name="do" value="save-request">Save</button>
					<button name="do" value="submit-request" onclick="return confirm('Are you sure this NCR is ready to be submitted?')">Submit NCR</button>
				</div>
			</div>
		</form>
	</section>
</main>
HTML;

	const HTML_EXTENDS_PARTIAL_CYCLIC_RECURSION = <<<HTML
<!-- extends=extended-page-1 -->
<div>
	This is the deepest part of the cyclic recursion.
</div> 
HTML;


	const HTML_EXTENDS_PARTIAL_CYCLIC_RECURSION_1 = <<<HTML
<!--
extends=extended-page-2
-->
<div>
	This HTML extends page 2. 
</div>
<div data-partial>Extended page is injected here</div>
HTML;

	const HTML_EXTENDS_PARTIAL_CYCLIC_RECURSION_2 = <<<HTML
<!--
extends=extended-page-1
-->
<div>
	This HTML extends page 1.
</div>
<div data-partial>Extended page is injected here</div>
HTML;

	// For https://github.com/PhpGt/DomTemplate/issues/341
	const HTML_BIND_KEY_REUSED = <<<HTML
<div>
	<h1>Your shopping preference</h1>
	
	<select name="shopId" required data-bind:value="selectedShopId">
		<option data-template="shop" data-bind:text="name" data-bind:value="id"></option>
	</select>
	
	<p>Your receipt ID is <span data-bind:text="id">000</span></p>
</div>
HTML;


	public static function createHTML(string $html = ""):HTMLDocument {
		return new HTMLDocument($html);
	}

	public static function createXML(string $xml):XMLDocument {
		return new XMLDocument($xml);
	}
}
