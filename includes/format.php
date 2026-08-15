<?php

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function money(int|float $amount): string
{
    return '£' . number_format((float) $amount, 0, '.', ',');
}

function date_range(string $start, string $end): string
{
    $from = date_create($start);
    $to   = date_create($end);

    if (!$from || !$to) {
        return '';
    }

    if ($from->format('Y-m') === $to->format('Y-m')) {
        return $from->format('j') . ' – ' . $to->format('j F Y');
    }

    if ($from->format('Y') === $to->format('Y')) {
        return $from->format('j F') . ' – ' . $to->format('j F Y');
    }

    return $from->format('j F Y') . ' – ' . $to->format('j F Y');
}

function date_long(string $date): string
{
    $value = date_create($date);

    return $value ? $value->format('j F Y') : '';
}

function is_date(string $value): bool
{
    $date = DateTime::createFromFormat('Y-m-d', $value);

    return $date !== false && $date->format('Y-m-d') === $value;
}

function invalid(array $errors, string $field): string
{
    return isset($errors[$field]) ? ' is-invalid' : '';
}

function field_error(array $errors, string $field): string
{
    if (!isset($errors[$field])) {
        return '';
    }

    return '<div class="invalid-feedback" id="' . e($field) . '-error">' . e($errors[$field]) . '</div>';
}
