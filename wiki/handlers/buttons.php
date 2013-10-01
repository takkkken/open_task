<?php

	function buttons ()	{
	    global $language;
		$html = "<div id='editbar' style='position:relative;z-index:102;float:left;'><div style='border:1px solid #ccc;float:left;margin-left:14px;background:url(\"images/system/button-bar.png\");'>";
		$html .= "<div style='float:left;'><input type='image' class='image' src='images/system/format-text-bold.png' title='<strong>selection</strong>' onclick='editbuttons(\"*\")'; /></div>";
		$html .= "<div style='float:left;'><input type='image' class='image' src='images/system/format-text-italic.png' title='<em>selection</em>' onclick='editbuttons(\"_\")'; /></div>";
		$html .= "<div style='float:left;'><input type='image' class='image' src='images/system/insert-horizontal-rule.png' title='<hr/>' onclick='editbuttons(\"HR\")'; /></div>";
		$html .= "<div style='float:left;'><input type='image' class='image' src='images/system/format-list-unordered.png' title='(<ul>)<li>selection</li>' onclick='editbuttons(\"LI\")'; /></div>";
		$html .= "<div style='float:left;'><input type='image' class='image' src='images/system/format-list-ordered.png' title='(<ol>)<li>selection</li>' onclick='editbuttons(\"OL\")'; /></div>";
		$html .= "<div style='float:left;'><input type='image' class='image' src='images/system/insert-link.png' title='<a href=url>selection</a>' onclick='editbuttons(\"A\")'; /></div>";
		$html .= "<div style='float:left;'><input type='image' class='image' src='images/system/insert-image.png' title='".$language->insertimage."' onclick='imagepage()'; /></div>";
		$html .= "<div style='float:left;'><input  type='image' class='image' src='images/system/plugin.png' title='Plugin - For advanced users only' onclick='editbuttons(\"Plugin\")'; /></div>";
		
		// dropdown menus
		$html .= "<div class='menuitem'><a href='javascript:dummy();' onmouseover='editmenu(\"hmenu\");' >Heading</a>";
		$html .= "<div id='hmenu' class='editmenu'>";
		$html .= "<ul class='menu'><li class='menu'><a href='javascript:editbuttons(\"=\",\"hmenu\");'><img src='images/system/bullet_white.png' class='menu' />&lt;H1&gt;</a></li>";
		$html .= "<li class='menu'><a href='javascript:editbuttons(\"==\",\"hmenu\");'><img src='images/system/bullet_white.png' class='menu' />&lt;H2&gt;</a></li>";
		$html .= "<li class='menu'><a href='javascript:editbuttons(\"===\",\"hmenu\");'><img src='images/system/bullet_white.png' class='menu' />&lt;H3&gt;</a></li>";
		$html .= "<li class='menu'><a href='javascript:editbuttons(\"====\",\"hmenu\");'><img src='images/system/bullet_white.png' class='menu' />&lt;H4&gt;</a></li>";
		$html .= "<li class='menu'><a href='javascript:editbuttons(\"=====\",\"hmenu\");'><img src='images/system/bullet_white.png' class='menu' />&lt;H5&gt;</a></li>";
		$html .= "<li class='menu'><a href='javascript:editbuttons(\"======\",\"hmenu\");'><img src='images/system/bullet_white.png' class='menu' />&lt;H6&gt;</a></li></ul></div></div>";

		$html .= "<div class='menuitem'><a href='javascript:dummy()'  onmouseover='editmenu(\"codemenu\");'>Code</a>";
		$html .= "<div id='codemenu' class='editmenu'>";
		$html .= "<ul class='menu' style='min-width:76px;><li class='menu'><a href='javascript:editbuttons(\"%\",\"codemenu\");'><img src='images/system/bullet_white.png' class='menu' />&lt;CODE&gt;</a></li>";
		$html .= "<li class='menu'><a href='javascript:editbuttons(\"PRE\",\"codemenu\");'><img src='images/system/bullet_white.png' class='menu' />&lt;PRE&gt;</a></li></ul></div></div>";
		
		$html .= "<div class='menuitem'><a href='javascript:dummy()' onmouseover='editmenu(\"textmenu\");' >Text</a>";
		$html .= "<div id='textmenu'  class='editmenu'>";
		$html .= "<ul class='menu'><li class='menu'><a href='javascript:editbuttons(\"^\",\"textmenu\");'><img src='images/system/format-text-superscript.png' class='menu' />&lt;SUP&gt;</a></li>";
		$html .= "<li class='menu'><a href='javascript:editbuttons(\"~\",\"textmenu\");'><img src='images/system/format-text-subscript.png' class='menu' />&lt;SUB&gt;</a></li>";
		$html .= "<li class='menu'><a href='javascript:editbuttons(\"INS\",\"textmenu\");'><img src='images/system/format-text-underline.png' class='menu' />&lt;INS&gt;</a></li>";
		$html .= "<li class='menu'><a href='javascript:editbuttons(\"DEL\",\"textmenu\");'><img src='images/system/format-text-strikethrough.png' class='menu' />&lt;DEL&gt;</a></li>";
		$html .= "<li class='menu'><a href='javascript:editbuttons(\"CSS\",\"textmenu\");'><img src='images/system/css_add.png' class='menu' />CSS</a></li></ul></div></div>";
		
		$html .= "<div style='clear:both'></div></div></div></div>";

		return $html;
	}

?>