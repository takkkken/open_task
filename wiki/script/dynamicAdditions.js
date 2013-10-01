/*
 This file is for additional dynamic elements added after the original hard coded index.php elements
 Elements are created dynamically to allow existing users to upgrade without changing index.php.
 */
var additions = function () {
    var self = null;
    return {
        // controls
        leftTabs:null,
        rightTabs:null,

        // elements (docks)
        _rightTabElement:null, // created here
        leftTabElement:null,

        rightTabPane:null,
        leftTabPane:null,

        // movable tabs
        searchtab:null,
        indextab:null,
        contentstab:null,

        // buttons
        dockbutton:null,
        undockbutton:null,


        addElements:function () {
            self = this;
            this.createLeftButton();
            // remove clear

            var control = document.getElementById('control');
            var clear = control.childNodes[control.childNodes.length > 9 ? 9 : 4]; // ie vs the world
            control.removeChild(clear);


            this._rightTabElement = this.createDiv('control', '',
                {"attributes":[
                    {"attr":"id", "val":"rightDock"}
                ]
                });

            control.appendChild(clear); // put it back

            this.searchtab = document.getElementById('ctab3');
            this.indextab = document.getElementById('ctab2');
            this.contentstab = document.getElementById('ctab1');


        },

        createLeftButton:function () {
            self.dockbutton = self.createDiv('tabdiv', '&nbsp;',
                {"attributes":[
                    {"attr":"id", "val":"dockRightButton"},
                    {"attr":"title", "val":"Dock index and search to the right"}
                ]
                });
            self.dockbutton.onclick = self.dockRight;

        },

        dockLeft:function () {
            // reverse order to preserved indices.
            self.rightTabPane.removeChild(self.indextab);
            var indexLabel = self.rightTabs.getLabel(1);
            self.rightTabs.remove(1);

            self.rightTabPane.removeChild(self.searchtab);
            var searchLabel = self.rightTabs.getLabel(0);
            self.rightTabs.remove(0);

            var contentsLabel = self.leftTabs.getLabel(0);
            self.leftTabs.remove(0);
            self.leftTabPane.appendChild(self.indextab);
            self.leftTabPane.appendChild(self.searchtab);

            // recreate the tabs
            self.leftTabs = tabs();
            self.leftTabs.create(
                {
                    name:"tabs",
                    target:"tabdiv",
                    width:"100%",
                    height:"100%",
                    info:[
                        {label:contentsLabel, content:"ctab1", foc:""},
                        {label:indexLabel, content:"ctab2", foc:"index"},
                        {label:searchLabel, content:"ctab3", foc:"keyword"}
                    ]
                }
            );
            self.leftTabs.tabs[0].onclick = tabchange;
            self.leftTabs.tabs[1].onclick = tabchange;


            self._rightTabElement.style.display = 'none';
            self.createLeftButton();

            pack();
            self.rightTabPane.removeChild(self.undockbutton);
            self._rightTabElement.removeChild(self.rightTabPane);
            tab1 = self.leftTabs; // TODO: why is this not by reference???
        },

        dockRight:function () {


            // remove from left pane
            self.leftTabPane.removeChild(self.searchtab);
            var searchLabel = self.leftTabs.getLabel(2); // keep language
            self.leftTabs.remove(2);

            self.leftTabPane.removeChild(self.indextab);
            var indexLabel = self.leftTabs.getLabel(1);
            self.leftTabs.remove(1);

            // create in right pane
            self.rightTabPane = self.createDiv('rightDock', '',
                {"attributes":[
                    {"attr":"id", "val":"rightTabPane"}
                ]}
            );
            self.rightTabPane.className = 'righttabpane';
            self.rightTabPane.appendChild(self.searchtab);
            self.rightTabPane.appendChild(self.indextab);
            self._rightTabElement.appendChild(self.rightTabPane);
            self._rightTabElement.style.display = 'block';
            self.rightTabs = tabs();
            self.rightTabs.create(
                {
                    name:"righttabs",
                    target:"rightTabPane",
                    width:"100%",
                    height:"100%",
                    info:[
                        {label:searchLabel, content:"ctab3", foc:"keyword"},
                        {label:indexLabel, content:"ctab2", foc:"index"}
                    ]
                }
            );
            this.style.display = 'none';
            self.rightTabs.tabs[1].onclick = tabchange;
            pack();
            self.undockbutton = self.createDiv('rightTabPane', '&nbsp;',
                {"attributes":[
                    {"attr":"id", "val":"dockLeftButton"},
                    {"attr":"title", "val":"Dock index and search to the left"}
                ]
                });
            self.undockbutton.onclick = self.dockLeft;
            self.leftTabPane.removeChild(self.dockbutton);
            self.leftTabs.setTab(0);
            document.getElementById('keyword').focus(); //set focus to search field
            tab1 = self.leftTabs;
            tab2 = self.rightTabs;


        },

        createDiv:function (target, html, attr) {
            var newdiv = document.createElement("div");
            newdiv.innerHTML = html;
            for (var a = 0; a < attr.attributes.length; a++) {
                var attribute = attr.attributes[a];
                newdiv.setAttribute(attribute.attr, attribute.val);
            }
            document.getElementById(target).appendChild(newdiv);

            return newdiv;
        }
    };
};
