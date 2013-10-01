var sfa_tracker = {
	id:null,
	parms:null,
	element:null,
	
	init:function(id,parms){
		this.id=id;
		this.parms=parms;
		this.element = document.getElementById(this.id);
		addstyle("plugins/sfa-tracker/sfa-tracker.css");
	},
	
	render:function(){
		var feed = "plugins/sfa-tracker/sfa-tracker.php?project="+this.parms.project+"&tracker="+this.parms.tracker;
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
