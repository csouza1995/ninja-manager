<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Company Information
    |--------------------------------------------------------------------------
    |
    | This file holds all company-specific settings used across the application,
    | such as in receipt templates and layout branding. Set these values in
    | your .env file so they are never committed to version control.
    |
    */

    'name' => env('COMPANY_NAME', 'Minha Empresa LTDA'),

    'document' => env('COMPANY_DOCUMENT', 'XX.XXX.XXX/0001-XX'),

    'address' => env('COMPANY_ADDRESS', 'Rua Exemplo, 123 - Cidade/UF'),

    'email' => env('COMPANY_EMAIL', 'contato@empresa.com'),

    'tagline' => env('APP_TAGLINE', ''),

    'owner_name' => env('COMPANY_OWNER_NAME', 'Nome do Responsável'),

    'owner_document' => env('COMPANY_OWNER_DOCUMENT', 'XXX.XXX.XXX-XX'),

];
