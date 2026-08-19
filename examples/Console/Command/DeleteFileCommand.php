<?php

namespace OneToMany\AI\Example\Console\Command;

use OneToMany\AI\Contract\Exception\ExceptionInterface as AIExceptionInterface;
use OneToMany\AI\Provider;
use OneToMany\AI\Resource\File\RemoteFile;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function is_string;

#[AsCommand(
    name: 'ai:files:delete',
    description: 'Deletes a file from an AI provider',
)]
final class DeleteFileCommand extends AbstractCommand
{
    /**
     * @see Symfony\Component\Console\Command\Command::configure()
     */
    #[\Override]
    protected function configure(): void
    {
        $this
            ->addArgument('provider', InputArgument::REQUIRED, 'The provider name: "gemini" or "openai".')
            ->addArgument('file-id', InputArgument::REQUIRED, 'The remote file ID returned by the provider.');

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
        $fileId = $input->getArgument('file-id');

        if (!is_string($providerName) || !is_string($fileId)) {
            $io->error('The provider and file ID arguments must be strings.');

            return Command::INVALID;
        }

        try {
            $provider = Provider::create($providerName);
            $file = new RemoteFile($provider, $fileId, 'application/octet-stream');

            $this->factory
                ->create($provider, $this->apiKey($input, $provider))
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
