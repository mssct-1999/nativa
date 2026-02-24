for model in app/Models/*.php; do
    name=$(basename "$model" .php)
    php artisan make:controller ${name}Controller --resource --model=$name
done