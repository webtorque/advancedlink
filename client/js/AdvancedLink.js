(function ($) {
    $.entwine('ss', function ($) {
        $('#Form_ItemEditForm_LinkType_Holder').entwine({
            onadd: function () {
                this.toggleFields();
            },
            onchange: function () {
                this.toggleFields();
            },
            toggleFields: function () {
                if (this.find('input:checked').length < 1) {
                    this.find('input').first().attr('checked', true);
                }

                var value = this.find('input:checked').val(),
                    internal = this.closest('.tab').find('#Form_ItemEditForm_PageID_Holder'),
                    external = this.closest('.tab').find('#Form_ItemEditForm_Link_Holder'),
                    file = this.closest('.tab').find('#Form_ItemEditForm_File_Holder'),
                    newTab = this.closest('.tab').find('#Form_ItemEditForm_TargetBlank_Holder');
                popup = this.closest('.tab').find('#Form_ItemEditForm_PopUpID_Holder');

                switch (value) {
                    case 'Internal':
                        external.hide();
                        file.hide();
                        popup.hide();
                        internal.show();
                        newTab.show();
                        break;
                    case 'External':
                        file.hide();
                        popup.hide();
                        internal.hide();
                        external.show();
                        newTab.show();
                        break;
                    case 'File':
                        popup.hide();
                        internal.hide();
                        external.hide();
                        newTab.hide();
                        file.show();
                        break;
                    case 'PopUp':
                        popup.show();
                        internal.hide();
                        external.hide();
                        newTab.hide();
                        file.hide();
                        break;
                    default:
                        popup.hide();
                        file.hide();
                        internal.hide();
                        newTab.hide();
                        external.show();
                }
            }
        });
    });
}(jQuery));