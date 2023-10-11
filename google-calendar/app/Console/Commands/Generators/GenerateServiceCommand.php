<?php

namespace App\Console\Commands\Generators;

use Illuminate\Console\Command;

class GenerateServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {serviceName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new service';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        /** @var string $serviceName */
        $serviceName = $this->argument('serviceName');
        /** @var array $partsOfServiceName */
        $partsOfServiceName = explode('/', $serviceName);
        /** @var string $methodName */
        $methodName = count($partsOfServiceName) > 1 ? 'generateWithNamespace' : 'generateWithoutNamespace';

        $this->{$methodName}($partsOfServiceName);
    }

    protected function generateWithNamespace(array $partsOfServiceName): void
    {
        /** @var string $nameOfService */
        $nameOfService = (array_pop($partsOfServiceName));
        /** @var string $serviceNamespace */
        $serviceNamespace = implode('\\', $partsOfServiceName);
        /** @var string $contractName */
        $contractName = $nameOfService . 'Contract';
        /** @var string $contractNamespace */
        $contractNamespace = $serviceNamespace;
        $template = str_replace(
            ['{{serviceNamespace}}', '{{serviceName}}', '{{contractNamespace}}', '{{contractName}}'],
            ['\\' . $serviceNamespace, $nameOfService, '\\' . $contractNamespace . '\\' . $contractName, $contractName],
            $this->getServiceStub()
        );

        if (
            ! file_exists($path = app_path('Services/' . implode('/', $partsOfServiceName)))
            && ! mkdir($path, 0777, true)
            && ! is_dir($path)
        ) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
        }

        file_put_contents($path . "/{$nameOfService}.php", $template);

        $template = str_replace(
            ['{{contractNamespace}}', '{{contractName}}'],
            ['\\' . $contractNamespace, $contractName],
            $this->getContractStub(),
        );

        if (
            ! file_exists($path = app_path('Contracts/' . implode('/', $partsOfServiceName)))
            && ! mkdir($path, 0777, true)
            && ! is_dir($path)
        ) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
        }

        file_put_contents($path . "/{$contractName}.php", $template);
    }

    /**
     * @param array $partsOfServiceName
     */
    protected function generateWithoutNamespace(array $partsOfServiceName): void
    {
        $contractName = $partsOfServiceName[0] . 'Contract';

        $template = str_replace(
            ['{{serviceNamespace}}', '{{serviceName}}', '{{contractNamespace}}', '{{contractName}}'],
            ['', $partsOfServiceName[0], '\\' . $contractName, $contractName],
            $this->getServiceStub()
        );

        file_put_contents(app_path("Services/{$partsOfServiceName[0]}.php"), $template);

        $template = str_replace(
            ['{{contractNamespace}}', '{{contractName}}'],
            ['', $contractName],
            $this->getContractStub(),
        );

        if (
            ! file_exists($path = app_path('Contracts'))
            && ! mkdir($path, 0777, true)
            && ! is_dir($path)
        ) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
        }

        file_put_contents(app_path("Contracts/{$contractName}.php"), $template);
    }

    /**
     * @return string
     */
    protected function getServiceStub(): string
    {
        return file_get_contents(resource_path("stubs/service.stub"));
    }

    /**
     * @return string
     */
    protected function getContractStub(): string
    {
        return file_get_contents(resource_path("stubs/serviceContract.stub"));
    }
}
