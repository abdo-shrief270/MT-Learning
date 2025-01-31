<?php

namespace App\Filament\Resources\CourseResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MeetingRelationManager extends RelationManager
{
    protected static string $relationship = 'meeting';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(50),
                Forms\Components\DateTimePicker::make('start_time')
                    ->required()
                    ->after(now())
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('start_time')
                    ->since(),
                Tables\Columns\TextColumn::make('url')
                    ->state(fn ($record)=>($record->status=='ongoing'?$record->link:ucfirst($record->type)))
                    ->color(fn ($record) => ($record->status=='pending'?'info':($record->status=='ongoing'?'warning':'success')))
                    ->label('Room URL')
                    ->url(fn ($record) => $record->url, true)
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('status')
                    ->color(fn ($record) => ($record->status=='pending'?'info':($record->status=='ongoing'?'warning':'success'))),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
