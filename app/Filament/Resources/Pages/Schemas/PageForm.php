<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('عنوان صفحه')
                    ->required()
                    ->maxLength(255),

                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                TextInput::make('template')
                    ->label('Template')
                    ->required()
                    ->placeholder('wordpress'),

                TextInput::make('seo_title')
                    ->label('عنوان SEO')
                    ->maxLength(255),

                Textarea::make('seo_description')
                    ->label('توضیحات SEO')
                    ->rows(4),

                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'draft' => 'پیش‌نویس',
                        'published' => 'منتشر شده',
                    ])
                    ->default('draft')
                    ->required(),
            ]);
    }
}