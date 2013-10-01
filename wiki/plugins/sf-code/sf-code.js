var sf_code = {
	id:null,
	parms:null,
	element:null,
	
	init:function(id,parms){
		this.id=id;
		this.parms=parms;
		this.element = document.getElementById(this.id);
		addstyle("plugins/sf-code/sf-code.css");
	},
	
	render:function(){
		var feed = "plugins/sf-code/sf-code.php?type="+this.parms.type+"&project="+this.parms.project+"&start="+this.parms.start;
        var self = this;
		this.element.innerHTML = "<img src='images/system/bigrotation2.gif' />"
        ajax(feed,null,
        function(x){
            self.element.innerHTML = x.responseText;
        },
        "GET"
        );
		
	}
}
