# arif/svg-upload-security

Reusable Laravel middleware that sanitizes SVG uploads before controllers run.

## Dependencies

Composer installs **[enshrined/svg-sanitize](https://github.com/darylldoyle/svg-sanitizer)** automatically when you require this package. You do **not** need to add `enshrined/svg-sanitize` to the host app’s `composer.json` unless you call the sanitizer directly elsewhere.

Minimum version: **1.1.1** (declared in this package’s `require`).

## Install (any Laravel app)

```bash
composer require arif/svg-upload-security
```

That single command pulls in `enshrined/svg-sanitize` as a transitive dependency.

Or path repository during development:

```json
"repositories": [{ "type": "path", "url": "packages/svg-upload-security" }],
"require": { "arif/svg-upload-security": "@dev" }
```

Publish config (optional):

```bash
php artisan vendor:publish --tag=svg-upload-security-config
```

## Behavior

- Runs on requests that include uploaded files (nested `file.*` supported).
- Detects SVG by MIME, extension, and `<svg` content sniff.
- **sanitize** (default): replaces the upload with `enshrined/svg-sanitize` output.
- **reject**: fails validation if sanitization would change content.

## Configuration

| Env | Purpose |
|-----|---------|
| `SVG_UPLOAD_SECURITY_ENABLED` | Master switch |
| `SVG_UPLOAD_SECURITY_MODE` | `sanitize` or `reject` |
| `SVG_UPLOAD_SECURITY_GLOBAL_MIDDLEWARE` | Auto-register on HTTP kernel |
| `SVG_UPLOAD_SECURITY_LOG` | Log rejections to configured channel |

## Route-only usage

Set `SVG_UPLOAD_SECURITY_GLOBAL_MIDDLEWARE=false`, then:

```php
Route::post('/upload', UploadController::class)->middleware('sanitize.svg');
```

## Limits

- Does not inspect base64 `data_image` fields — handle those in a shared service if needed.
- Node/Next.js apps need an equivalent middleware/handler (DOMPurify + jsdom per security doc).
