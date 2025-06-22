<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Command as EntityCommand;
use App\Entity\Game;
use App\Entity\Provider;
use App\Enum\GameEnum;
use App\Repository\GameRepository;
use App\Service\Message\Transformer\TransformerManager;
use App\Service\Provider\ProviderManager;
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
        private ProviderManager $providerManager,
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->doGames();
        $this->doProviders();

        $this->em->flush();

        return self::SUCCESS;
    }

    private function doGames(): void
    {
        $translationRepository = $this->em->getRepository(Translation::class);
        foreach (GameEnum::cases() as $gameEnum) {
            if ($this->gameRepository->find($gameEnum)) {
                continue;
            }

            $game = new Game($gameEnum);
            $game->setImg('https://media.tenor.com/w2HShG6EI4oAAAAm/nerd.webp');
            $game->setTranslatableLocale($this->locales[0]);

            foreach ($this->locales as $locale) {
                $translationRepository->translate($game, 'name', $locale, $gameEnum->getName());
                $translationRepository->translate($game, 'description', $locale, \sprintf('%s description (%s)', $gameEnum->getName(), $locale));
            }

            $this->em->persist($game);

            $this->doCommands($game);
        }

        $this->em->flush();
    }

    private function doCommands(Game $game): void
    {
        $translationRepository = $this->em->getRepository(Translation::class);
        foreach ($this->transformerManager->getCommandsFromGame($game->getGame()) as $command) {
            $command = new EntityCommand($game->getId().'_'.$command, $game);
            $command->setTranslatableLocale($this->locales[0]);

            foreach ($this->locales as $locale) {
                $translationRepository->translate($command, 'description', $locale, \sprintf('%s description (%s)', $command->getId(), $locale));
            }

            $this->em->persist($command);
        }
    }

    private function doProviders(): void
    {
        foreach ($this->providerManager->getAll() as $providerName) {
            if ($this->em->getRepository(Provider::class)->find($providerName)) {
                continue;
            }

            $entity = new Provider($providerName);

            $this->em->persist($entity);
        }
    }
}
