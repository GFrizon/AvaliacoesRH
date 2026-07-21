<?php

return [
    'required' => 'O campo :attribute e obrigatorio.',
    'email' => 'Informe um e-mail valido.',
    'max' => [
        'string' => 'O campo :attribute nao pode ter mais de :max caracteres.',
    ],
    'min' => [
        'string' => 'O campo :attribute deve ter pelo menos :min caracteres.',
    ],
    'unique' => 'Este :attribute ja esta em uso.',

    'attributes' => [
        'name' => 'nome',
        'email' => 'e-mail',
        'phone' => 'telefone',
        'password' => 'senha',
        'role' => 'perfil',
    ],
];
