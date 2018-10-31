var Editor = {
	init : function(){
		this.createEditor();
	},
	createEditor : function(){

		 var editorLen = $('textarea.we-editor').length;
		 var upImgLen = $('.we-btn-img').length;
		 if (editorLen == 0 && upImgLen == 0) {
		   return;
		 }

		 $.getScript('/static/kindeditor/kindeditor-all.js',function(){

		   KindEditor.basePath = '/static/kindeditor/';
		   KindEditor.create('textarea.we-editor' , {
		     height : '450px',
		     filterMode : false,
		     allowFileManager : false,
		    //  uploadJson : '/file/upload',
		    //  fileManagerJson : '/file/data',
		     formatUploadUrl : false,
				 allowImageRemote : false,
		     afterBlur : function(){
		       this.sync();
		     }
		   });

		   $('.we-btn-img').click(function(event) {

		     var editor = KindEditor.editor( {
		       basePath : '/static/kindeditor/',
		       pluginsPath : '/static/kindeditor/plugins/',
		       allowFileManager : false,
		      //  uploadJson : '/file/upload',
		      //  fileManagerJson : '/file/data',
		       formatUploadUrl : false,
					 allowImageRemote : false

		     });
		     // console.log(editor);
		     var id = $(this).attr('data-id');
		     editor.loadPlugin('image', function() {
		         editor.plugin.imageDialog({
		           imageUrl : $('#' + id).val(),
		           clickFn : function(url, title, width, height, border, align) {
		             $('#' + id).val(url);
		             $('#pre-img-' + id).html("<img src='"+url+"' height='50'>");
		             editor.hideDialog();
		           }
		         });
		       });
		   });


		 });
	}
};

module.exports = Editor;
