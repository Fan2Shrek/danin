<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Command as EntityCommand;
use App\Enum\GameEnum;
use App\Service\Message\Transformer\TransformerManager;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Translatable\Entity\Translation;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:sync:data')]
class SynchronizeDataCommand extends Command
{
    public function __construct(
        private TransformerManager $transformerManager,
        private EntityManagerInterface $em,
        private array $locales,
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach (GameEnum::cases() as $game) {
            $this->doCommands($game->value);
        }

        $this->em->flush();

        return self::SUCCESS;
    }

    private function doCommands(string $game): void
    {
        $translationRepository = $this->em->getRepository(Translation::class);
        foreach ($this->transformerManager->getCommandsFromGame($game) as $command) {
            $command = new EntityCommand($game.'_'.$command);
            $command->setTranslatableLocale($this->locales[0]);

            foreach ($this->locales as $locale) {
                $translationRepository->translate($command, 'description', $locale, \sprintf('%s description (%s)', $command->getId(), $locale));
            }

            $this->em->persist($command);
        }
    }
}
