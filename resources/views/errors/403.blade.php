@extends('errors.layout')

@php
    $title = '403 Forbidden';
    $code = '403';
    $icon = '⛔';
    $color = 'text-yellow-500';
    $message = isset($exception) && $exception->getMessage() ? $exception->getMessage() : 'Anda tidak memiliki izin untuk mengakses halaman ini.';
@endphp
