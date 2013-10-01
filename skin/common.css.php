<?php
require_once '../webapp/CB.php';
header("Content-type: text/css");
?>

/* COMMON */
body {
	margin: 0px;
	}
body, TD, DIV { 
	font-family:'ÉqÉâÉMÉmäpÉS Pro W3','Hiragino Kaku Gothic Pro','ÇlÇr ÇoÉSÉVÉbÉN','MS PGothic','ÉÅÉCÉäÉI',Meiryo,sans-serif;
	color: #121212;
}

/*body { direction: rtl; unicode-bidi: embed; }
TD   { direction: rtl; unicode-bidi: embed; }
DIV  { direction: rtl; unicode-bidi: embed; }*/

/* COMMON LINKS */
a:link     { background-color: transparent; }
a:visited  { background-color: transparent; }
a:active   { background-color: transparent; }
a:hover    { background-color: transparent; }
a.noformat { text-decoration: none; color: #121212; }
a img{ border:none; }

a {
	color:navy;
	text-decoration: none;
}
a:hover, a:active {
	color:navy;
    text-decoration: underline;
}

a:visited {
	color:navy;
}
a.myname {
    background-color: #b0e0e6;
	border-width: 0px;
	padding: 2px;
	border-radius: 2px;
	-webkit-border-radius: 2px;
	-moz-border-radius: 2px;
}


/* CATEGORY */
.catLink        { font-weight : bold; }
.catDescription { color : #121212;  }


/* ARTICLE */
H1.articleTitle    { font-size : 1.0em; margin: 0px; }
a.articleLink      { font-weight : bold; }
a.articleLinkOther {  }  /* for other in category and related */
.articleDecription { color : #121212; }
.articleStaff      { color : #505050; 	font-size : 0.8em; }
.glossaryItem      { background-color : #F0F0F0; cursor : help; color : #CC3333; }


/* HEADER & CONTENT */	
div.content {
	background-color: #ffffff;
	padding-left: 8px;
	padding-right: 8px;
}

/* LOGIN LINK */
div.login { font-size : 0.8em; font-weight: bold; text-align: right; white-space : nowrap;
			padding-right: 15px; padding-bottom: 8px; }		
a.login {  }


/* NAVIGATION */
div.navigation  { color : #121212; 	background-color: #ffffff; font-size : 0.9em; }
a.navigation    { color : #121212; }


/* ARTICLE BLOCK */
a.abLink     {  }
.abBorder    { background-color : #E4E4E4;  }
.abBgr       { background-color : #FFFFFF;  font-size : 0.9em; }
.abBgrDarker { background-color : #FAFAFA;  color : #606060;  font-size : 0.9em; }


/* ATTACHMENT */
.atTitle      { font-weight : bold;  }
.atEntry      { /* font-size: 0.8;*/ }


/* FILES */
.fName        { font-size : 0.8em; }


/* TABLES */
.tdBorder     { background-color : #E4E4E4;  }
.tdTitle      { background-color : #E4E4E4; padding : 4px 4px;  border : 1px solid #D4D4D4; }
.tdSubTitle   { background-color : #EFEFEF; padding : 4px 4px;  border : 1px solid #DADADA; }

.trLighter    { background-color : #FFFFFF; }
.trDarker     { background-color : #F4F4F4; }
.trMoreDarker { background-color : #DADADA; }


/*  FORMS  */
.trForm         { background-color : #FAFAFA; }
.tdFormCaption  { background-color : #F4F4F4;  text-align: right; width: 150px; }

input, select   { font-size : 12px;  margin : 2px;  padding: 2px; }
input.text      { width : 110px; }
input.shortText { width : 80px; }
input.longText  {  width : 250px;   }

.button         { width : 150px; }
.colorInput     { background-color : #F5F4ED; }
.requiredSign   { color : #C40000; font-weight : bold; }
.formComment    { font-size : 0.9em; }


/*  OTHER  */
.copyright      { font-size : 0.8em; }
.pageByPage     { font-size : 0.9em; }
.smallerText    { font-size : 0.9em; }
.nowrap         { white-space: nowrap; }
.space          { padding-bottom: 5px; }
.less_space     { padding-bottom: 2px; }
.info           { background-color : #FFFFE1; border : 1px solid #8592A2; padding: 10px;}

.fright         { float: right; }
.fleft          { float: left; }


#header {
    color: #ffffff;
    background-color: #464646;
    margin-bottom: 1em;
    font-size: 10pt;
    padding: 2.5em 1em 0.2em 1em;
}

#siteTitle {
    font-size: 20pt;
}

.siteMenu {
	display: inline-block;
	text-align: center;
	width: 100px;
    font-size: 10pt;
	border-color: darkgray;
	border-width: 1px;
	border-style: solid;
	padding: 3px;
	border-radius: 5px;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
}

#header a {
    width: 400px;
    color: #CCFF33;
}

/* LOGIN */	
div.login { font-size : 0.9em; font-weight: bold; text-align: right; 
			padding-right: 15px;  color: #FFFFFF; }
a.login {  color: #FFFFFF; }

body {  font-size : 75%;  }
TD   {  font-size : 1.0em;  }
DIV  {  font-size : 1.0em; }

.textBlock { line-height : 150%; }

.admin td { background:#F5F5F5;}

<?php echo $GLOBALS['topic_status_css']; ?>