<?php

Route::get('/pages', 'PagesController@index');

Route::get('/locale/{locale}', function ($locale) {
    request()->session()->put('locale', $locale);
    return redirect('/pages');
});