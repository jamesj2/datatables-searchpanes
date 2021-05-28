<?php

namespace App\Http\Controllers\Datatable;

use
    DataTables\Database,
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Format,
    DataTables\Editor\Mjoin,
    DataTables\Editor\Options,
    DataTables\Editor\Upload,
    DataTables\Editor\Validate,
    DataTables\Editor\ValidateOptions;
use DataTables\Editor\SearchPaneOptions;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class UploadManyController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
//        $db = DB::connection('sqlite')->getPdo();

        $db = new Database([
            "type" => ucwords(config('database.connections.sqlite.driver')),
            "user" => "",
            "pass" => "",
            "db" => config('database.connections.sqlite.database'),
            "dsn" => ""
        ]);

        $uploadPath = public_path('upload');
        $adapter = new Local($uploadPath,
            LOCK_EX,
            Local::SKIP_LINKS,
            [
                'file' => [
                    'public' => 0744,
                    'private' => 0700,
                ],
                'dir' => [
                    'public' => 0755,
                    'private' => 0700,
                ]
            ]);
        $filesystem = new Filesystem($adapter);

        // Build our Editor instance
        $editor = Editor::inst( $db, 'users', 'id' )
            ->fields(
                Field::inst( 'users.id', 'u.id' )
                    ->set( Field::SET_NONE ),
                Field::inst( 'users.name', 'u.name' )
                    ->searchPaneOptions(
                        SearchPaneOptions::inst()
                            ->table("users")
                            ->value("name")
                            ->label("name")->render(function ($str) {
                                return $str.' display';
                            })
                    )
                    ->validator( Validate::notEmpty( ValidateOptions::inst()
                        ->message( 'A name is required' )
                    ) ),
                Field::inst( 'users.email', 'u.email' )
                    ->searchPaneOptions(
                        SearchPaneOptions::inst()
                    )
                    ->validator( Validate::email(
                        ValidateOptions::inst()
                            ->allowEmpty( false )
                            ->optional( false )
                    ) ),
                Field::inst( 'users.exclude', 'u.exclude' )
                    ->searchPaneOptions(
                        SearchPaneOptions::inst()
                    )
                    ->validator( Validate::values(['Y', 'N']) )
            )
            ->join(
                Mjoin::inst( 'files' )
                    ->link( 'users.id', 'users_files.user_id' )
                    ->link( 'files.id', 'users_files.file_id' )
                    ->fields(
                        Field::inst( 'id' )
                            ->upload( Upload::inst( function ( $file, $id ) use ($filesystem, $db, $uploadPath) {
                                $pathInfo = pathinfo($file['name']);
                                $webPath = 'upload/'.$id.'.'.strtolower($pathInfo['extension']);
                                $systemPath = $id.'.'.strtolower($pathInfo['extension']);

                                $stream = fopen($file['tmp_name'], 'r+');
                                $filesystem->writeStream(
                                    $systemPath,
                                    $stream
                                );
                                if (is_resource($stream)) {
                                    fclose($stream);
                                }

                                $mimetype = $filesystem->getMimetype($systemPath);

                                $db->update(
                                    'files',
                                    [
                                        'system_path' => $systemPath,
                                        'web_path' => $webPath,
                                        'mime_type' => $mimetype
                                    ],
                                    function ($q) use ($id) {
                                        $q->where('id', $id);
                                    }
                                );

                                return $id;
                            } )
                                ->db( 'files', 'id', array(
                                    'filename'    => Upload::DB_FILE_NAME,
                                    'filesize'    => Upload::DB_FILE_SIZE,
                                    'web_path'    => '',
                                    'mime_type'    => '',
                                ) )
                                // THIS IS REMOVING IMAGES FROM UNFINISHED EDITS
                                ->dbClean( 'users_files.file_id', function ( $data ) use ($filesystem, $db) {
                                    for ($i = 0, $ien = count($data); $i < $ien; $i++) {
                                        $id = $data[$i]['id'];
                                        $res = $db->select(
                                            'files',
                                            ['system_path'],
                                            function ($q) use ($id) {
                                                $q->where('id', $id, '=');
                                            }
                                        );
                                        $record = $res->fetch();
                                        $filesystem->delete($record['system_path']);
                                    }

                                    // tell Editor remove the rows from the database
                                    return true;
                                } )
                                // THIS WORKS
//                                ->dbClean( function ( $data ) use ($filesystem, $db) {
//                                    for ($i = 0, $ien = count($data); $i < $ien; $i++) {
//                                        $id = $data[$i]['id'];
//                                        $res = $db->select(
//                                            'files',
//                                            ['system_path'],
//                                            function ($q) use ($id) {
//                                                $q->where('id', $id, '=');
//                                            }
//                                        );
//                                        $record = $res->fetch();
//                                        $filesystem->delete($record['system_path']);
//                                    }
//
//                                    // tell Editor remove the rows from the database
//                                    return true;
//                                } )
                                ->validator( Validate::fileSize( 5242880, 'Files must be smaller that 5MB' ) )
                                ->validator( Validate::fileExtensions( array( 'png', 'jpg', 'jpeg', 'gif' ), "Please upload an image" ) )
                            )

                    )
            )
            ->debug((env('APP_ENV') === 'local') ? true : false);
        ;
        return response()
            ->json($editor->process($_POST)->data());
    }
}
