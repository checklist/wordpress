(function() {
    tinymce.create('tinymce.plugins.checklist', {
        init : function(editor, url) {
            editor.addButton('checklistBox', {
                title : checklist_obj.checklistBox,
                cmd : 'checklistBox',
                image : url + '/../images/checklist-icon-16.png'
            });
            
            editor.addCommand('checklistBox', function() {
                editor.windowManager.open({
                    title: 'Customize your Checklist',
                    body: [
                            {
                                type: 'label', 
                                text: checklist_obj.checklistTitle,
                                minWidth: 600,
                            },
                            {
                                type: 'textbox', 
                                name: 'title', 
                                label  : checklist_obj.title,
                                placeholder : checklist_obj.optional,
                            },
                            {
                                type: 'label', 
                                text: checklist_obj.extraOptional
                            },
                            {
                                type: 'textbox', 
                                name: 'extraTitle', 
                                label  : checklist_obj.extraTitle,
                                placeholder : checklist_obj.optional,
                            },
                            {
                                type: 'textbox', 
                                name: 'extraUrl', 
                                label  : checklist_obj.extraUrl,
                                placeholder : checklist_obj.extraUrlPlaceholder,
                            }
                        ],
                    onsubmit: function(e) {
                        var selected_text = editor.selection.getContent();
                        shortcode = '[checklist-box title="' + e.data.title + '" extraTitle="' + e.data.extraTitle + '" extraUrl="' + e.data.extraUrl + '"]' + selected_text + '[/checklist-box]';
                        editor.execCommand('mceInsertContent', 0, shortcode);
                    }
                });
            });             
        },

        createControl : function(n, cm) {
            return null;
        },

        getInfo : function() {
            return {
                longname : 'Checklist Plugin',
                author : 'Checklist',
                authorurl : 'https://checklist.com',
                infourl : 'https://checklist.com/publishers/wordpress',
                version : "1.0"
            };
        }
    });
 
    // Register plugin
    tinymce.PluginManager.add( 'checklist', tinymce.plugins.checklist );
})();