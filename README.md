# Time spent

Thursday: 2 hours

- Project setup and general selection of libraries and structure

Friday: 8 1/2 hours

- Majority of the coding effort and feature additions
  - Login: 2 hours
  - Overview frontend: 3 hours
  - Check in/Check out and editing existing attendances: 2 hours
  - Small bug fixes and additions of various points brought up in the spec: 1 hour
  - Responsiveness: 30 minutes

Saturday: 1 hour

- Had some problems with Laravel CSRF cookies not being handled correctly so spent an hour fixing that then uploaded the project to GitHub.

# AI usage

Mostly help with API usage like how to format a Carbon class in a specific way

```php
$newCheckIn = Carbon::parse($validated['check_in_at'])->format('Y-m-d H:i:s');
```

or help with specific laravel validation rules

```php
'new_password' => ['required', 'min:8'],
'confirm_password' => ['required', 'same:new_password'],
```

and help with new apis I haven't used before like GeoLocation

```js
navigator.geolocation.getCurrentPosition(
```
