## Example of DataTables Searchpanes null issue.

Using search panes with null values.  The server side search doesn't work properly.

A copy of the editor library with bootstrap 4 is required.  Please download and place it in the root and name it Editor-PHP.zip.

Run the following:

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

* click the "null" option in the search pane and verify there are no matching records found


