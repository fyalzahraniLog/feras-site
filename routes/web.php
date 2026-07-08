<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'home')->name('home');

Route::livewire('/log', 'log-index')->name('log.index');
Route::livewire('/log/{slug}', 'log-show')->name('log.show');

Route::livewire('/study', 'study-index')->name('study.index');
Route::livewire('/study/{slug}', 'study-show')->name('study.show');

Route::livewire('/docs', 'docs-index')->name('docs.index');
Route::livewire('/docs/{slug}', 'docs-show')->name('docs.show');
Route::livewire('/docs/{slug}/coach', 'docs-coach')->name('docs.coach');
