<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;

class EditEvent extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected function getBackUrl(): string
    {
        $previousUrl = URL::previous();
        $currentUrl = Request::fullUrl();

        if (blank($previousUrl) || ($previousUrl === $currentUrl)) {
            return static::getResource()::getUrl('index');
        }

        return $previousUrl;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('KEMBALI')
                ->color('gray')
                ->icon(Heroicon::OutlinedArrowLeft)
                ->url(fn () => $this->getBackUrl()),
            DeleteAction::make(),
        ];
    }
}
