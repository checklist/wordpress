(function() {
    tinymce.create('tinymce.plugins.checklist', {
        init : function(editor, url) {
            editor.addButton('checklistMenu', {
                type : 'menubutton',
                image : url + '/../images/checklist-icon-16.png',
                menu: [
                    {
                        text : checklist_obj.checklistButtons,
                        onclick: function(){
                            shortcode = '[checklist-buttons save="' + checklist_obj.saveDefault + '" print="' + checklist_obj.printDefault + '" /]';
                            editor.execCommand('mceInsertContent', 0, shortcode);
                        }
                    },
                    {
                        text : checklist_obj.checklistBox,
                        onclick: function(){
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
                        }
                    }
                ]
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
                infourl : 'https://checklist.com/publishers/',
                version : "1.1"
            };
        }
    });
 
    // Register plugin
    tinymce.PluginManager.add( 'checklist', tinymce.plugins.checklist );
})();