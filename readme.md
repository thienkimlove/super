## Laravel 5.3 COD Example Project.

### Một số thay đổi của Laravel 5.3


New in Laravel 5.3

1. routers.php move from app/Http/routes.php to 2 files : `app/routers/web.php` and `app/routes/api.php`

2. Some directories in app folder like Event, Policy be removed, but when we run php artisan make:policy it will be back.


3. Query Builder using DB:: now return collection, same like Eloquent. For example in laravel 5.2

```
DB::table('users')->get(); will return an array of object.

But now in Laravel 5.3 it return a collection mean that we can using 
DB::table('users')->get()->first();
DB::table('users')->get()->count();
```
If we still want it return the array like Laravel 5.2 we can using like `DB::table('users')->get()->all()`.

4. Adding `cache()` function to helper.

In laravel 5.2 instead of using session in this way :

```
use Sesssion;

...

Session::get('foo');

Sesssion::put('foo', 'bar');
```
We can using helper function to do that. 
```
session()->get('foo');

session()->put('foo', 'bar');
```
No need using Facade Session:: because it make slowly and must `"use Sesssion;"` at start of php file.

Now in laravel 5.3 we can so the same for cache :
```
cache()->get('foo');
cache()->put('for', 'bar', 10);
```
5. Pagination View File Added.

Now when using `{{$users->links()}}` in our views to display pagination, we can modify html for pagination by running command `php artisan vendor:publish`

and go to `resources/views/vendor/pagination` to edit `boostrap-3.blade.php`

6. [SendEmail Syntax Change] (https://laracasts.com/series/whats-new-in-laravel-5-3/episodes/6?autoplay=true)

Please note that we have in our `.env`

```
MAIL_DRIVER=smtp
MAIL_HOST=mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
```

`http://mailtrap.io` is good site for developer to test send email using smtp, when we send in laravel email will go to your account at `mailtrap.io` to preview.

7. Adding `$loop` variable when we foreach a collection in views. It allow us to know item in foreach is first, last and how many items remind.

```
@foreach ($users as user)
    <li>{{ var_dump($loop); }}</li>
    <li>{{$user->name}}</li>  
@endofreach
```
8. [Laravel Passport] (https://laracasts.com/series/whats-new-in-laravel-5-3/episodes/13?autoplay=true)

9. [Laravel Scout Search] (https://laracasts.com/series/whats-new-in-laravel-5-3/episodes/15)

10. `lists` rename to `pluck`.

### Packages

```
     "barryvdh/laravel-ide-helper": "^2.2",
     "intervention/image": "^2.3",
     "intervention/imagecache": "^2.3",
     "cviebrock/eloquent-sluggable": "^4.0",
     "laravelcollective/html": "^5.3",
     "maatwebsite/excel": "^2.1",
     "predis/predis": "^1.1",
     "laracasts/flash": "^2.0",
     "laravel/socialite": "^2.0"
```
### Setup

Copy file `.env.example` => `.env`

Tạo database và thay đổi thông tin database trong file `.env`.

```
$ composer íntall

$ php artisan migrate

$ php artisan key:generate

$ chmod -R 777 public/files

//add your email to admin.

$ php artisan add:admin --email=manhquan.do@ved.com.vn
```
### Admin Authenticate and Permission.

Authentication using G+, you need to access to `https://console.developers.google.com` to create an OAuth2 
Credentials and add to `.env` :

```
#Google
GOOGLE_CLIENT_ID=315338546630-82tcmg4vgtaahkgongjrhkdofpa31plj.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=AlG_JigETRTcapDcnMiKlE4l
GOOGLE_CALLBACK=http://local.example.com/admin/callback

```
Flow for authentication as below :

User access to `http://local.example.com/admin` => go through `app\Http\Middleware\Admin\AdminAuthenticate.php` => Redirect to G+ => Back to method 
`handleGoogleCallback` in `app\Http\Controller\Backend\AuthController`.

Please note that when we create new Controller in Backend, need to add to `config/permissions.php`.

### Frontend Authenticate.

Garena authenticate.

We can using hardLogin function which located in `app\Garena\Functions.php`.

### Some notes about Coding

1. All frontend method should be located in `app\Http\Controllers\Frontend\MainController.php`.

2. When hard Login We also must comment in `app\Http\Kernel.php`

```
 \App\Http\Middleware\VerifyCsrfToken::class,
```
3. When Create new Controller in Backend or add new method to existed Controller at Backend, must add to 
`config/permissions.php`.

4. When working with databases, prefer to using `DB::` instead of Eloquent.

5. All logical functions for project should be place in `app/Garena/Functions` as static method.

 
