@extends('errors.layout')

@php
    $title = '404 Not Found';
    $code = '404';
    $icon = '🔍';
    $color = 'text-red-600';
    $message = isset($exception) && $exception->getMessage() ? $exception->getMessage() : 'Halaman tidak ditemukan.';
@endphp
