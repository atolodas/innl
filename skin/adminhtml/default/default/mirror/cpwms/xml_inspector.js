if(typeof xml_inspector=='undefined') {
    var xml_inspector = {};
}

xml_inspector.Log = Class.create();
xml_inspector.Log.prototype = {
	initialize: function(formId, postUrl){
		this.postUrl = postUrl;
		Event.observe($(formId), 'submit', this.query.bindAsEventListener(this));
	},
   	refresh: function(){
		this.query();
   	},
   	purge: function(type){
   		if(confirm("ATTENTION: This operation will delete all files in the directory.\nAre you sure?")){
			new Ajax.Request(this.postUrl.replace('tailFile', 'purge'), {
			  method: 'get',
			  parameters: {dir:type},
			  onSuccess: function(transport) {
			    window.location.reload();
			  }
			});
   		}
   	},
   	query: function(ev){

		if(typeof ev != 'undefined'){
			Event.stop(ev);
		}

   		if($('file_inspector_dl').getValue() == '-'){
   			alert('Please select a file.');
   			return false;
   		}

   		var lines = $$('input[name="devtools_show"]')[0].getValue();
   		if(!parseInt(lines)){
   			alert('Please enter a valid number of lines.');
   			return false;
   		}

		new Ajax.Request(this.postUrl, {
		  method: 'get',
		  //loaderArea: false,
		  parameters: {file: $('file_inspector_dl').getValue(), grep:$$('input[name="grep"]')[0].getValue(), devtools_show:lines },
		  onSuccess: function(transport) {
		    $('log-console').update(transport.responseText);
		  }
		});
   	}
}

xml_inspector.Xml = Class.create();
xml_inspector.Xml.prototype = {
    initialize: function(formId, postUrl){
        this.postUrl = postUrl;
        Event.observe($(formId), 'submit', this.query.bindAsEventListener(this));
    },
    refresh: function(){
        this.query();
    },
    query: function(ev){

        if(typeof ev != 'undefined'){
            Event.stop(ev);
        }

        if($('file_inspector_dl').getValue() == '-'){
            alert('Please select a file.');
            return false;
        }

        new Ajax.Request(this.postUrl, {
            method: 'get',
            parameters: {file: $('file_inspector_dl').getValue()},
            onSuccess: function(transport) {
                $('xml-console').update(transport.responseText);
            }
        });
    },
    view_file: function(filepath, basedir){
        var path = filepath.split('/');
        if(path[0] == 'inbound'){
            nodes = $('file_inspector_dl').childNodes[1];
        }
        else if(path[0] == 'outbound'){
            nodes = $('file_inspector_dl').childNodes[2];
        }

        for(var i = 0; i < nodes.childNodes.length; i++){
            var nodepath = nodes.childNodes[i].value.split('/');
            if(nodepath[nodepath.length-1] == path[1]){
                nodes.childNodes[i].selected = '1';
            }
        }

        new Ajax.Request(this.postUrl, {
            method: 'get',
            parameters: {file: basedir+filepath},
            onSuccess: function(transport) {
                $('xml-console').update(transport.responseText);
            }
        });
    }
}