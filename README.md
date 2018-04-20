# BIIGLE Annotation Assistance Module

Install the module:

Add the following to the repositories array of your `composer.json`:
```
{
  "type": "vcs",
  "url": "git@github.com:BiodataMiningGroup/biigle-ananas.git"
}
```

1. Run `php composer.phar require biigle/ananas`.
2. Add `'Biigle\Modules\Ananas\AnanasServiceProvider'` to the `providers` array in `config/app.php`.
3. Run `php artisan ananas:publish` to refresh the public assets of this package. Do this for every update of the package.
