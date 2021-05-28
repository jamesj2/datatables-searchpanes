<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>DT Upload Many issue</title>
        <link type="text/css" rel="stylesheet" href="{{ mix('css/app.css') }}">
        <link type="text/css" rel="stylesheet" href="{{ mix('css/datatables.css') }}">

        <script src="{{ mix('js/app.js') }}"></script>
        <script src="{{ mix('js/uploadmany.js') }}"></script>
    </head>
    <body>
    <div class="container m-5 p-2 border">
        <table id="uploadmany" class="table table-striped table-bordered" cellspacing="0">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Phone #</th>
                <th>Excluded</th>
                <th>Image</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Phone #</th>
                <th>Excluded</th>
                <th>Image</th>
            </tr>
            </tfoot>
        </table>
    </div>
    <div id="editForm" class="container-fluid">
        <div class="row">
            <div class="col col-ie mb-3">
                <div class="card card-block h-100">
                    <div class="card-header">
                        Branch
                    </div>
                    <div class="card-body">
                        <editor-field name="u.name"></editor-field>
                        <editor-field name="u.email"></editor-field>
                        <editor-field name="u.site"></editor-field>
                        <editor-field name="u.exclude"></editor-field>
                        <editor-field name="files[].id"></editor-field>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </body>
</html>
