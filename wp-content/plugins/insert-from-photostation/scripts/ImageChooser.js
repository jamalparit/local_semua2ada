/**
 * $Id: ImageChooser.js,v 1.4 2011/04/08 04:55:31 chihcheng Exp $
 *
 * @author chihcheng
 * @copyright Copyright © 2004-2011, Synology Inc, All rights reserved.
 * @http://www.synology.com/
 */

var ImageChooser = function(config){
	this.config = config;
	this.initTemplates();
	this.lookup = {};
	this.url_prefix = (location.pathname.match(/\/[^\/]+/ )) ? location.pathname.match(/\/[^\/]+/ ) : '';

	/* ---- Begin side_navbar tree --- */
	this.tree = new Ext.tree.TreePanel({
		region: 'west',
		width: 150,
		minSize: 150,
		maxSize: 250,
		animate: true,
		split: true,
		loader: new Ext.tree.TreeLoader({
				dataUrl: this.url_prefix+'/wp-content/plugins/insert-from-photostation/include/synoimg.php',
				listeners: {
					'load': { fn:function(loader,node,response){
							if ('null' == response.responseText) {
								Ext.Msg.alert(chooser_error, chooser_no_public_folder, function(btn){
									if (btn == 'ok'){
										this.win.close();
  			  						}
								},this);
							}
						}, scope:this, single:true }
				}
			}),
		autoScroll: true,
		containerScroll: true,
		rootVisible:false,
		root: new Ext.tree.AsyncTreeNode({
				text: 'Files', 
				cls: 'folder',
				leaf: false, 
				id: 'source',
				draggable: false
			}),
		listeners: {
				scope: this,
				'click': function(node, e) {
					this.store.removeAll();
					this.store.load({
							params: {current_dir: node.id.replace("source/", "")}
					});
				},
				'expandnode': function(node, e){
					if(node.id == 'source' && node.firstChild != null) {
						node.firstChild.select();
							this.store.load({
								params: {current_dir: node.firstChild.id.replace("source/", "")}
							});
					}
				}
		}
	});
	/* ---- End side_navbar tree --- */

	this.store = new Ext.data.JsonStore({
		url: this.url_prefix+'/wp-content/plugins/insert-from-photostation/include/synoimg.php',
		method:'POST',
		root: 'images',
		autoLoad: true,
		baseParams:{
			action	:	this.config.action
		},
		fields: [
			'name', 'url','dir','id','src',
			'display_info','thumb_width','thumb_height'
		],
		listeners: {
			'load': { fn:function(){ this.view.select(0); }, scope:this, single:true }
		}
	});

	var formatData = function(data){
		data.shortName = data.name.ellipse(15);
		this.lookup[data.id] = data;
		return data;
	};

	this.view = new Ext.DataView({
		store: this.store,
		tpl: this.thumbTemplate,
		singleSelect: true,
		overClass:'x-view-over',
		itemSelector: 'div.thumb-wrap',
		emptyText : '<div style="padding:10px;">No images</div>',
		listeners: {
			'dblclick'		: {fn:this.doCallback, scope:this},
			'beforeselect'	: {fn:function(view){
				return view.store.getRange().length > 0;
			}}
		},
		prepareData: formatData.createDelegate(this)
	});
	/* ---- Begin Combobox ---- */
	this.AlignComboBox = new Ext.form.ComboBox({
		fieldLabel	: chooser_align,
		triggerAction	: 'all',
		store	: new Ext.data.ArrayStore({
			data	: [ ['aligncenter', chooser_align_center],
						['alignleft', chooser_align_left],
						['alignright', chooser_align_right] ],
			fields	: ['returnValue', 'displayValue']
		}),
		editable	: false,
		hiddenName	: 'adj_align',
		displayField: 'displayValue',
		valueField	: 'returnValue',
		typeAhead	: true,
		inputType	: 'ext/text',
		mode		: 'local'
	});
	this.SizeComboBox = new Ext.form.ComboBox({
		fieldLabel	: chooser_thumbnail_size,
		triggerAction: 'all',
		store		: new Ext.data.ArrayStore({
					data	: [ [0, '120x120'],
								[4, '320x320'],
								[1, '640x640'],
								[5, '800x800'],
								[12,'1280x1280']],
					fields	: ['returnValue', 'displayValue']
		}),
		editable	: false,
		displayField: 'displayValue',
		valueField	: 'returnValue',
		hiddenName	: 'adj_size',
		typeAhead	: true,
		inputType	: 'ext/text',
		mode		: 'local'
	});

	/* ---- End combobox --- */

	this.win = new Ext.Window({
		//title: 'Choose an Image',
		id: 'img-chooser-dlg',
		layout	: 'border',
		y		: 20,
		width	: this.config.width,
		height	: this.config.height,
		minWidth	: this.config.width,
		minHeight	: this.config.height,
		modal	: true,
		closeAction	: 'close',
		border	: false,
		items:[
			this.tree,
			{
				id: 'img-chooser-view',
				region: 'center',
				autoScroll: true,
				items: this.view
			},{
				xtype:'fieldset',
				region: 'south',
				title: chooser_image_properties,
				autoHeight: true,
				margins : {top: 3, right: 0, bottom: 0, left: 0},
				layout:'form',
				cls: 'image-chooser-fieldset',
				items: [this.AlignComboBox,this.SizeComboBox]
			}],
		buttons: [{
				id: 'ok-btn',
				text: chooser_insert_an_image,
				handler: this.doCallback,
				scope: this
			},{
				text: chooser_cancel,
				handler: function(){ this.win.close(); },
				scope: this
			}],
		keys: {
				key: 27, // Esc key
				handler: function(){ this.win.close(); },
				scope: this
			}
		});
}

ImageChooser.prototype = {
// cache data by image name for easy lookup

	show : function(el, callback){
		this.reset();
		this.win.show(el);
		this.callback = callback;
		this.animateTarget = el;
	},
	reset : function(){
		this.view.select(0);
		this.SizeComboBox.setValue("1");
		this.AlignComboBox.setValue("aligncenter");
		},

	initTemplates : function(){
		this.thumbTemplate = new Ext.XTemplate(
			'<tpl for=".">',
	'<div class="thumb-wrap" id="{dispaly_info}">',
	'<div class="syno_img_thumb">',
	'<table align="center"><tr><td width="120" height="120" valign="center" align="center">',
	'<img src="{url}" width="{thumb_width}" height="{thumb_height}" title="{dispaly_info}"></td></tr></table></div>' ,
	'<div class="syno_img_thumb_info" title="{dispaly_info}"><nobr>{shortName}</nobr></div></div>',
			'</tpl>'
		);
		this.thumbTemplate.compile();
	},

	doCallback : function(){
		var selNode = this.view.getSelectedNodes()[0];
		var selIndex = this.view.getSelectedIndexes();
		var callback = this.callback;
		var lookup = this.lookup;
		var adj_size = this.SizeComboBox.getValue();
		var adj_align = this.AlignComboBox.getValue();
		if(this.SizeComboBox.isValid() && this.AlignComboBox.isValid() && selNode && callback){
			this.win.hide(this.animateTarget, function(){
				var data = lookup[selIndex];
				data.size = adj_size;
				data.align = adj_align;
				callback(data);
			});
			this.win.close();
		}
	},

	onLoadException : function(v,o){
		this.view.getEl().update('<div style="padding:10px;">Error loading images.</div>');
	}
};

String.prototype.ellipse = function(maxLength){
	if(this.length > maxLength){
		return this.substr(0, maxLength-3) + '...';
	}
	return this;
};

