<?php

namespace Onepix\FoodSpotVendor\Illuminate\Database\Console\Migrations;

use Illuminate\Console\Command;
use Onepix\FoodSpotVendor\Illuminate\Database\Migrations\MigrationRepositoryInterface;
use Onepix\FoodSpotVendor\Symfony\Component\Console\Attribute\AsCommand;
use Onepix\FoodSpotVendor\Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'migrate:install')]
class InstallCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'migrate:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the migration repository';

    /**
     * The repository instance.
     *
     * @var \Onepix\FoodSpotVendor\Illuminate\Database\Migrations\MigrationRepositoryInterface
     */
    protected $repository;

    /**
     * Create a new migration install command instance.
     *
     * @param  \Onepix\FoodSpotVendor\Illuminate\Database\Migrations\MigrationRepositoryInterface  $repository
     * @return void
     */
    public function __construct(MigrationRepositoryInterface $repository)
    {
        parent::__construct();

        $this->repository = $repository;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->repository->setSource($this->input->getOption('database'));

        $this->repository->createRepository();

        $this->components->info('Migration table created successfully.');
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use'],
        ];
    }
}
