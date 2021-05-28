## Example of DataTables Upload Many issue with dbClean()

When using dbClean with the tableField parameter it causes images to be removed with field validation fails.

A copy of the editor library with bootstrap 4 is required.  Please download and place it in the root and name it Editor-PHP.zip.

Run the following;
 
```shell script
cp .env.example .env
touch database/database.sqlite
npm ci
npm run development
composer install
php artisan key:generate
php artisan migrate:refresh --seed
php artisan serve
```

Then open http://localhost:8000.  Follow the steps below to recreate issue.  

* edit an entry
* upload image
* clear the name field
* click submit
* enter a name
* click submit
* reopen edit to verify image has been removed

See app/Http/Controllers/Datatable/UploadManyController.php @ line 115


