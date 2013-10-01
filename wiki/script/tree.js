var tree = {
	
	n : 0,
	target : null,
    clickhandler: null,
	home : '',
	nodes : [],
	crumbs : [],
	activenode : '',
	activetitle : '',
	highlight : '#ccccff',
	normal: '#ffffff',
		
	updateNode : function(ref){

	        if (ref && this.nodes.length > 0) {
	        	ref = parseInt(ref);
	            this.crumbs = [];
	            this.expandnode(this.nodes[ref]);
	            this.crumbs.reverse();
	            
	            if (!(ref in this.nodes)) 
	                return;
	            if (this.activenode != '') {
	                document.getElementById(this.nodes[this.activenode].id).style.backgroundColor = this.normal;
	            }
	            this.activenode = ref;
	            this.activetitle = this.nodes[ref].label;
	            var selectednode = document.getElementById(this.nodes[ref].id);
	            
	            // TODO: this is very sloppy, need better way
	            
	            // scroll to node if not visible
	            selectednode.style.backgroundColor = this.highlight;
	            var parent = selectednode.parentNode.parentNode;
	            var toc = document.getElementById("toc");
				
	            if (parent.offsetTop - toc.scrollTop + selectednode.offsetHeight > toc.offsetHeight) 
	                toc.scrollTop = parent.offsetTop + selectednode.offsetHeight - toc.offsetHeight;
	            
	            if (toc.scrollTop > parent.offsetTop) 
	                toc.scrollTop = parent.offsetTop;
	            
	            // END TODO
	        }
	},
	
	click : function(id){
		
		this.clickhandler(id);
		this.updateNode(id);
		cl="tree";
	},
	
	getTree : function(file, t, active){
		this.nodes = [];
		this.home = '';
		if(active==null) this.activenode=''; else this.activenode = active;
		this.target = document.getElementById(t);
		
		var self = this;
	    ajax(file,
	           null,
	           function(x){
	                var xmlDoc=x.responseXML.documentElement;
					self.target.innerHTML = '';
	                tree.parseTree(xmlDoc,self.target,0);
	                if (!document.location.hash) {
	                    tree.click(tree.home); // no #page requested get home page
	                }else{
	                    if (self.activenode=='' && !(document.location.hash.substring(1)) in self.nodes) {
	                        tree.click(tree.home);
							//this.activenode =
	                    }else{
	                        tree.click(document.location.hash.substring(1));
	                    }
	                }
	           },
		    "GET",
                    false
	    );
		
	},
	
	parseTree : function(node,parent,pref){

		for (var x = 0; x < node.childNodes.length; x++) {

			var el = node.childNodes[x];
			if (el.nodeName == 'folder') {
				if(this.home==''){
					this.home = el.getAttribute('ref');
					//this.activenode = el.getAttribute('ref');
				} 
				this.n++;
				var f = document.createElement("div");
				f.className = "node";
				var html = "<div style='overflow:hidden;width:1000px;'><div class='toggle closed' id='toggle"+el.getAttribute('ref')
				            +"' onclick='tree.togglenode("+el.getAttribute('ref')+")'></div><div id='"+"treelabel"
							+el.getAttribute('ref')+"' class='closelabel' onclick='tree.click(\""
							+el.getAttribute('ref')+"\");' onselectstart='return false;' ondblclick='tree.togglenode("+el.getAttribute('ref')+")'>"
							+el.getAttribute('label')+"</div></div>";
				var b = document.createElement("div");
				b.id = "treenode"+el.getAttribute('ref');
				f.innerHTML = html;		
				f.appendChild(b);	
				b.style.display = "none";	
				
				if(el.getAttribute('ref') != "")
					this.nodes[el.getAttribute('ref')] = {"ref": el.getAttribute('ref'), "id" : "treelabel"+el.getAttribute('ref'), label : el.getAttribute('label'),"pid" : parent.id, "owner" : pref};
				
			    this.parseTree(el,b,el.getAttribute('ref'));
				parent.appendChild(f);
			}
			
			if (el.nodeName == 'leaf') {
				if(this.home=='') this.home = el.getAttribute('ref');
				this.n++;
				var l = document.createElement("div");
				l.innerHTML = "<div style='overflow:hidden;width:1000px;'><div id='"+"treelabel"+el.getAttribute('ref')+"' class='leaf' onclick='tree.click(\""
							+el.getAttribute('ref')+"\");' onselectstart='return false;'>"
							+el.getAttribute('label')+"</div></div>";
				var ref = l.getAttribute('ref');
				parent.appendChild(l);
				if(el.getAttribute('ref') != "")
					this.nodes[el.getAttribute('ref')] = {id : "treelabel"+el.getAttribute('ref'), label : el.getAttribute('label'),pid : parent.id, owner : pref};
			}

		}
		
	},
	
	togglenode : function (id){
		var n = document.getElementById("treenode"+id);
		var s = document.getElementById("treelabel"+id);
		var t = document.getElementById("toggle"+id);
		if(n.style.display=='block'){
			n.style.display =  'none'; 
			s.className="closelabel";
            t.className="toggle closed"
		}else{
			n.style.display =  'block';
			s.className="nodelabel";
            t.className="toggle open"
		}
	},

	expandnode : function (node){
		if(!node) return;
		this.crumbs.push(node);
		if(node.owner == 0) return; // no more parents
		var n = document.getElementById(node.pid);
		var id = node.pid.substring(8); // treenodexxx
		var s = document.getElementById("treelabel"+id);
		var t = document.getElementById("toggle"+id);

		n.style.display =  'block';
		s.className="nodelabel";
        t.className="toggle open"

		this.expandnode(this.nodes[node.owner]);
	}
	
};
