<?php

namespace OneToMany\AI\Command;

use OneToMany\AI\Provider;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

use function is_string;
use function mime_content_type;

#[AsCommand(
    name: 'ai:files:upload',
    description: 'Uploads a file to an AI provider',
)]
final readonly class UploadFileCommand
{
    public function __invoke(
        SymfonyStyle $io,
        #[Argument('The provider name')] Provider $provider,
        #[Argument('The file to upload')] string $path,
    ): int {
        $io->title('Upload File');

        return Command::SUCCESS;
    }
}
