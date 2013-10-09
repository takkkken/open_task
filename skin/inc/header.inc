<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <title><?=PJ_NAME?> / <?=SITE_NAME?></title>
    <!--[if IE]>
    <link rel="stylesheet" type="text/css" href="./skin/common_ie.css" />
    <![endif]-->
    <link rel="stylesheet" type="text/css" href="./skin/common.css.php" />
    <link rel="alternate" type="application/rss+xml" title="RSS" href="./topic_list.php?<?=http_build_query(array("mode"=>"rss"))?>">
    <link rel="stylesheet" type="text/css" href="./js/popup/popbox.css" />

    <script type='text/javascript' src="./js/liveDate.php?client=all&stub=liveDate"></script>
    <script type='text/javascript' src="./js/common.js"></script>
	<script type="text/javascript" src="./js/jquery.min.js"></script>
	<script type="text/javascript" src="./js/popup/popbox.min.js"></script>
	<script type="text/javascript" src="./js/jquery.peity.min.js"></script>
	<script type="text/javascript" src="./js/jquery-ui.min.js"></script>



</head>
<body>

<div id="header">
		<span style="display: inline-block;width: 400px;">
		<a id="siteTitle" href="./"><?=SITE_NAME?> / <?=PJ_NAME?></a>
		</span>
		<span class="siteMenu"><a href="./wiki/"><?=WIKI_NAME?></a></span>
		<span class="siteMenu" style="background-color: dimgray;"><?=PJ_NAME?></span>
		<span class="siteMenu"><a href="./websvn/"><?=SVNV_NAME?></a></span>
<p class="fright">
<?php if(isset($GLOBALS['user'])){ ?>
		<?=$GLOBALS['user']['user_name']?>　<a onclick="alert('次のダイアログで、キャンセルをクリックして下さい。\nログアウトします。');" href=./?logout>ログアウト</a></p>
<?php } ?>
</p>
</div>



<div class="content" id="content_div">
    <table border="0" width="100%" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF">
    <tr valign="bottom">
        <td class="nowrap">
            
            <div style="padding-bottom: 5px;" class="nowrap smallerText">
                <b>検索:</b>&nbsp;&nbsp;&nbsp;&nbsp;
            </div>
            <form action="./" style="margin:0px;">
                        <?=hiddenForm('q');?>
                <input type="text" name="q" id="q" value="<?=$_GET["q"]?>" class="colorInput" style="width: 100px" />
                <input type="submit" value="検索" />
            </form>
        </td>
        <td class="nowrap">
            <div style="padding-bottom: 5px;" class="nowrap smallerText">
                <b>状態:</b>
            </div>
            <form action="./" style="margin:0px;">
                <?=hiddenForm('topic_status');?>
                <select name="topic_status" class="colorInput" style="width: 70px" onchange="this.form.submit(true);">
                <option value="0" selected>▼状態</option>
                    <?php foreach($GLOBALS['topic_status'] AS $topic_status_key=>$topic_status_val){ ?>
                    <option value="<?=$topic_status_key?>"<?php if($topic_status_key==$_GET["topic_status"]) echo " selected=\"\"selected"?>><?=$topic_status_key?></option>
                    <?php } ?>
                </select>
                <!-- <input type="submit" name="browse" id="browse" value="検索" /> -->
            </form>
            
        </td>
<!--		<td class="nowrap">
            <div style="padding-bottom: 5px;" class="nowrap smallerText">
                <b>期限:</b>
            </div>
            <form action="./" style="margin:0px;">
                <?=hiddenForm('topic_due_datetime');?>
                <select name="topic_due_datetime" class="colorInput" style="width: 70px" onchange="this.form.submit(true);">
                <option value="0" selected>▼期限</option>
                    <?php foreach($GLOBALS['topic_due_datetime'] AS $topic_due_datetime_key=>$topic_due_datetime_val){ ?>
                    <option value="<?=$topic_due_datetime_key?>"<?php if($topic_due_datetime_key==$_GET["topic_due_datetime"]) echo " selected=\"\"selected"?>><?=$topic_due_datetime_key?></option>
                    <?php } ?>
                </select>
-->                 <!-- <input type="submit" name="browse" id="browse" value="検索" /> -->
<!--            </form>
        </td>
-->       
        <td class="nowrap">
            <div style="padding-bottom: 5px;" class="nowrap smallerText">
                <b>担当者:</b>
            </div>
            <form action="./" style="margin:0px;">
                <?=hiddenForm('user');?>
                <select name="user" class="colorInput" style="width: 90px" onchange="this.form.submit(true);">
                <option value="all"<?php if($_GET["user"]=="all") echo " selected=\"\"selected"?>>▼担当者</option>
                <option value="<?=$GLOBALS["user"]["user_name"]?>"<?php if($_GET["user"]==$GLOBALS["user"]["user_name"]) echo " selected=\"\"selected"?>><?=$GLOBALS["user"]["user_name"]?></option>
<?php
				foreach($GLOBALS['topic_to_user'] AS $topic_to_user_key=>$topic_to_user_val){
?>
                    <option value="<?=$topic_to_user_val["user_name"]?>"
<?php
					if($topic_to_user_val["user_name"]==$_GET["user"])
						echo " selected=\"\"selected";
?>
					>
					<?=$topic_to_user_val["user_name"]?></option>
<?php
				}
?>
<?php
				foreach($GLOBALS['topic_to'] AS $key=>$val){
?>
					<option value="<?=$key?>"><?=$key?></option>
<?php
				}
?>
                </select>
                <!-- <input type="submit" name="browse" id="browse" value="検索" /> -->
            </form>
            
        </td>
        
        <td class="nowrap">
            <div style="padding-bottom: 5px;" class="nowrap smallerText">
                <b>関係者:</b>
            </div>
            <form action="./" style="margin:0px;">
                <?=hiddenForm('topic_cc');?>
                <select name="topic_cc" class="colorInput" style="width: 90px" onchange="this.form.submit(true);">
                <option value="all"<?php if($_GET["topic_cc"]=="all") echo " selected=\"\"selected"?>>▼関係者</option>
                <option value="<?=$GLOBALS["user"]["user_name"]?>"<?php if($_GET["topic_cc"]==$GLOBALS["user"]["user_name"]) echo " selected=\"\"selected"?>><?=$GLOBALS["user"]["user_name"]?></option>
<?php
				foreach($GLOBALS['topic_to_user'] AS $topic_to_user_key=>$topic_to_user_val){
?>
                    <option value="<?=$topic_to_user_val["user_name"]?>"
<?php
					if($topic_to_user_val["user_name"]==$_GET["topic_cc"])
						echo " selected=\"\"selected";
?>
					>
					<?=$topic_to_user_val["user_name"]?></option>
<?php
				}
?>
<?php
				foreach($GLOBALS['topic_to'] AS $key=>$val){
?>
					<option value="<?=$key?>"><?=$key?></option>
<?php
				}
?>
                </select>
                <!-- <input type="submit" name="browse" id="browse" value="検索" /> -->
            </form>
            
        </td>
        <td class="nowrap">
            <div style="padding-bottom: 5px;" class="nowrap smallerText">
                <b>種別:</b>
            </div>
            <form action="./" style="margin:0px;">
                <?=hiddenForm('topic_type');?>
                <select name="topic_type" class="colorInput" style="width: 70px" onchange="this.form.submit(true);">
                <option value="0" selected>▼種別</option>
                    <?php foreach($GLOBALS['topic_type'] AS $topic_type_key=>$topic_type_val){ ?>
                    <option value="<?=$topic_type_key?>"<?php if($topic_type_key==$_GET["topic_type"]) echo " selected=\"\"selected"?>><?=$topic_type_key?></option>
                    <?php } ?>
                </select>
                <!-- <input type="submit" name="browse" id="browse" value="検索" /> -->
            </form>
            
        </td>

        <td class="nowrap">
            <div style="padding-bottom: 5px;" class="nowrap smallerText">
                <b>優先度:</b>
            </div>
            <form action="./" style="margin:0px;">
                <?=hiddenForm('topic_priority');?>
                <select name="topic_priority" class="colorInput" style="width: 70px" onchange="this.form.submit(true);">
                <option value="0" selected>▼優先</option>
                    <?php foreach($GLOBALS['topic_priority'] AS $topic_priority_key=>$topic_priority_val){ ?>
                    <option value="<?=$topic_priority_key?>"<?php if($topic_priority_key==$_GET["topic_priority"]) echo " selected=\"\"selected"?>><?=$topic_priority_key?></option>
                    <?php } ?>
                </select>
                <!-- <input type="submit" name="browse" id="browse" value="検索" /> -->
            </form>
            
        </td>
        
        <td class="nowrap">
            <div style="padding-bottom: 5px;" class="nowrap smallerText">
                <b>プロジェクト:</b>
            </div>
            <form action="./" style="margin:0px;">
                <?=hiddenForm('topic_project');?>
                <select name="topic_project" class="colorInput" style="width: 120px" onchange="this.form.submit(true);">
                <option value="0" selected>▼プロジェクト</option>
                    <?php foreach($GLOBALS['topic_project'] AS $topic_project_key=>$topic_project_val){ ?>
                    <option value="<?=$topic_project_key?>"<?php if($topic_project_key==$_GET["topic_project"]) echo " selected=\"\"selected"?>><?=$topic_project_key?></option>
                    <?php } ?>
                </select>
                <!-- <input type="submit" name="browse" id="browse" value="検索" /> -->
            </form>
            
        </td>
        
        <td class="nowrap" align="right" width=90%>
            [<a href="./">TOP</a>]
            [<a href="topic_input.php">新規登録</a>]
        </td>
    </tr>
    </table>
    <div style="padding-top: 5px;"></div>
<hr size="1" class="default" noshade="noshade" />


