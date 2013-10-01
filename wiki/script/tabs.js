var tabs = function(){

  return{
	info : null,
	target : null,
	name : null,
	height : null,
	width : null,
	tabs : [],
	selected : 0,
	
    getLabel : function(index) {
        return this.tabs[index].innerHTML;
    },
	setLabel : function(index, label){
		this.tabs[index].innerHTML = label;		
	},
	
	setTab : function(index){
        for (var t = 0; t < this.info.length; t++) {
            var tab = this.tabs[t];
            var content = document.getElementById(this.info[t].content);
            if(index==t){
                tab.className = "tabon";
                content.style.display = 'block';
                if(this.info[t].foc != "") document.getElementById(this.info[t].foc).focus();
            }else{
                content.style.display = 'none';
                tab.className = "taboff";                       
            }
        }   
		
	},

    remove : function(index) {
        this.tabs.splice(index, 1);
        this.info.splice(index, 1);
        var tab = document.getElementById(this.name+'tab'+index);
        this.target.removeChild(tab);
    },

	create : function(obj){

		this.target = document.getElementById(obj.target);
		this.info = obj.info;
		this.name = obj.name;
		this.height = obj.height;
		this.width = obj.width;
		
		this.target.style.height = this.height;
		
		for(var i=0;i<this.info.length;i++){
			var tab = document.createElement("div");
			tab.setAttribute('id',this.name+'tab'+i);
			tab.className = 'taboff';
			tab.innerHTML = this.info[i].label;
			
			var self = this;
			tab.onmouseup = function(){
				for (var t = 0; t < self.info.length; t++) {
					var tab = self.tabs[t];
					var content = document.getElementById(self.info[t].content);
					if(tab==this){
						tab.className = "tabon";
						content.style.display = 'block';
						if(self.info[t].foc != "") document.getElementById(self.info[t].foc).focus();
						self.selected = t;
					}else{
						content.style.display = 'none';
						tab.className = "taboff";						
					}
				}	
			};
			
			tab.style.height = "22px";
			this.tabs[i] = tab;
			this.target.appendChild(tab);
			
			var cont = document.getElementById(this.info[i].content);
			cont.style.left = 0;
			cont.style.display = 'none';
			cont.style.top = (tab.offsetHeight - 1) + 'px';
		}
		
		this.tabs[0].className = "tabon";
		document.getElementById(this.info[0].content).style.display='block';

	}	

  };
};


