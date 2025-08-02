php artisan make:module FileManager
composer dump-autoload
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear


# Creating a Simple Public Chat with Laravel 12, PHP 8.4, and Broadcast (Reverb)
- php artisan make:event ChatPublicMessage