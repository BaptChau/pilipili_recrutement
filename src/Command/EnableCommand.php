<?php
namespace App\Command;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EnableCommand extends Command
{
    protected static $defaultName = "product:enabled";
    protected $product;
    protected $em;

    public function __construct(ProductRepository $productRepository, EntityManagerInterface $entityManagerInterface)
    {
        $this->product = $productRepository->findBy(['enabled'=>false]);
        $this->em = $entityManagerInterface;

        parent::__construct();
    }

    public function enable()
    {
        foreach ($this->product as $key => $value) {
            $value->setEnabled(true);
            $this->em->persist($value);
            $this->em->flush();
        }

        

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $output->writeln([
            'Recherche d\'occurences',
            'Occurences trouvé'
        ]);
        $this->enable();

        $output->writeln('Exécution de la méthode enable');

        return Command::SUCCESS;

    }
}
