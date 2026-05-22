<?php

namespace App\Filament\Resources\Registrations\Tables;

use App\Models\Registration;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class RegistrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('user.email')
                    ->label('User')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('event.title')
                    ->label('Event')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('fullname')
                    ->label('Full Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('idr')
                    ->sortable(),

                BadgeColumn::make('status')
                    ->colors([
                        'warning'   => 'waiting_approval',
                        'success'   => 'approved',
                        'danger'    => 'rejected',
                    ])
                    ->label('Status'),

                TextColumn::make('created_at')
                    ->label('Registered At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'waiting_approval' => 'Waiting Approval',
                        'approved'         => 'Approved',
                        'rejected'         => 'Rejected',
                    ]),
                Tables\Filters\SelectFilter::make('event_id')
                    ->relationship('event', 'title')
                    ->label('Event'),
            ]);
    }
}
