<?php

namespace OneToMany\AI\Example\Console\Command;

use OneToMany\AI\Contract\Exception\ExceptionInterface as AIExceptionInterface;
use OneToMany\AI\Provider;
use OneToMany\AI\Resource\File\RemoteFile;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'ai:files:delete',
    description: 'Deletes a file from an AI provider',
)]
final readonly class DeleteFileCommand extends AbstractCommand
{
    public function __invoke(
        SymfonyStyle $io,
        #[Argument('The provider name: "gemini" or "openai".')] string $provider,
        #[Argument('The remote file ID returned by the provider.')] string $fileId,
        #[Option('The provider API key. Defaults to the provider-specific environment variable.')]
        #[\SensitiveParameter]
        ?string $apiKey = null,
    ): int {
        try {
            $provider = Provider::create($provider);
            $file = new RemoteFile($provider, $fileId, 'application/octet-stream');

            $this->factory
                ->create($provider, $this->apiKey($provider, $apiKey))
                ->files
                ->delete($file);
        } catch (AIExceptionInterface $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        $io->table(['Property', 'Value'], [
            ['provider', $provider->getValue()],
            ['fileId', $file->id],
        ]);

        return Command::SUCCESS;
    }
}
