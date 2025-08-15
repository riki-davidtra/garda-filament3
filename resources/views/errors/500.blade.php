@extends('errors.layout')

@php
    $title = '500 Internal Server Error';
    $code = '500';
    $icon = '💥';
    $color = 'text-purple-600';
    $message = isset($exception) && $exception->getMessage() ? $exception->getMessage() : 'Terjadi kesalahan pada server.';
@endphp
