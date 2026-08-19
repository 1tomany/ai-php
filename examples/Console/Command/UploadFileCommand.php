<?php

namespace OneToMany\AI\Example\Console\Command;

use OneToMany\AI\Contract\Exception\ExceptionInterface as AIExceptionInterface;
use OneToMany\AI\Provider;
use OneToMany\AI\Resource\File\LocalFile;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function is_string;
use function mime_content_type;

#[AsCommand(
    name: 'ai:files:upload',
    description: 'Uploads a local file to an AI provider.',
)]
final class UploadFileCommand extends AbstractAICommand
{
    /**
     * @see Symfony\Component\Console\Command\Command::configure()
     */
    #[\Override]
    protected function configure(): void
    {
        $this
            ->addArgument('provider', InputArgument::REQUIRED, 'The provider name: "gemini" or "openai".')
            ->addArgument('path', InputArgument::REQUIRED, 'The path of the local file to upload.');

        $this->addApiKeyOption();
    }

    /**
     * @see Symfony\Component\Console\Command\Command::execute()
     */
    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $providerName = $input->getArgument('provider');
        $path = $input->getArgument('path');

        if (!is_string($providerName) || !is_string($path)) {
            $io->error('The provider and path arguments must be strings.');

            return Command::INVALID;
        }

        try {
            $provider = Provider::create($providerName);
            $apiKey = $this->apiKey($input, $provider);
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
