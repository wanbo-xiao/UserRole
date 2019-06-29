<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Controller\UserRolesController;

class UserRolesCommand extends Command
{
    private $controller;

    public function __construct(UserRolesController $controller)
    {
        $this->controller = $controller;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('app:demo');
        $this->addArgument('userID', InputArgument::OPTIONAL, 'Test with your own user id');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userId = $input->getArgument('userID');
        if ($userId) {
            $this->controller->getSubOrdinates($userId);
        } else {
            $this->controller->demoAction();
        }

    }
}