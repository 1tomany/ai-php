<?php

namespace OneToMany\AI\Example\Console\Command;

use OneToMany\AI\Provider;
use OneToMany\AI\Resource\File\LocalFile;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

use function mime_content_type;

#[AsCommand(
    name: 'ai:files:upload',
    description: 'Uploads a file to an AI provider',
)]
final readonly class UploadFileCommand extends AbstractCommand
{
    public function __invoke(
        SymfonyStyle $io,

        #[Argument('The provider name')]
        Provider $provider,

        #[Argument('The file to upload')]
        string $path,

        #[\SensitiveParameter]
        #[Option('The provider API key')]
        ?string $apiKey = null,
    ): int {
        $io->title('Upload File');

        if (false === $mediaType = @mime_content_type($path)) {
            $mediaType = 'application/octet-stream';
        }

        $file = $this->factory->create($provider, $this->apiKey($provider, $apiKey))->files->upload($provider, new LocalFile($path, $mediaType));

        $io->table(['Provider', 'FileId', 'MediaType', 'URI', 'ExpiresAt', 'Purpose'], [
            [
                $file->provider->getValue(),
                $file->id,
                $file->mediaType,
                $file->uri,
                $file->expiresAt?->format('c'),
                $file->purpose,
            ],
        ]);

        return Command::SUCCESS;
    }
}
