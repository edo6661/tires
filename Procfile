web: php artisan serve --host=0.0.0.0 --port=$PORT
queue: php artisan queue:work redis --tries=3 --timeout=90 --sleep=3 --max-jobs=1000
