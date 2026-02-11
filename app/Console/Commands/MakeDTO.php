<?php

namespace App\Console\Commands;

class MakeDTO extends BaseFileGeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:dto {name : The name of the DTO class}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new DTO class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        [$className, $folderPath] = $this->normalizeName($this->argument('name'));

        $directory = app_path($folderPath);
        $filePath  = $directory . '/' . $className . '.php';

        if (!$this->createDirectoryAndFile($directory, $filePath)) {
            return;
        }

        $namespace = 'App' . ($folderPath ? '\\' . str_replace('/', '\\', $folderPath) : '');
        $this->buildFileFromStub(
            __DIR__ . '/stubs/dto.stub',
            $filePath,
            $namespace,
            $className,
        );

        $this->info("DTO {$className} created successfully");
    }
}
