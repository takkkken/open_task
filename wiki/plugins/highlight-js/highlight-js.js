var highlight_js = {
    id:null,
    parms:null,
    element:null,
    html:null,

    init:function(id,parms){
        this.id=id;
        this.parms=parms;
        this.element = document.getElementById(this.id);
        var style = parms.style ? parms.style+".css" : "default.css";
        addstyle("plugins/highlight-js/styles/"+style);
        var self=this;
        loadscript("plugins/highlight-js/highlight.pack.js", function() {
            var highlightElement = self.element.getElementsByTagName('pre')[0];
            var cls = self.parms.class ? self.parms.class : "";
            var html = highlightElement.innerHTML;
            var code = "<code class='"+cls+"'>"+html+"</code>";
            highlightElement.innerHTML = code;
            hljs.highlightBlock(highlightElement.getElementsByTagName("code")[0],'    ');
        });
    },

    render:function(){
        // not needed, rendered on callback from loadscript in init
    },


}
