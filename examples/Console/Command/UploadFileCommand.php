<?php

namespace OneToMany\AI\Example\Console\Command;

use OneToMany\AI\Contract\Exception\ExceptionInterface as AIExceptionInterface;
use OneToMany\AI\Provider;
use OneToMany\AI\Resource\File\LocalFile;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

use function is_string;
use function mime_content_type;

#[AsCommand(
    name: 'ai:files:upload',
    description: 'Uploads a file to an AI provider',
)]
final readonly class UploadFileCommand extends AbstractCommand
{
    public function __invoke(
        SymfonyStyle $io,
        #[Argument('The provider name: "gemini" or "openai".')] string $provider,
        #[Argument('The path of the local file to upload.')] string $path,
        #[Option('The provider API key. Defaults to the provider-specific environment variable.')]
        #[\SensitiveParameter]
        ?string $apiKey = null,
    ): int {
        try {
            $provider = Provider::create($provider);
            $apiKey = $this->apiKey($provider, $apiKey);
            $mediaType = @mime_content_type($path);

            if (!is_string($mediaType)) {
                $io->error(sprintf('Detecting the media type of the file "%s" failed.', $path));

                return Command::FAILURE;
            }

            $file = $this->factory
                ->create($provider, $apiKey)
                ->files
                ->upload($provider, new LocalFile($path, $mediaType));
        } catch (AIExceptionInterface $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        $io->table(['Property', 'Value'], [
            ['provider', $file->provider->getValue()],
            ['id', $file->id],
            ['mediaType', $file->mediaType],
            ['uri', $file->uri ?? ''],
            ['expiresAt', $file->expiresAt?->format(\DateTimeInterface::ATOM) ?? ''],
            ['purpose', $file->purpose ?? ''],
        ]);

        return Command::SUCCESS;
    }
}
