<?php

namespace App\Models;

use SmyPhp\Core\DatabaseModel;

class User extends DatabaseModel
{

    public function tableName(): string
    {
        return 'users';
    }

    public function uniqueKey(): string 
    {
        return 'id';
    }

    public function rules(): array{
        return [];
    }

    public function attributes(): array{
        return []; 
    }

    public function labels(): array
    {
        return [];
    }

    public function getDisplayName(): string
    {
        return ''; 
    }
}