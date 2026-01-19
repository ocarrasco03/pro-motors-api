<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

abstract class BaseFileGeneratorCommand extends Command
{
    protected function normalizeName(string $name): array
    {
        $normalized = str_replace('\\', '/', $name);
        $parts = explode('/', $normalized);
        $class = array_pop($parts);
        $path = implode('/', $parts);

        return [$class, $path, $normalized];
    }

    protected function buildFileFromStub(
        string $stubPath,
        string $outputPath,
        string $namespace,
        string $className,
        array $replacements = []
    ): void {
        $stub = file_get_contents($stubPath);
        $stub = str_replace(['{{ namespace }}', '{{ class }}'], [$namespace, $className], $stub);

        foreach ($replacements as $key => $value) {
            $stub = str_replace('{{ ' . $key . ' }}', $value, $stub);
        }

        file_put_contents($outputPath, $stub);
    }

    protected function generateTest(
        string $folderPath,
        string $className,
        string $stubFile,
        string $baseNamespace,
        string $subDir = ''
    ): void {
        $testDir = base_path('tests/Unit/' . $folderPath);
        $testPath = $testDir . '/' . $className . 'Test.php';

        if (!is_dir($testDir)) {
            mkdir($testDir, 0755, true);
        }

        $fullClass = 'App\\' . $baseNamespace .
            ($folderPath ? '\\' . str_replace('/', '\\', $folderPath) : '') .
            ($subDir ? '\\' . $subDir : '') . '\\' . $className;

        $stub = file_get_contents(__DIR__ . '/stubs/tests/' . $stubFile);
        $stub = str_replace('{{ fullClass }}', $fullClass, $stub);

        file_put_contents($testPath, $stub);
        $this->info("Pest test for {$className} created successfully");
    }

    protected function createDirectoryAndFile(string $directory, string $filePath): bool
    {
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        if (file_exists($filePath)) {
            $this->error("File already exists at: {$filePath}");
            return false;
        }

        return true;
    }
}
