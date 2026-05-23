<?php

namespace App\Filament\Resources\Registrations\Tables;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
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

                ImageColumn::make('foto')
                    ->label('Bukti Bayar')
                    ->getStateUsing(function ($record) {
                        $foto = $record->foto ?: $record->user?->foto;

                        if (!$foto) {
                            return null;
                        }

                        $path = ltrim($foto, '/');

                        if (str_starts_with($path, 'public/')) {
                            $path = substr($path, 7);
                        }

                        return asset('storage/' . $path);
                    })
                    ->height(44)
                    ->square(),

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
