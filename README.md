# Authority Builders
<div style="text-align: center;">
    <p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

</div>


[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Travis](https://img.shields.io/travis/deyji/manage.svg?style=flat-square)]()


# Installation

## API Installation

Assuming you have already made a laravel project

1. Clone this repo/branch by running 
    ```bash
    git clone https://git.fligno.com/marketplace/fromager-laravel/laravel-user-management.git -b enhancement/rewrite-vue
     ```
2. In your laravel application composer.json file add this line <br>(where package_location is the path to your package relative to your composer.json file)
    ```json
    "repositories":[{
         "type": "path",
         "url": "<package_location>"   
        }]
    ```
3.  Update your composer by running
	``` 
	composer update 
	```

4.  Install the package
	``` 
	composer require deyji/manage 
	```
5. Run the required migrations by running 
	```bash
	php artisan migrate
	```
6. Install passport by running 
	```bash
	php artisan passport:install 
	```

7. Setup your environment's email provider 

8. Seed the database with default roles by running
	```bash
	php artisan db:seed --class=RoleSeeder
	```
	or you can role your own roles but to save the hassle, just run the default seeds

You can stop here and use the API only but if you want to use this package with vue then proceed below

## Frontend Installation (Optional)

1. Publish the resources by running
	```bash
	php artisan vendor:publish --tag=view --force
	```
	NOTE: only run force if you have a new project or your package.json and webpack.mix.js will be overwritten

2.  Install the front-end dependencies by running
	```bash
	npm install
	```
3. Bundle the front-end after installing the required dependencies by running
	```bash
	npm run dev
	```

# Usage
After installing and setting up the package you can serve it by typing

```bash
php artisan serve
```


# Notes

1. In your `APIRequest.js` replace this line `let cors_api_url = "https://tesbench2.local/"` with your domain, assuming you're using the front-end
	otherwise ignore this note


## Testing

Run the tests with:

```bash
vendor/bin/phpunit
```

##  ToDo

- [x] Custom Authentication, now uses OAuth or Auth0
- [x] Map API, currently supports OSM only
- [x] Login Throttling
- [x] Password Resets
- [x] Custom Email Verification
- [x] Custom Roles
- [x] Organizations
- [x] Social Sign In, Currently Supports Facebook only
- [x] Customizable Admin App Settings
- [x] Timezones
- [x] User Management
- [ ] Coming Soon Email Blast
- [x] Timed Sessions (Client Side)


## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.


## Security

If you discover any security-related issues, please email abgaoe@gmail.com instead of using the issue tracker.


## License

The MIT License (MIT). Please see [License File](/LICENSE.md) for more information.