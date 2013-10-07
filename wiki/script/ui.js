var VERSION = "Wiki Web Help Version 0.3.15";
var ie = document.all ? true : false;
var splitting = false;
var a; 					// left split pane
var s; 					// splitter
var b; 					// right pane
var c; 					// split pane control
var textbox; 			// search input
var resultbox; 			// search result
var bt; 				// border top
var br; 				// right
var bl; 				// left
var bb; 				// bottom
var brd;                // rigth dock
var cl;					// keep track of click
var lang;				// the current language
var user;				// who is logged on
var uid;				// their id
var ip;					// the current user's ip address
var clipboard;			// for cutting and pasting of folders
var imageclip;			// for pasting images
var currenttitle;		// hoder for image uploads
var tab1 ;
var tab2;
var language;			// language object that holds the translations
var plugin_urls;		// hold loaded scripts here to not duplicate
var loaded_plugins;		// script is fully loaded
var unrendered;			// holder for multiple use of same plugin on page
var css_urls;			// hold loaded style sheets to not duplicate
var _replaceContext = false; 
var haveindex = false;		// flag to control index loading
var help;
var rigthDock;

function loading(){
	lang='';
	window.onresize = pack;
	document.onmousemove=mv;
	document.onmouseup = dmu;
	document.body.onmousedown = ContextMouseDown;
	document.body.oncontextmenu = treecontext;

	a = document.getElementById("pane_a");
	b = document.getElementById("pane_b");
	s = document.getElementById("splitter");
	c = document.getElementById("control");
	help = document.getElementById("help");

    tree.clickhandler = clickhandler;

	plugin_urls = [];
	loaded_plugins = [];
	css_urls = [];
	
	tab1 = tabs();
	tab1.create(
		{
			name : "tabs",
			target:"tabdiv",
			width : "100%",
			height : "100%",
			info:[
				{label:"Contents" , content: "ctab1",foc : ""},
				{label:"Index" , content: "ctab2",foc : "index"},
				{label:"Search" , content: "ctab3", foc : "keyword"}
			]
		}
	);
	tab1.tabs[0].onclick = tabchange;
	tab1.tabs[1].onclick = tabchange;
	
	languages();

	document.getElementById('adminmenu').style.display = 'none';

	user = "anonymous";
	uid = 0;
	
	cl="tree";
	clipboard = '';
	imageclip = '';
	crumbs();
    
    // is user logged in?  This would happen on page refresh
    ajax('handlers/logincheck.php',null, 
        function(x){
            var obj = eval('(' + x.responseText + ')');
            ip=obj.ip;
            if(obj.response=='ok')
                loginresponse(obj);
            else
                logout(true); // this only sets the menu, we already know server status

        }
    );

    // <iframe id="printFrame" style="display:none"></iframe>
    var printFrame = document.createElement("iframe");
    printFrame.setAttribute("id", "printFrame");
    printFrame.setAttribute("style", "display:none");
    document.getElementsByTagName('body') [0].appendChild(printFrame);

    /* dynamic elements below, later additions that allow users to keep existing index.php file */
    loadscript("script/dynamicAdditions.js", function() {
        var additional = additions();
        additional.leftTabs = tab1;
        additional.rightTabs = tab2;
        additional.leftTabPane = document.getElementById('tabdiv');
        additional.addElements();

        rigthDock = document.getElementById('rightDock');
        pack();
    });
}

function togglediff(to, from, page, dtype,a){
	var el = document.getElementById('revdiff_'+from);
	var link = document.getElementById(a);
	if(el.style.display=='none') {
		el.style.display='block';
	    link.className='histexpand';
		if(el.innerHTML=='' && from > 0) {
			ajax('handlers/getdiff.php',
				'to='+to+'&from='+from+'&page='+page+'&type='+dtype+'&lang='+lang,
				function(x){
					el.innerHTML = x.responseText;
				}
			);
		}
	}else{
		el.style.display='none';
		link.className = 'histcollapse'
	}
}

function tabchange(){
	if(tab1.selected==0)
		tree.updateNode(tree.activenode);

	if(tab1.selected==1 && !haveindex)
		getindex();

    if(tab2 != null){
        if(tab2.selected==1 && !haveindex)
            getindex();
    }
}

function currentStyle(){
	// loop through all link elements to find theme
	var targetelement="link";
	var targetattr="href";
	var allsuspects=document.getElementsByTagName(targetelement);
	for (var i=allsuspects.length; i>=0; i--){ //search backwards within nodelist for matching elements
		if (allsuspects[i] && allsuspects[i].getAttribute(targetattr)!=null && allsuspects[i].getAttribute(targetattr).indexOf("ui.css") > -1) {
			var paths = allsuspects[i].getAttribute(targetattr).split("/");
			return paths[1];
		}
	}
}

function printIframePage(){
    var content = document.getElementById('help').innerHTML;
    var win = document.getElementById("printFrame");
    win.contentWindow.document.write("<html><head><title>Help Page</title>");
    win.contentWindow.document.write("<link rel='stylesheet' type='text/css' href='theme/"+currentStyle()+"/css/ui.css' /></head>");
    win.contentWindow.document.write("<body>"+content + "</body></html>");
    win.contentWindow.print();
}

function printpage(){
    if ((verOffset=navigator.userAgent.indexOf("Chrome"))!=-1) {
     if(parseInt(navigator.userAgent.substring(verOffset+7)) > 12) {
         printIframePage();
         return;
     }
    }
    var content = document.getElementById('help').innerHTML;
    win = window.open("","mywindow","width=500,height=500");
    win.document.open();
    win.document.write("<html><head><title>Help Page</title>");
    win.document.write("<link rel='stylesheet' type='text/css' href='theme/"+currentStyle()+"/css/ui.css' /></head>");
	win.document.write("<body>"+content + "</body></html>");	    
    win.document.close();
    setTimeout(function(){showPrint(win)},500);
}

function showPrint(win) {
    win.print();
    win.close();            
}

function setclipboard(id){
	clipboard = id;
}

function dummy(){}

// history related functions
function revert(rev){
	popup.show('revert.php?rev='+rev, 'revform', 'handlers/reverttorev.php?rev='+rev+'&uid='+uid+'&user='+user+'&lang='+lang, '220px','revresponse','validaterev()');
}
	
function revresponse(obj){
	if (obj.response == 'ok') {
		tree.click(obj.node);
	}else{
		alert(obj.response);
	}
}

function validaterev(){
	if(document.getElementById('revcomment').value == ''){
		alert('Please enter a comment(min 10 characters)!');
		return false;
	}
	
	return true;
}
	
function getrev(id,rt,off){
    ajax("handlers/getrev.php",
           "id="+id+"&rt="+encodeURIComponent(rt),
           function(x){
                document.getElementById('help').innerHTML = "<div style='float:right;'><a href='javascript:gethistory("+off+")'>"+language.cancel+"</a></div>"+Wiky.toHtml(x.responseText);
                plugins();
                document.getElementById('help').scrollTop = 0;
           },
           "POST"
    );
}

function gethistory(offset){
    if(offset==null) offset=0;
    ajax("handlers/history.php",
           "id="+tree.activenode+"&lang="+lang+"&offset="+offset,
           function(x){
                document.getElementById('help').innerHTML = x.responseText;
				menucontrol('history');
           }
    );
}

function getnodehistory(){
	
    ajax("handlers/nodehistory.php?lang="+lang,
           "id="+tree.activenode,
           function(x){
                var html=x.responseText;                
                document.getElementById('help').innerHTML = html;
		   }
	);
}

function profile(){
	popup.show('getprofile.php?id='+uid+'&lang='+lang, 'profile', 'handlers/updateprofile.php?id='+uid, '260px','profileresponse','validateprofile()','ppass');
}

function profileresponse(obj){
	if(obj.response != 'ok')
		alert(obj.response);	
}

function validateprofile(){
	document.getElementById('subscribe').value= document.getElementById('sub').checked;
	return true;
}

function indexkey(){
	var pane = document.getElementById('indexpane');
	var text = document.getElementById('index').value.replace(">","&gt;");
	var pattern="id=['\"](tag-"+text+"[^('|\")]*?)['\"]";

	var re = new RegExp(pattern,"i");

	var m = re.exec(pane.innerHTML);
	if (m != null) {
		var s = m[1];
		pane.scrollTop = document.getElementById(s).offsetTop;
	}
}

function getindex(){
    document.getElementById('indexpane').innerHTML = '<div style="margin:20px;"><img src="images/system/bigrotation2.gif"/></div>';
    ajax("handlers/getindex.php?lang="+lang,
	       null,
	       function(x){
	            var xmlDoc=x.responseXML.documentElement;
	            var tags = xmlDoc.getElementsByTagName('tag');
	            var target = document.getElementById('indexpane');
	            var html='';
	            for(var t=0;t<tags.length;t++){
	                var tag=tags[t];
	                var lab = tag.getAttribute('label');
	                var labid = lab.replace(/\</g,'&lt;').replace(/\>/g,'&gt;').replace(/'/g,"&#39;").replace(/\&/g,'&amp;').replace(/"/g,'&quot;');

	                html += "<div id='tag-"+lab+"' class='indextag'>"+lab+"</div>";
	                var nodes = tag.getElementsByTagName('node');
	                for (var n = 0; n < nodes.length; n++) {
	                    var anode = nodes[n];
	                    var nlab = anode.getAttribute('label');
	                    var nid = anode.getAttribute('id');
	                    html += "<div style='margin-left:20px;'><a href='javascript:indexpage("+nid+")'>"+nlab+"</a></div>";
	                }
	            }
	            
	            target.innerHTML = html;
				haveindex = true;
				indexkey();
	       },
	       "GET"
	);
}

function indexpage(id) {
    if (id != tree.activenode) {
        clickhandler(id);
    }
    tree.updateNode(id);
    cl = "index";
}

function previewcancel(title){
	document.getElementById('imgpane').style.display = 'none';
	document.getElementById('editbar').style.display = "block";
	//if(imageclip !='')  document.getElementById('imgins').style.display = "block";
}

function menucontrol(sel){
	var v = document.getElementById('viewa');
	var e = document.getElementById('edita');
	var h = document.getElementById('historya');
	var a = document.getElementById('adminmenu');
	
	var cls = "menupage";
	v.className = sel == 'view' ? cls + " select" : cls;
	e.className = sel == 'edit' ? cls + " select" : cls;
	h.className = sel == 'history' ? cls + " select" : cls;
	a.className = sel == 'admin' ? cls + " select" : cls;
}

function editpage(path){
	var pathinfo;
	var nodeid = tree.activenode;
	if (path == null) {
		pathinfo = '';
	}else {
		pathinfo = "&path="+path;
	}			
		
	ajax("handlers/editpage.php?id="+nodeid+pathinfo+"&lang="+lang+"&clip="+encodeURIComponent(imageclip),
		null,
		function(x){
			var html=x.responseText;    
			var id=tree.activenode;
			document.getElementById('help').innerHTML = html;
			document.getElementById('edittext').focus();
			menucontrol('edit');
		},
		"GET"
	);
}

function loadscript(src,callback){
	if(!ArrayContains(loaded_plugins, src)){
		var script = document.createElement("script");
		script.src = src;
		script.type="text/javascript";
		if (script.readyState){  //IE
			script.onreadystatechange = function(){
				if (script.readyState == "loaded" ||
						script.readyState == "complete"){
					script.onreadystatechange = null;
					callback();
					loaded_plugins.push(src);
				}
			};
		} else {  //Others
			script.onload = function(){
				loaded_plugins.push(src);
				callback();
			};
		}
		document.getElementsByTagName("head")[0].appendChild(script);
	}else{
		callback();
	}
}

function addstyle(s){
    if (ArrayContains(css_urls,s)) return;
    css_urls.push(s);
    var css=document.createElement("link");
    css.setAttribute("rel", "stylesheet");
    css.setAttribute("type", "text/css");
    css.setAttribute("href", s);
    document.getElementsByTagName("head")[0].appendChild(css);

}

function edit(path){
	loadscript("script/edit.js", function(){editpage(path)});
}

function sysclipboard(clip){
	imageclip = clip;
    previewcancel("");
    insertimage();
    inlinepreview();
}

function ContextMouseDown(event)
{
    // IE doesn't pass the event object
    if (event == null)
        event = window.event;

    // standard compliant or IE
    var target = event.target != null ? event.target : event.srcElement;

    // right mouse button and tree node clicked
    if (event.button == 2 && target.id.indexOf('treelabel')==0){
        _replaceContext = true;
    }   
}

function treecontext(event){ 
    if (event == null)
        var event = window.event;

    var target = event.target != null ? event.target : event.srcElement;

    // right mouse button and tree node clicked
    if (_replaceContext){
        var id=parseInt(target.id.substring(9));
		var type = 'folder';
		
        if(id != tree.activenode) // load page if not selected
		    tree.click(id);

		popup.show('foldermenu.php?target=' + id + '&t=' + type + '&lang=' + lang + '&clip=' + clipboard, 'treemenu', 'handlers/folder.php?target=' + id + '&lang=' + lang + '&uid=' + uid + '&clip=' + clipboard, '220px', 'folderresponse', 'validateFolder()');
		_replaceContext = false;
		
		return false;
	}	
    return true;       
}

function folderresponse(obj){
	if(obj.response=='ok'){
		if(clipboard == ''){
			if(obj.node==-1){ // removed node
				tree.getTree('handlers/gettree.php?lang='+lang,'tree');				
			}else{
				tree.getTree('handlers/gettree.php?lang='+lang,'tree',obj.node);				
			}
			
		}
	}else{
		alert(obj.response);
	}
	
}

function tags(){
	popup.show('tagedit.php?id='+tree.activenode+'&lang='+lang, 'tags', 'handlers/tagsave.php?id='+tree.activenode+"&uid="+uid+'&lang='+lang, '260px','tagresponse', null, 'tags_tags');
}
function tagresponse(obj){
	haveindex = false;
	gettags();
	if(tab1.selected==1)
		getindex();
}

function logout(local){
    document.getElementById('loginmenu').style.display = 'block';
    document.getElementById('logoutmenu').style.display = 'none';
    document.getElementById('profilemenu').style.display = 'none';
    document.getElementById('registermenu').style.display = 'block';
    document.getElementById('adminmenu').style.display = 'none';
    document.getElementById('status').innerHTML = 'anonymous@'+ip;
    user = 'anonymous';
    uid=-1;
    if(!local) ajax('handlers/logout.php',null,function(){editable();});

}

function loginform(){
	popup.show('login.php?lang='+lang, 'login', 'handlers/login.php', '260px','loginresponse', null, 'login_user');
}

function registerform(){
	popup.show('register.php?lang='+lang, 'register', 'handlers/register.php', '260px','registerresponse','validateRegister()', 'register_user');
}

function forgotlink() {
        document.getElementById('forgotpassword').style.display = 'none';
        document.getElementById('forgotpasslink').style.display = 'none';
        document.getElementById('username_email').innerHTML = 'email:'
        document.getElementById('loginsubmit').value = 'Reset Password:'
        document.getElementById('loginmode').value = 'reset'
}


function loginresponse(obj) {
    if (obj.response == 'ok') {
        if (!obj.forgot) {
            document.getElementById('status').innerHTML = "<span>" + obj.user + '@' + obj.ip + "</span>";
            document.getElementById('loginmenu').style.display = 'none';
            document.getElementById('logoutmenu').style.display = 'block';
            document.getElementById('profilemenu').style.display = 'block';
            document.getElementById('registermenu').style.display = 'none';
            if (obj.level == 'admin') {
                document.getElementById('adminmenu').style.display = 'block';
            } else {
                document.getElementById('adminmenu').style.display = 'none';
            }

            user = obj.user;
            uid = obj.uid;
            ip = obj.ip;
            editable(); // TODO: check this in login, need to send page and language to check
        }
    } else {
        alert(obj.response);
    }
}

function registerresponse(obj){
	loginresponse(obj);
}

function searchkey(e) {
 	e = e || window.event;   
 	var code = e.keyCode || e.which;    
	if(code == 13){     
		search();
		document.getElementById('keyword').select();
	}
}

function getip(){
    ajax("handlers/ip.php",
          null,
          function(x){
                var obj = eval('(' + x.responseText + ')');
		document.getElementById('status').innerHTML = "<span>"+user+'@'+obj.ip+"</span>";
                ip = obj.ip;
		  }
	);
	return false;	
}

function changelanguage(){
	var sel = document.getElementById('langsel');
	lang = sel.options[sel.selectedIndex].value;
	if(lang=='') return;
	var date = new Date();
	date.setTime(date.getTime()+(30*24*60*60*1000)); // expire in 30 days

    ajax("language/"+lang+".json",
          null,
          function(x){
                var obj = eval('(' + x.responseText + ')');
                document.getElementById('logina').innerHTML = obj.menu.login;
                document.getElementById('logouta').innerHTML = obj.menu.logout;
                document.getElementById('registera').innerHTML = obj.menu.register;
                document.getElementById('profilea').innerHTML = obj.menu.profile;
                document.getElementById('edita').innerHTML = obj.menu.edit;
                document.getElementById('viewa').innerHTML = obj.menu.view;
                document.getElementById('adminmenu').innerHTML = obj.menu.admin;
                document.getElementById('tagsa').innerHTML = obj.menu.edittags;
				
                document.getElementById('historya').innerHTML = obj.menu.history;
                document.getElementById('historyna').innerHTML = obj.treehistory;

                document.getElementById('searchlabel').innerHTML = obj.tabs.search;

              for(var t=0; t<tab1.tabs.length; t++) {
                  switch (t) {
                      case 0 :
                          tab1.setLabel(t,obj.tabs.contents);
                          break;
                      case 1:
                          tab1.setLabel(t,obj.tabs.index);
                          break;
                      case 2:
                          tab1.setLabel(t,obj.tabs.search);
                  }
              }
              if(tab2 != null)
                  for(var t=0; t<tab2.tabs.length; t++) {
                      switch (t) {
                          case 0 :
                              tab2.setLabel(t,obj.tabs.search);
                              break;
                          case 1:
                              tab2.setLabel(t,obj.tabs.index);
                       }
                  }


              document.getElementById('indextype').innerHTML = obj.tabs.type;
                language = obj;
				var thispage = '';
                tree.getTree('handlers/gettree.php?lang='+lang,'tree',tree.activenode);
				haveindex = false;
				if(tab1.selected==1)
	                getindex();
                if(tab2!=null){
                    if(tab2.selected == 1)
                        getindex();
                }
                crumbs();
                
				// clear search
				document.getElementById('keyword').value = '';
				document.getElementById('searchresult').innerHTML = '';
		  }
    );
	return false;
}

function languages(){
	
    ajax("language/languages.json",
          null,
          function(x){
	            var obj = eval('(' + x.responseText + ')');
	            
	            var html = '';
	            for ( var i=0; i < obj.languages.length; i++ ){
	                 html+= "<option value='"+obj.languages[i].symbol+"'>"+obj.languages[i].text+"</option>" ;
	            }   
	            var lng = document.getElementById('lang');
	            lng.innerHTML = '<select id="langsel" onchange="changelanguage();">'+html+'</select>'; // ie hack
	            var l = document.getElementById('langsel');
	            if(lang=='') {
					lang=obj.languages[0].symbol;
                    CheckForHash();
					changelanguage();
				}

	            l.value = lang;
          }
    );

	return false;
}
function getRequestObject() {
	var xmlHttp=null;
	try{
	    this.postmode = true;
		xmlHttp=new XMLHttpRequest();
	}catch (e){
	  // Internet Explorer
	  try{ 
		this.postmode = false;
		xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
	  } catch (e){
			xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
	  }
	return xmlHttp;		
}

// Resize

function pack(){
	c.style.height = "auto";
	a.style.overflow = "hidden"; // prevents flicker
	
	
    if (c.currentStyle) {
		bt = parseInt(c.currentStyle.borderTopWidth);
		br = parseInt(c.currentStyle.borderRightWidth);
		bb = parseInt(c.currentStyle.borderBottomWidth);
        bl = parseInt(c.currentStyle.borderLeftWidth);
        brd = parseInt(rigthDock.currentStyle.borderLeftWidth)
            + parseInt(rigthDock.currentStyle.borderRightWidth);
	}
	else if (window.getComputedStyle) {
		bt = parseInt(document.defaultView.getComputedStyle(c, null).getPropertyValue('border-top-width'));
		br = parseInt(document.defaultView.getComputedStyle(c, null).getPropertyValue('border-right-width'));
		bb = parseInt(document.defaultView.getComputedStyle(c, null).getPropertyValue('border-bottom-width'));
        bl = parseInt(document.defaultView.getComputedStyle(c, null).getPropertyValue('border-left-width'));
        brd = parseInt(document.defaultView.getComputedStyle(rigthDock, null).getPropertyValue('border-left-width'));
        brd += parseInt(document.defaultView.getComputedStyle(rigthDock, null).getPropertyValue('border-right-width'));
	}
	
	b.style.width = Math.max(0,c.offsetWidth - a.offsetWidth - s.offsetWidth-bl-br - rigthDock.offsetWidth - brd) + "px";
}

// Split Pane Control
var sp=300;
function dc(){
	var l = s.offsetLeft > 5 ? 0 : sp;
	sp = s.offsetLeft;
	a.style.width = Math.max(0,l) + "px";
    pack();
	
}

function md(e){
	splitting = true;
	document.body.onselectstart = function(){return false;};
}

function mv(e){
	if(!splitting) return;
	var l = ie ? event.clientX + document.body.scrollLeft : e.pageX;
	a.style.width = Math.max(0,l-c.offsetLeft - s.offsetWidth - 5) + "px";
	b.style.width = (c.offsetWidth - a.offsetWidth - s.offsetWidth-bl-br-rigthDock.offsetWidth - brd) + "px";
}

function mu(e){
	splitting = false;
	document.body.onselectstart = function(){return true;};
}

function dmu(e){
	if(!splitting) return true;
	var l = ie ? event.clientX + document.body.scrollLeft : e.pageX;
	if(s.offsetLeft > l) mu(e); 
}

// Search functions

function search(){
	textbox = document.getElementById("keyword");
	resultbox = document.getElementById("searchresult");
    resultbox.innerHTML = '<div style="margin:20px;"><img src="images/system/bigrotation2.gif"/></div>';

    ajax("handlers/search.php?lang="+lang,
          "search="+encodeURIComponent(textbox.value),
          function(x){
            var xmlDoc=x.responseXML.documentElement;
            var nodes = xmlDoc.getElementsByTagName("file");
            var list = '';
            for (var x = 0; x < nodes.length; x++) {
                var el = nodes[x];
                list += "<div class='list' onclick='searchClick(\""
                            +el.getAttribute('name')+"\")'>"
                            +el.getAttribute('title')+"</div>";
            }
            resultbox.innerHTML = list;
		  	
          }
    );

	return false;
}

function searchHighlight(html) {
    var words = textbox.value.split(" ");
    for (var i=0;i<words.length;i++){
        var word=words[i];
        // highlight the word ignoring elements
        pattern = "(" + word + ")(?=[^>]*<)";
        html = html.replace(new RegExp(pattern,"gi"), "<span style='background:yellow;display:inline-block;'>$1</span>")
    }
    document.getElementById('help').innerHTML = html;

}

function searchClick(file) {
    if (file != tree.activenode) {
        clickhandler(file,true);
    } else {
        searchHighlight(document.getElementById('help').innerHTML);
    }
    tree.updateNode(file);
    cl = "search";
}

function plugins(){ // this gets called after wiky conversion
	unrendered = []; // queue for plugins loading
	for (var p in Wiky.plugins) {
		var w = Wiky.plugins[p];
		
		unrendered.push(w); // automatically removed when rendered
		var src = 'plugins/'+w[0]+'/'+w[0]+'.js'; // path to plugin
		loadPlugin(src,w);  
		
		// not finished loading yet, remains queued
		if (!ArrayContains(loaded_plugins, src)) {
			continue;
		}
		
		renderplugin(w); // render specific instance of plugin
	}
}

function clone(o)
{
    var ClonedObject = function(){};
    ClonedObject.prototype = o;
    return new ClonedObject;
}

function renderplugin(w){
	// w[0] = plugin name, w[1] = unique id, w[2] = json parameters 
	try {
		var p = eval('(' + w[0].replace('-','_') + ')');
		var c = clone(p);	
		c.init(w[1],w[2]);
		c.render();
		ArrayRemove(unrendered,w);
		c=null;
	} catch (e) {}
}

function updateplugins(url){
	
	// this plugin has finished loading
    loaded_plugins.push(url);	
	
	// render all instances of the plugin?
	for(var p=unrendered.length-1;p>-1;p--){
		var w = unrendered[p];
		var src = 'plugins/'+w[0]+'/'+w[0]+'.js';
		
		if (!ArrayContains(loaded_plugins, src)) // different plugin finished loading
			continue;
			
		renderplugin(w); 
	}	
}

function loadPlugin(url,wp){
	// check to see if plugin has been called to load, if so no need to continue
	if (ArrayContains(plugin_urls,url)) 
		return;

	plugin_urls.push(url); // track loading calls
	
	// add plugin script
    var script = document.createElement("script");
	script.src = url;
	script.type="text/javascript";

	// once fully loaded, update instances
    if (script.readyState){  //IE
        script.onreadystatechange = function(){
            if (script.readyState == "loaded" ||
                    script.readyState == "complete"){
                script.onreadystatechange = null;
                updateplugins(url);
            }
        };
    } else {  //Others
        script.onload = function(){
			updateplugins(url);
        };
    }
	
	document.getElementsByTagName("head")[0].appendChild(script);
}


// Navigation functions

var last='';
function CheckForHash(){
	if(document.location.hash){ 
		var HashLocationName = document.location.hash;
		HashLocationName = HashLocationName.substring(1);
		if(last==HashLocationName) return;
		
		if(parseInt(HashLocationName) != tree.activenode) {
			if(tree.activenode != '')
				tree.click(HashLocationName);
		}
			
		var hashes = HashLocationName.split('-');
		if(hashes.length > 1){
			if(hashes[1].substring(0,2) != lang){
				var sel = document.getElementById('langsel');
				for(i=0;i<sel.length;i++){
					if(sel[i].value == hashes[1].substring(0,2)){
						sel.selectedIndex = i;
						
						break;
					}
				}
				lang=hashes[1].substring(0,2);
				changelanguage();
				return;
			}
		}

		anchor(HashLocationName);
		//RedirectLocation("LocationAnchor", HashLocationName, "#"+HashLocationName)
		crumbs();
		last=HashLocationName;
	}
}
function RenameAnchor(anchorid, anchorname){
	document.getElementById(anchorid).name = anchorname; //this renames the anchor
}

function RedirectLocation(anchorid, anchorname, HashName){
	var hashes = HashName.split('-');
	var anchors = anchorname.toString().split('#');
	var anch = anchors.length > 1 ? "#"+anchors[1] : '';
	RenameAnchor(anchorid, anchorname);
	document.location.hash = hashes[0]+"-"+lang+anch;

	plugins();
	anchor(HashName);
	menucontrol('view');
}

function clickhandler(id, search) {
    handler = "handlers/getpage.php?id="+parseInt(id)+"&lang="+lang;
    document.getElementById('help').innerHTML = '<div style="margin:30px;"><img src="images/system/bigrotation2.gif"/></div>';
    ajax(handler,
        null,
        function(x){
            var obj = eval('(' + x.responseText + ')');
            var html=obj.page;

            document.getElementById('help').innerHTML = Wiky.toHtml(html);
            if(search) searchHighlight(document.getElementById('help').innerHTML);
            RedirectLocation("LocationAnchor", id, "#"+id);
            showtags(obj.tags);

            var edita = document.getElementById('edita');
            var tagsm = document.getElementById('tagsm');

            if (obj.editable == "1" || obj.editable == "2") {
                edita.innerHTML = language.menu.edit;
                tagsm.style.display = "block";
            }
            else {
                if(language != null) edita.innerHTML = language.sourceview;
                tagsm.style.display = "none";
            }

        },
        "GET"
    );

}

function showtags(csv) {
    var tags = csv.split(',');
    var tagstring = '';
    for (i = 0; i < tags.length; i++) {
        var tag = tags[i];
        tagstring += ", <a href='javascript:tagselect(\"" + tag + "\")'>" + tag + "</a>";
    }
    document.getElementById('taglist').innerHTML =
        csv != '' ?
            language.menu.tags + " : " + tagstring.substring(2)
            :
            ''
    ;

}

function gettags(csv){
    ajax("handlers/gettags.php", "id=" + tree.activenode + "&lang=" + lang,
        function (x) {
            showtags(x.responseText) ;
        }

    );
}

function tagselect(tag){
	document.getElementById('index').value=tag;
	if(tab1.tabs.length == 3)
	tab1.setTab(1);
    else
        tab2.setTab(1);
	if(haveindex){
		indexkey();
	}else{
		getindex();
	}
}

function anchor(aname){
	anum = aname.substring(1);
	var pane = document.getElementById('help');
	pane.scrollTop =0;
	if(parseInt(anum) == anum) {return;} // nothing to do
	var anchs = anum.split('#');
	if(anchs.length > 1){
		var tags = document.getElementsByName(anchs[1]);
		if(tags.length > 0) pane.scrollTop = tags[0].offsetTop;
	}

}

function editable(){
    ajax("handlers/editable.php", "id=" + tree.activenode + "&lang=" + lang , function(x){
        var edita = document.getElementById('edita');
        var tagsm = document.getElementById('tagsm');
        
        if (x.responseText == "1" || x.responseText == "2") {
            edita.innerHTML = language.menu.edit;
            tagsm.style.display = "block";
        }
        else {
            if(language != null) edita.innerHTML = language.sourceview;
            tagsm.style.display = "none";
        }
    });
}

function forward(){
	history.go(1);
}

function back(){
	history.go(-1);
}

// validation

function validateRegister(){
	 // email
	var element = document.getElementById('user');
	if(element.value==''){
	  alert('Please provide a user name');
	  element.focus()
	  element.select();
	  return false;
	}
	
	var element = document.getElementById('pass');
	if(element.value==''){
	  alert('Please provide a password');
	  element.focus()
	  element.select();
	  return false;
	}
	
	var element = document.getElementById('confirm');
	if(element.value=='' || element.value != document.getElementById('pass').value){
	  alert('Password and Confirmation must match.');
	  element.focus()
	  element.select();
	  return false;
	}
	
	var element = document.getElementById('email');
	if(element.value==''){
	  alert('Please provide a valid email address');
	  element.focus()
	  element.select();
	  return false;
	}
	
	var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	if (!filter.test(element.value)) {
	  alert('Please provide a valid email address');
	  element.focus()
	  element.select();
	  return false;
	}
	
	return true;
}

function validateFolder(){
	var n = document.getElementById("fname");
	
	var ap = document.getElementById("faddpage");
	var p = document.getElementById("fpaste");
	
	var r = document.getElementById("fremovefolder");
	var rn = document.getElementById("frename");
	var c = document.getElementById("fcut");
	
	var comment = document.getElementById("commentf");

	if(ap.checked || rn.checked){
		if(n.value==''){
		  alert('Please enter a name');
		  n.focus()
		  n.select();
		  return false;
		}		
	}
	
	if(r.checked && user=='anonymous'){
	  alert('You do not have permission to remove folders!');
	  return false;		
	}
	
	if(r.checked && comment.value.length < 1){
	  alert('Please enter reason for deletion');
	  comment.focus()
	  comment.select();
	  return false;		
	}
	
	var action = document.getElementById('action');
	
	if(ap.checked) action.value = "addpage";
	if (p.checked) {
		if (clipboard == '') {
			alert("Clipboard is empty!");
			return false;
		}
		action.value = "paste";
	}
	
	if(r.checked) action.value = "remove";
	if(rn.checked) action.value = "rename";
	if (c.checked) {
		action.value = "cut";
		clipboard = tree.activenode;
	}else{
		if(action.value == 'paste') clipboard = ''; // clipboard parameter already set in treedblckl()
	}
	
	var pos = document.getElementById('position');
	if(document.getElementById('before').checked) pos.value = "before";
	if(document.getElementById('after').checked) pos.value = "after";
	
	if(document.getElementById('in').checked) pos.value = "in";		
	
	return true;
}

// utility

function pageFromPath(path){

    ajax("handlers/pageFromPath.php",
           'lang='+lang+"&path="+encodeURIComponent(path),
           function(x){
	            var id=x.responseText.replace(/^\s+|\s+$/g, ''); //trim
	
	            if(id > 0){
	                tree.click(id);
	            }else{
	                if(id==-1){
	                    edit(path);
	                }
	            }
           }
    );    
}

function adminpage(){
	loadscript("script/admin.js", 
				function(){
					ajax("admin/adminpage.php",
					   'lang='+lang+"&page="+tree.activenode,
					   function(x){
                           var version = "<div class='version'>"+VERSION+"</div>"
						   document.getElementById('help').innerHTML = version + x.responseText;
						   menucontrol('admin');
					   }
					)
				});
}

function ajax(handler,postparameters,callback,method,cache){
    if(method==null)
        method = "POST";
    if(cache==null)
        cache = true;

    // TODO: need to handle no ?a=b, for now false is selectively added
    if(cache == false)
        handler = handler + '&dummy=' + new Date().getTime();

    var xmlHttp=getRequestObject();

    xmlHttp.onreadystatechange=function (){
        if (xmlHttp.readyState == 4) {
            callback(xmlHttp);
        }
    };
    
    xmlHttp.open(method,handler,true);
    if(method == "POST") xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlHttp.send(postparameters); 
}

function crumbs(){
	var c = tree.crumbs;
	if(c.length==0) setTimeout("crumbs()",50);
	var html = '';
	for(var i=0; i< c.length; i++){
		var crumb = c[i];
		if(i>0) html+="<div class='crumbsep'>:</div>";
		var click = i==(c.length-1) ? "" : "onclick='tree.click(\""+crumb.ref+"\")'";
		var cls = i==(c.length-1) ? "endcrumb" : "crumb";
		
		html+="<div class='"+cls+"'"+click+">"+crumb.label+"</div>";
	}
	document.getElementById("crumbs").innerHTML = html;
}

function updatefldrfrm(rad){
	
    var divname = document.getElementById("fldrfrmname");
    var divap = document.getElementById("fldrfrmaddpaste");
    var divcmt = document.getElementById("fldrfrmcmt");
	
    if(rad.id=="faddpage"){
        divname.style.display="block";
        divap.style.display="block";
        divcmt.style.display="block";		
    }

    if(rad.id=="fremovefolder"){
        divname.style.display="none";
        divap.style.display="none";
        divcmt.style.display="block";       
    }

    if(rad.id=="fcut"){
        divname.style.display="none";
        divap.style.display="none";
        divcmt.style.display="none";       
    }

    if(rad.id=="fpaste"){
        divname.style.display="none";
        divap.style.display="block";
        divcmt.style.display="block";       
    }

    if(rad.id=="frename"){
        divname.style.display="block";
        divap.style.display="none";
        divcmt.style.display="block";       
    }

}

function ArrayContains(ar, value) {
	for (var i = 0;i < ar.length; i++) {
		if (ar[i] == value) {
			return true;
		}
	}	
	return false;
}

function ArrayRemove(ar, value) {
	for (var i = 0;i < ar.length; i++) {
		if (ar[i] == value) {
			ar.splice(i,1);
		}
	}	
}

var HashCheckInterval = setInterval("CheckForHash()", 250);
