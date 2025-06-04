<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Command as EntityCommand;
use App\Entity\Game;
use App\Enum\GameEnum;
use App\Repository\GameRepository;
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
        private GameRepository $gameRepository,
        private TransformerManager $transformerManager,
        private EntityManagerInterface $em,
        private array $locales,
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->doGames();

        foreach (GameEnum::cases() as $gameEnum) {
            $game = $this->gameRepository->find($gameEnum->value);
            $this->doCommands($game);
        }

        $this->em->flush();

        return self::SUCCESS;
    }

    private function doGames(): void
    {
        $translationRepository = $this->em->getRepository(Translation::class);
        foreach (GameEnum::cases() as $gameEnum) {
            $game = new Game($gameEnum->value);
            $game->setTranslatableLocale($this->locales[0]);

            foreach ($this->locales as $locale) {
                $translationRepository->translate($game, 'name', $locale, \sprintf('%s (%s version)', $gameEnum->getName(), $locale));
                $translationRepository->translate($game, 'description', $locale, \sprintf('%s description (%s)', $gameEnum->getName(), $locale));
            }

            $this->em->persist($game);
        }

        $this->em->flush();
    }

    private function doCommands(Game $game): void
    {
        $translationRepository = $this->em->getRepository(Translation::class);
        foreach ($this->transformerManager->getCommandsFromGame($game->getId()) as $command) {
            $command = new EntityCommand($command, $game);
            $command->setTranslatableLocale($this->locales[0]);

            foreach ($this->locales as $locale) {
                $translationRepository->translate($command, 'description', $locale, \sprintf('%s description (%s)', $command->getId(), $locale));
            }

            $this->em->persist($command);
        }
    }
}
