<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),

                TextInput::make('email')
                    ->required()
                    ->email()
                    ->unique(ignoreRecord: true),

                TextInput::make('no_tlp')
                    ->label('No. Telepon')
                    ->tel(),

                Select::make('role')
                    ->required()
                    ->options([
                        'user' => 'User',
                        'admin' => 'Admin',
                    ]),
            ]);
    }
}
