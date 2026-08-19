<?php

namespace OneToMany\AI\Example\Console\Command;

use OneToMany\AI\Contract\Exception\ExceptionInterface as AIExceptionInterface;
use OneToMany\AI\Model;
use OneToMany\AI\Resource\Query\InputText;
use OneToMany\AI\Resource\Query\JsonSchema;
use OneToMany\AI\Resource\Query\Prompt;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'ai:queries:run',
    description: 'Runs a prompt against an AI model',
)]
final readonly class RunQueryCommand extends AbstractCommand
{
    public function __invoke(
        SymfonyStyle $io,
        #[Argument('The model in "provider:model" format.')] string $model,
        #[Argument('The text prompt to run.')] string $prompt,
        #[Option('The path of a JSON Schema file used to constrain the response.')]
        ?string $jsonSchemaFile = null,
        #[Option('The provider API key. Defaults to the provider-specific environment variable.')]
        #[\SensitiveParameter]
        ?string $apiKey = null,
    ): int {
        try {
            $model = Model::create($model);
            $prompt = Prompt::with(new InputText($prompt));

            if (null !== $jsonSchemaFile) {
                $prompt = $prompt->withSchema(JsonSchema::fromFile(null, $jsonSchemaFile));
            }

            $response = $this->factory
                ->create($model->provider, $this->apiKey($model->provider, $apiKey))
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
            $io->writeln($response->text, OutputInterface::OUTPUT_RAW);

            return Command::SUCCESS;
        }

        $io->warning('The query completed without returning text.');

        return Command::FAILURE;
    }
}
