<p align="center">
    <img src="https://user-images.githubusercontent.com/41773797/118710426-2be2b080-b816-11eb-81bc-93296de352f8.png" alt="Package banner" style="width: 100%; max-width: 800px;" />
</p>

<p align="center">
    <a href="https://github.com/radio-js/radio/actions"><img alt="Tests passing" src="https://img.shields.io/badge/Tests-passing-green?style=for-the-badge&logo=github"></a>
    <a href="https://laravel.com"><img alt="Laravel v8.x" src="https://img.shields.io/badge/Laravel-v8.x-FF2D20?style=for-the-badge&logo=laravel"></a>
    <a href="https://github.com/alpinejs/alpine"><img alt="Alpine.js v2.x" src="https://img.shields.io/badge/Alpine.js-v2.x-8bc0d0?style=for-the-badge"></a>
    <a href="https://php.net"><img alt="PHP 8.0" src="https://img.shields.io/badge/PHP-8.0-777BB4?style=for-the-badge&logo=php"></a>
</p>

A component-centric backend communication layer for Alpine.js.

## Installation

Install using the following command:

```bash
composer require radio/radio
```

Install the `@radioScripts` into your Blade template, along with Alpine.js:

```html
<html>
    <head>
        ...
        
        <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    </head>
    
    <body>
        ...
    
        @radioScripts
    </body>
</html>
```

## Usage

1. Create a new component class, and apply the `Radio\Radio` trait. The class may be located anywhere in your codebase, we recommend `App\Http\Components`:
   
```php
<?php

namespace App\Http\Components;

use Radio\Radio;

class MyComponent
{
    use Radio;
    
    // ...
}
```

2. Use the `@radio` Blade directive to connect your Alpine.js component to your Radio PHP class:

```html
<div x-data="@radio(App\Http\Components\MyComponent::class)">
    ...
</div>
```

3. Use your PHP class with Alpine.js!
   
    - Interact with public methods and properties:
      
        ```php
        public $name = '';
      
        public function saveName()
        {
            auth()->user()->update([
                'name' => $this->name,
            ]);
      
            return $this->name;
        }
        ```
        
        ```html
        <input x-model="name" type="text" />
      
        <button @click="await saveName()">Save name</button>
        ```
      
    - Dispatch browser events:
      
        ```php
        use Radio\Concerns\WithEvents;
      
        public function closeUser($userId)
        {
            $this->dispatchEvent('closeUser', [
                'id' => $userId,
            ]);
        }
        ```

   - Render validation errors:

       ```html
       <template x-if="$radio.errors.has('name')">
           <p x-text="$radio.errors.get('name')[0]"></p>
       </template>
       ```

   - Interact with PHP object properties:

       ```php
       // `Collection`s and `EloquentCollection`s are automatically cast using property type hinting. Note: objects within a collection are not cast with it.
       public Collection $users;
     
       // ...as well as `Stringable` objects.
       public Stringable $slug;
     
       // Implement the `Radio\Contracts\Castable` interface on any object for custom DTO support using `fromRadio()` and `toRadio` for hydration and dehydration.
       public CustomObject $dto;
       ```

   - Define computed properties:

       ```php
       #[Computed('getWelcomeMessage')]
       public $welcomeMessage;
      
       public function getWelcomeMessage()
       {
           $name = auth()->user()->name;
     
           return "Welcome {$name}!";
       }
       ```

## Need Help?

🐞 If you spot a bug with this package, please [submit a detailed issue](https://github.com/radio-js/radio/issues/new), and wait for assistance.

🤔 If you have a question or feature request, please [start a new discussion](https://github.com/radio-js/radio/discussions/new).

🔐 If you discover a vulnerability within the package, please review our [security policy](https://github.com/radio-js/radio/blob/main/SECURITY.md).
