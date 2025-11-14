<?php
namespace App\Support;

final class Time
{
    public static function ago(?string $datetime): string
    {
        if (!$datetime) {
            return '';
        }
        $ts = is_numeric($datetime) ? (int)$datetime : strtotime($datetime);
        if ($ts === false) {
            return '';
        }
        $diff = time() - $ts;
        if ($diff < 60) {
            return 'Just now';
        }
        $mins = (int) floor($diff / 60);
        if ($mins < 60) {
            return $mins . ' minute' . ($mins === 1 ? '' : 's') . ' ago';
        }
        $hours = (int) floor($mins / 60);
        if ($hours < 24) {
            return $hours . ' hour' . ($hours === 1 ? '' : 's') . ' ago';
        }
        return date('M j, Y', $ts);
    }
}

