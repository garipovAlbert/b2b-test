Тестовое задание
---------------
"Тест Backend:
Реализовать АПИ для приложения “Каталог рецептов”, разрешается использовать микрофрэймворки 
(psr7 middleware,Lumen, Silex, Slim), 
АПИ желательно должно соответствовать РЕСТ стандартам, за исключением некоторых методов которые можно реализовать в виде RPC.
АПИ должно давать возможность создать юзера, залогиниться, создавать, редактировать и удалять от лица этого юзера рецепты, 
так же обязательным полем у рецепта является фотография, следовательно нужно АПИ для загрузки фотографий. 
Для реализации самого АПИ и сопутствующего ему функционала запрещается использовать библиотеки.
База данных PostgreSql
Версия PHP > 7
Итоговый вариант АПИ ожидается на гитхабе."

Установка
---------------
- Создайте базу данных из файла /b2b-test.dump в PostgreSQL
- Установите параметры подключения к базе данных в файле /application/config/app.php
```php
    'db' => [
        'dsn' => 'pgsql:dbname=НАЗВАНИЕ_БАЗЫ;host=localhost',
        'username' => '...',
        'password' => '...',
    ],
```
- Установите права на запись в папку /application/web/images
- Укажите папку /application/web как DocumentRoot в конфигурации (виртуального хоста) Apache

Тестовое задание выполнено с использованием
- Apache2 (+mod_rewrite)
- PHP 7.0
- PostgreSQL 10



Объекты
-------------
#### RegisterForm - форма регистрации 
Содержит поля:  
- login    
- password  
- id - уникальный ID пользователя  

Принимает: login, password  
Выводит: id  

#### LoginForm
Содержит поля:  
- login - уникальный логин пользователя  
- password - пароль  
- id - уникальный ID пользователя  
- accessKey - ключ, используемый в аутентификации (bearer)  

Принимает: login, password  
Выводит: accessKey, id  

#### Account
Содержит поля:
- id - уникальный ID пользователя  
- login - уникальный логин пользователя  

Выводит: id, login

#### Recipe
Содержит поля:  
- id - уникальный ID рецепта  
- account_id - уникальный ID аккаунта пользователя, создавшего рецепт  
- name - название рецепта  
- description - описание рецепта  
- image_url - путь к файлу с изображением, например "/images/ahvc2pts.jpg"  

Принимает: name, description  
Выводит: id, name, account_id, description, image_url  

#### Validation
Содержит ошибки валидации, возвращается с HTTP-статусом 422.  
Пример:
```json
{
    "errors": [
        {
            "field": "login",
            "error": "User 'My Username' already exists."
        }
    ]
}
```
#### HttpException
Пример:
```json
{
    "status": 404,
    "error": "Not Found"
}
```

Функции
-------------
#### POST /register
Регистрирует нового пользователя. Принимает и возвращает объект RegisterForm.    
Выводит ошибки валидации если
- пользователь с таким логином уже существует
- пустой логин или пароль
- неправильная длина строк логина (3-16) и пароля (6-32)

Пример запроса:
```php
/* POST /register */
(function() use($siteUrl) {
    $url = 'http://' . $siteUrl . '/register';

    $data = http_build_query([
        'login' => 'My Username',
        'password' => 'My Password',
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    var_dump($code); // HTTP code
    var_dump($response); // content
})();
```
Пример ответа:  
(HTTP-статус: 200)
```json
{
    "id": "8"
}
```

#### POST /login
Принимает и возвращает объект LoginForm, содержащий accessKey, который в дальнейшем используется для аутентификации пользователя в функциях управления рецептами.
Выводит ошибку валидации, если неправильный логин/пароль.  

Пример запроса:
```php
/* POST /login */
(function() use($siteUrl) {
    $url = 'http://' . $siteUrl . '/login';

    $data = http_build_query([
        'login' => 'My Username',
        'password' => 'My Password',
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    var_dump($code); // HTTP code
    var_dump($response); // content
})();
```

Пример ответа:  
(HTTP-статус: 200)
```json
{
    "id": 5,
    "accessKey": "oqdc93i6avj1sp0hyewmlzr2478knx5g"
}
```

#### POST /new-recipe
Создает рецепт. Принимает и возвращает объект Recipe. Требует авторизации.
Принимает файл в поле imageFile при отправке файла в запросе с заголовком "Content-Type: multipart/form-data".
Выводит ошибки валидации если  
- неправильная длина строк названия (1-32) и описания (0-255)
- неправильный формат файла изображения

Пример запроса:
($accessKey - ключ, полученный в результате выполнения функции "POST /login", $imageFilePath - путь к файлу на локальном диске)
```php
/* POST /new-recipe */
(function() use($siteUrl, $accessKey, $imageFilePath) {
    $url = 'http://' . $siteUrl . '/new-recipe';

    $data = [
        'name' => 'My recipe',
        'description' => 'My recipe description',
        'imageFile' => new CURLFile($imageFilePath, 'image/jpeg'),
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: multipart/form-data']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $accessKey));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    var_dump($code); // HTTP code
    var_dump($response); // content
})();
```


Пример ответа:  
(HTTP-статус: 201)
```json
{
    "id": "20",
    "name": "My recipe",
    "account_id": 1,
    "description": "My recipe description",
    "image_url": "\/images\/14nrxcgb.jpg"
}
```

#### GET /recipe/:id
Возвращает объект Recipe по его ID.
Пример запроса:
```php
/* GET /recipe/:id */
(function() use($siteUrl, $accessKey) {
    $url = 'http://' . $siteUrl . '/recipe/19';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $accessKey));
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    var_dump($code); // HTTP code
    var_dump($response); // content
})();
```

Пример ответа:  
(HTTP-статус: 200)
```json
{
    "id": 19,
    "name": "My recipe",
    "account_id": 1,
    "description": "My recipe description",
    "image_url": "\/images\/vjq8p905.jpg"
}
```

#### PATCH /recipe/:id
Редактирует текстовые поля рецепта. Принимает и отдает объект Recipe. 
Пример запроса:
```php
/* PATCH /recipe/:id */
(function() use($siteUrl, $accessKey) {
    $url = 'http://' . $siteUrl . '/recipe/19';

    $data = http_build_query([
        'name' => 'My recipe 123',
        'description' => 'My recipe description 123',
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $accessKey));
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    var_dump($code); // HTTP code
    var_dump($response); // content
})();
```

Пример ответа:  
(HTTP-статус: 200)
```json
{
    "id": 19,
    "name": "My recipe 123",
    "account_id": 1,
    "description": "My recipe description 123",
    "image_url": "\/images\/vjq8p905.jpg"
}
```

#### DELETE /recipe/:id
Удаляет рецепт. Отдает удаленный объект Recipe. 
Пример запроса:
```php
/* DELETE /recipe/:id */
(function() use($siteUrl, $accessKey) {
    $url = 'http://' . $siteUrl . '/recipe/19';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $accessKey));
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    var_dump($code); // HTTP code
    var_dump($response); // content
})();
```

Пример ответа:  
(HTTP-статус: 200)
```json
{
    "id": 19,
    "name": "My recipe 123",
    "account_id": 1,
    "description": "My recipe description 123",
    "image_url": "\/images\/vjq8p905.jpg"
}
```
