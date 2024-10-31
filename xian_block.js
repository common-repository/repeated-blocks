(function() {

    tinymce.PluginManager.add('xb_addblockshortcodes', function( editor )
    {
        var shortcodeValues = [];
        jQuery.each(shortcodes_button, function(i)
        {
            shortcodeValues.push({text: shortcodes_button[i], value: shortcodes_button[i]});
        });

        editor.addButton('xb_addblockshortcodes', {
            type: 'listbox',
            text: 'Add Blocks',
            onselect: function(e) {
                var v = e.target.settings.value;
                tinyMCE.activeEditor.selection.setContent( '[xian_block section="' + v + '"]' );

            },
            values: shortcodeValues
        });
    });
})();