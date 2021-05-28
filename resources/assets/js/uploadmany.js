import "datatables.net";
import "datatables.net-bs4/js/dataTables.bootstrap4.js";
import "datatables.net-select/js/dataTables.select.js";
import "datatables.net-select-bs4/js/select.bootstrap4";
import "datatables.net-buttons/js/dataTables.buttons.js";
import "datatables.net-buttons-bs4/js/buttons.bootstrap4.js";
import "datatables.net-editor-bs4";
import "datatables.net-searchpanes-bs4";
import "datatables.net-searchpanes-bs4/css/searchPanes.bootstrap4.css";

$(document).ready(function() {
    let editor = new $.fn.dataTable.Editor({
        ajax: "/datatable/uploadmany",
        table: "#uploadmany",
        template: "#editForm",
        fields: [
            {
                label: "Name:",
                name: "u.name"
            },
            {
                label: "Email:",
                name: "u.email"
            },
            {
                label: "Exclude:",
                name: "u.exclude",
                type: "select",
                unselectedValue: 'N',
                options: [{ label: "No", value: 'No' },{ label: "Yes", value: 'Y' }],
            },
            {
                label: "Images:",
                name: "files[].id",
                type: "uploadMany",
                display: function(fileId, counter) {
                    return (
                        '<img src="' +
                        editor.file("files", fileId).web_path +
                        '"/>'
                    );
                },
                noFileText: "No images"
            }
        ]
    });

    let options = {
        deferRender: true,
        dom: "PBftpi",
        ajax: {
            url: "/datatable/uploadmany",
            type: "POST"
        },
        columns: [
            { data: "u.id" },
            { data: "u.name" },
            { data: "u.email" },
            { data: "u.exclude" },
            {
                data: "files",
                render: function(d) {
                    return d.length ? d.length + " image(s)" : "No image";
                },
                title: "Image"
            }
        ],
        orderCellsTop: true,
        fixedHeader: true,
        pageLength: 25,
        select: true,
        serverSide: true,
        stateSave: false,
        buttons: [
            { extend: "create", editor: editor },
            { extend: "edit", editor: editor },
            { extend: "remove", editor: editor }
        ]
    };

    const table = $("#uploadmany").DataTable(options);

    // Display the buttons
    new $.fn.dataTable.Buttons(table, [
        { extend: "create", editor: editor },
        { extend: "edit", editor: editor },
        { extend: "remove", editor: editor }
    ]);

    table
        .buttons()
        .container()
        .appendTo($(".col-md-6:eq(0)", table.table().container()));
});
