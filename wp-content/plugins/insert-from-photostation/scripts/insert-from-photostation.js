Ext.onReady(function() {
	Ext.getBody().removeClass('ext-ie6');
});
var addExtImage = {
	width : '',
	height : '',
	align : '',
	src : '',
	title : '',
	insert : function() {
		var t = this, html, cls, title = '', alt = '', caption = '';
		if ( '' == t.src || '' == t.width )
			return false;
		if ( t.title ) {
			title = t.title.replace(/'/g, '&#039;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
			alt = title;
			title = ' title="'+title+'"';
		}
		if ( t.align ) {
			cls = ' class="'+t.align+'"';
		}
		html = '<img alt="'+alt+'" src="'+t.src+'"'+title+cls+' width="'+t.width+'" height="'+t.height+'" />';
	//	if ( caption )
	//		html = '[caption id="" align="'+t.align+'" width="'+t.width+'" caption="'+caption+'"]'+html+'[/caption]';
		var win = window;
		win.send_to_editor(html);
		return false;
	},
	resetImageData : function() {
		var t = addExtImage;
		t.width = t.height = '';
	},
	updateImageData : function() {
		var t = addExtImage;
		t.width = t.preloadImg.width;
		t.height = t.preloadImg.height;
		t.insert();
	},
	getImageData : function(data) {
		var t = addExtImage;
		t.preloadImg = new Image();
		t.preloadImg.onload = t.updateImageData;
		t.preloadImg.onerror = t.resetImageData;
		t.src = data.src.replace(/type=[0-2]/g,'type='+data.size);
		t.preloadImg.src = t.src;
		t.align = data.align;
		t.title = data.name;
	},
	onClickSelectSynoImage : function() {
		var t = addExtImage;
		var chooser = new ImageChooser({action: "get_album_photo", width: 600, height: 470});
		chooser.show(null, t.getImageData);
	}
}
