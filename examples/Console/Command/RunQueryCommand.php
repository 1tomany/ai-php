<?php

namespace OneToMany\AI\Example\Console\Command;

use OneToMany\AI\Contract\Exception\ExceptionInterface as AIExceptionInterface;
use OneToMany\AI\Model;
use OneToMany\AI\Resource\Query\InputText;
use OneToMany\AI\Resource\Query\JsonSchema;
use OneToMany\AI\Resource\Query\Prompt;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function is_string;

#[AsCommand(
    name: 'ai:queries:run',
    description: 'Runs a prompt against an AI model.',
)]
final class RunQueryCommand extends AbstractAICommand
{
    /**
     * @see Symfony\Component\Console\Command\Command::configure()
     */
    #[\Override]
    protected function configure(): void
    {
        $this
            ->addArgument('model', InputArgument::REQUIRED, 'The model in "provider:model" format.')
            ->addArgument('prompt', InputArgument::REQUIRED, 'The text prompt to run.')
            ->addOption(
                'json-schema-file',
                null,
                InputOption::VALUE_REQUIRED,
                'The path of a JSON Schema file used to constrain the response.',
            );

        $this->addApiKeyOption();
    }

    /**
     * @see Symfony\Component\Console\Command\Command::execute()
     */
    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $modelName = $input->getArgument('model');
        $promptText = $input->getArgument('prompt');
        $schemaFile = $input->getOption('json-schema-file');

        if (!is_string($modelName) || !is_string($promptText)) {
            $io->error('The model and prompt arguments must be strings.');

            return Command::INVALID;
        }

        try {
            $model = Model::create($modelName);
            $prompt = Prompt::with(new InputText($promptText));

            if (is_string($schemaFile)) {
                $prompt = $prompt->withSchema(JsonSchema::fromFile(null, $schemaFile));
            }

            $response = $this->factory
                ->create($model->provider, $this->apiKey($input, $model->provider))
                ->queries
                ->compileAndRun($model, $prompt);
        } catch (AIExceptionInterface $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        $io->table(['Property', 'Value'], [
            ['provider', $response->provider->getValue()],
            ['model', $model->name],
            ['id', $response->id],
        ]);

        if (null !== $response->error) {
            $io->error($response->error);

            return Command::FAILURE;
        }

        if (null !== $response->refusal) {
            $io->warning($response->refusal);

            return Command::FAILURE;
        }

        if (null !== $response->text) {
            $output->writeln($response->text, OutputInterface::OUTPUT_RAW);

            return Command::SUCCESS;
        }

        $io->warning('The query completed without returning text.');

        return Command::FAILURE;
    }
}
