XEeditor.tools.define({
    id : 'editortool/daummap@daummap',
    events: {
        iconClick: function(targetEditor, cbAppendToolContent) {

            var cWindow = window.open(daumToolURL.get('popup'), 'createPopup', "width=750,height=930,directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no");

            $(cWindow).on('load', function() {
                cWindow.targetEditor = targetEditor;
                cWindow.appendToolContent = cbAppendToolContent;
            });
        },
        elementDoubleClick: function() {

        },
        beforeSubmit: function(targetEditor) {
            $(targetEditor.document.$.querySelectorAll('[data-daummap]')).empty().css({background: ''});
            targetEditor.updateElement();
        },
        editorLoaded: function(targetEditor) {
            var editorWindow = targetEditor.window.$;

            if($(targetEditor.document.$.querySelectorAll('[data-daummap]')).length > 0) {
                $(targetEditor.document.$.querySelectorAll('[data-daummap]')).daumMapRender({
                    win: editorWindow,
                    callback: function (el) {
                        $(el).prepend('<button type="button" class="btnEditMap" style="position:absolute;z-index:1;left:0;top:0">Edit</button>');
                    }
                });

                $(targetEditor.document.$.querySelectorAll('[data-daummap]')).on('click', '.btnEditMap', function() {
                    var cWindow = window.open(daumToolURL.get('edit_popup'), 'editPopup', "width=750,height=930,directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no");
                    var $this = $(this);

                    $(cWindow).on('load', function() {
                        cWindow.targetEditor = targetEditor;
                        cWindow.$targetDom = $this.parents("[data-daummap]");
                    });
                });

            }
        }
    },
    props: {
        name: 'DaumMap',
        options: {
            label: 'Daum Map',
            command: 'openDaumMap'
        },
        addEvent: {
            doubleClick: false
        }
    }
});