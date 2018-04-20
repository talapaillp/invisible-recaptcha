Invisible reCAPTCHA
==========
![php-badge](https://img.shields.io/badge/php-%3E%3D%205.6-8892BF.svg)
[![packagist-badge](https://img.shields.io/packagist/v/albertcht/invisible-recaptcha.svg)](https://packagist.org/packages/albertcht/invisible-recaptcha)
[![travis-badge](https://api.travis-ci.org/albertcht/invisible-recaptcha.svg?branch=multi-forms)](https://travis-ci.org/albertcht/invisible-recaptcha)

## Notice
* This branch is for multi-forms purpose.
* In most of cases, there should be only one captcha in your page. You should use master branch normally.
* **Do not use multiple captchas in one page for protecting every form far from bots**, it will cause terrible user experience.

## Installation

```
composer require albertcht/invisible-recaptcha:dev-multi-forms
```

## Usage Example
```php
// you must include `jquery` beforehand
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

{!! Form::open(['url' => '/', 'id' => 'form1']) !!}
@captcha()
{!! Form::submit('Sumbit', ['id'=>'s1']) !!}
{!! Form::close() !!}

{!! Form::open(['url' => '/']) !!}
@captcha()
{!! Form::submit('Sumbit2', ['id'=>'s2']) !!}
{!! Form::close() !!}
```
> **Please include jquery.js manually before you calling the captcha.**

> Just call captcha function in forms directly, it will render only one captcha and all the forms will share the same captcha validation.
> You must add new attribute like `data-submit` an evaluate strings as function. For example, my `data-sumbit` attribute is `signUp(event)`:
```html
<form accept-charset="utf-8" class="ui form" data-submit="signup(event)" autocomplete="off" data-inline="true" data-on="blur" method="POST">
 <!--- All input fields and buttons-->
</form>
```
```javascript
$('form.ui.form').each(function (key, item) {        
     form.on('captcha', function (event) {
        var onSubmit = event.currentTarget.getAttribute('data-submit');
        eval(onSubmit);
        _submitAction = false;
    });
});
```
> Ajax submit function realization
```javascript
function signup(event) {
    event.preventDefault();

    var el = event.currentTarget,
        $form = $(el);

    $.ajax({
        method: 'POST',
        url: '/signup',
        data: $form.serialize(),
        dataType: 'json',
        success: function (data) {
            if (data.success == 'yes') {
               alert("success")
            }
            $form.resetButtons();
        },
        error: function (err, status) {
            console.error(err, status)
        }
    });
}
```
> In this branch, you can cutomize your submit behavior by listening a captcha event.

## Example Repository

Repo: https://github.com/albertcht/invisible-recaptcha-example/tree/multi-forms

This repo demonstrates how to use this package with ajax way.

## Diffs
* There's no `INVISIBLE_RECAPTCHA_DEBUG` config in this branch.
* This package rely on `jquery` instead of `pilyfill.js`, and you have to include `jquery` by yourself before you call the captcha.

### This branch is still under develop, welcome for any deg report or advice.
