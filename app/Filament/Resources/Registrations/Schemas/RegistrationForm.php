<?php

namespace App\Filament\Resources\Registrations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class RegistrationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('event_id')
                    ->required()
                    ->numeric(),
                TextInput::make('fullname')
                    ->required(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                Select::make('status')
                    ->options([
            'waiting_payment' => 'Waiting payment',
            'waiting_approval' => 'Waiting approval',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ])
                    ->default('waiting_payment')
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                Textarea::make('admin_note')
                    ->columnSpanFull(),
            ]);
    }
}
