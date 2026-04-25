<?php

$dirs = [
    __DIR__ . '/app/Http/Controllers',
    __DIR__ . '/app/Services',
    __DIR__ . '/resources/views',
];

$replacements = [
    'TrainingController' => 'EventController',
    'RegistrationController' => 'ParticipantController',
    'Training' => 'Event',
    'training' => 'event',
    'Trainings' => 'Events',
    'trainings' => 'events',
    'Registration' => 'Participant',
    'registration' => 'participant',
    'Registrations' => 'Participants',
    'registrations' => 'participants',
    'trainings.' => 'events.',
    'training.' => 'event.',
];

function processDir($dir, $replacements) {
    if (!is_dir($dir)) return;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $content = file_get_contents($file->getPathname());
            $newContent = str_replace(array_keys($replacements), array_values($replacements), $content);
            if ($content !== $newContent) {
                file_put_contents($file->getPathname(), $newContent);
                echo "Updated: " . $file->getPathname() . "\n";
            }
        }
    }
}

foreach ($dirs as $dir) {
    processDir($dir, $replacements);
}

// Rename view folders
if (is_dir(__DIR__ . '/resources/views/trainings')) {
    rename(__DIR__ . '/resources/views/trainings', __DIR__ . '/resources/views/events');
    echo "Renamed views/trainings to views/events\n";
}
if (is_dir(__DIR__ . '/resources/views/registrations')) {
    rename(__DIR__ . '/resources/views/registrations', __DIR__ . '/resources/views/participants');
    echo "Renamed views/registrations to views/participants\n";
}
