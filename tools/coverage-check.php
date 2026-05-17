<?php

declare(strict_types=1);

$cloverFile = $argv[1] ?? 'var/coverage/clover.xml';
$threshold = (float) ($argv[2] ?? 80.0);

if (!is_file($cloverFile)) {
    fwrite(STDERR, "Clover file not found: {$cloverFile}\n");
    exit(2);
}

$xml = simplexml_load_file($cloverFile);
if ($xml === false) {
    fwrite(STDERR, "Failed to parse clover file: {$cloverFile}\n");
    exit(2);
}

$metrics = $xml->project->metrics ?? null;
if ($metrics === null) {
    fwrite(STDERR, "No <metrics> element in clover file\n");
    exit(2);
}

$statements = (int) $metrics['statements'];
$covered = (int) $metrics['coveredstatements'];

if ($statements === 0) {
    fwrite(STDERR, "No statements reported in clover file\n");
    exit(2);
}

$coverage = ($covered / $statements) * 100;
$formatted = number_format($coverage, 2);

if ($coverage < $threshold) {
    fwrite(STDERR, "Coverage {$formatted}% is below threshold {$threshold}%\n");
    exit(1);
}

echo "Coverage {$formatted}% meets threshold {$threshold}%\n";
exit(0);
