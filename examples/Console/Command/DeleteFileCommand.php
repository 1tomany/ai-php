<?php

namespace OneToMany\AI\Example\Console\Command;

use OneToMany\AI\Contract\Exception\ExceptionInterface;
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

        #[Argument('The provider name')]
        Provider $provider,

        #[Argument('The remote file ID')]
        string $fileId,

        #[\SensitiveParameter]
        #[Option('The provider API key')]
        ?string $apiKey = null,
    ): int {
        $io->title('Delete File');

        $file = new RemoteFile($provider, $fileId);

        $this->factory->create($provider, $this->apiKey($provider, $apiKey))->files->delete($file);

        $io->table(['Provider', 'FileId'], [
            [
                $provider->getValue(),
                $file->id,
            ],
        ]);

        return Command::SUCCESS;
    }
}
