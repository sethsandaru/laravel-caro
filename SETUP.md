# Setup Laravel Caro x Seth Phat

## 1. Setup dependencies
Sử dụng PHP 8.2+ và Node 18+

```bash
composer install # first time only
npm ci # first time only
cp .env.example .env # first time only
php artisan key:generate # first time only
```

## 2. Prepare ENVs

### ENVs cơ bản

```php
APP_URL=http://localhost:8000 # quan trọng
SESSION_DOMAIN=.localhost # quan trọng

AUTH_GUARD=api
```

### Database ENVs

Tùy theo các bạn, tôi xài MySQL nên sử dụng MySQL.

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_caro
DB_USERNAME=root
DB_PASSWORD=
```

### (Không quan trọng) Redis

Nếu được, hãy cài Redis và dùng `CACHE_STORE=redis`

### Generate JWT Secret

```bash
php artisan jwt:secret
```

### Acquire Google OAUTH Keys

Vào trang [Console Google](https://console.cloud.google.com/) và tạo 1 project mới.

Sau đó vào [OAuth Consent Screen](https://console.cloud.google.com/apis/credentials/consent) và tạo 1 app testing.

- User Type: External
- Nhớ thêm testing users (Gmail của bạn, ko thì ko login dc đâu).
- Authorized domains: điền tạm 1 domain nào đó, có thể tạo 1 domain từ ngrok cũng được
- Scopes: userinfo.email, userinfo.profile và openid. Chỉ 3 cái này thôi nhé.
- Còn lại cứ next next

Sau đó vào [Credentials](https://console.cloud.google.com/apis/credentials) và tạo **OAuth 2.0 Client ID**. 
Lưu ý, Authorized redirect URIs thì điền vào cái localhost của bạn (e.g.: http://localhost:8000/login-with-google)

Sau khi tạo xong, chúng ta sẽ update 3 cái ENVs này:

```dotenv
GOOGLE_OAUTH_CLIENT_ID=
GOOGLE_OAUTH_CLIENT_SECRET=
GOOGLE_OAUTH_REDIRECT_URI=http://localhost:8000/login-with-google
```

## 3. Migration

```bash
php artisan migrate
```

### 4. Start

```bash
npm run build # build frontend
php artisan serve # localhost:8000 - web server
php artisan reverb:start # localhost:8080 - reverb ws server
```

## 5. Truy cập và sử dụng

Truy cập vào **localhost:8000** và bắt đầu chơi thử.
