function input_str(str,id){
    document.getElementById(id).value = str + document.getElementById(id).value;
}

function check(is_admin){
    var value = '';
    var msg = '';

    msg += blank_check('title','タイトルを入力してください');
    msg += blank_check('comment','コメントを入力してください');
    if(is_admin){
	msg += blank_check('is_admin','公開範囲を選択してください');
    }
    if (msg != '') {
        error(msg);
        return false;
    }
    user_select_all();
    return comfirm();
}

function check_update(){
    var value = '';
    var msg = '';

    msg += blank_check('title','タイトルを入力してください');
    msg += blank_check('comment','コメントを入力してください');
    if (msg != '') {
        error(msg);
        return false;
    }
    user_select_all();
    return comfirm_update();
}

function blank_check(id,str){
    var msg   = '';
    var value = '';
    value = document.getElementById(id).value;
    if (value == ''){
     msg = '\n - ' + str;
    }
	return msg;
}

function error(msg){
    msg = '入力エラーがあります。' + msg;
    msg = msg + '\n';
    alert(msg);
}

function comfirm(){
	var msg = '';
	if(!msg) msg = '登録します。';
	if(window.confirm(msg)){ 
		return true;	
	} 
	else{
		return false;
	}
}

function comfirm_update(){
	var msg = '';
	if(!msg) msg = '更新します。';
	if(window.confirm(msg)){ 
		return true;	
	} 
	else{
		return false;
	}
}


function add_select(cname,sname){
	var list_from		= document.getElementById(cname).options;
	var list_to			= document.getElementById(sname).options;
	for(i=0;i<list_from.length-1;i++)	{
		var co = list_from[i];
		if( ! co.selected || ! co.value ) continue;
		var f = false;
		var li = list_to.length - 1;
		for(j= 0;j<li;j++){
			if(list_to[j].value==co.value){
				f = true; break;
				}
			}
		if(f) continue;
		list_to[list_to.length] = new Option(list_to[li].text,list_to[li].value);
		list_to[li] = new Option(co.text,co.value,true,true);
	}
}

function remove_select(sname){
	var u = document.getElementById(sname).options;
	li = u.length - 1;
	for(i=0;i<li;i++){
		if(u[i].selected){
			u[i] = null;
			li -=1;
			i-=1;
		}
	}
}

function user_select_all(){
	var u = document.getElementById('topic_cc').options
	for(i=0;i<u.length-1;i++){
		u[i].selected = true ;
	}
	u[i].selected = false;
/*	if(u.length<2){
        alert("担当者を選択してください");
        return false;
    }*/
}

function showswich(normal,open){
    document.getElementById(normal).style.display = "none";
    document.getElementById(open).style.display   = "block";
}


function get_date(str){
	var year	= document.getElementById(str+'[year]').value;
	var month	= document.getElementById(str+'[month]').value;
	var date	= document.getElementById(str+'[date]').value;
	var result	= liveDate.get_date(year,month,date,str);
	document.getElementById(str).innerHTML = result;
}

function set_date(str,value){
	var year	= document.getElementById(str+'[year]').disabled = value;
	var month	= document.getElementById(str+'[month]').disabled = value;
	var date	= document.getElementById(str+'[date]').disabled = value;
}

var liveDate = new liveDate();
