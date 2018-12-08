<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\NewRelicReporting\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Magento\NewRelicReporting\Model\Apm\DeploymentsFactory;
use Magento\NewRelicReporting\Model\ServiceShellUser;

class DeployMarker extends Command
{
    /**
     * @var DeploymentsFactory
     */
    private $deploymentsFactory;

    /**
     * @var ServiceShellUser
     */
    private $serviceShellUser;

    /**
     * Initialize dependencies.
     *
     * @param DeploymentsFactory $deploymentsFactory
     * @param ServiceShellUser $serviceShellUser
     * @param null $name
     */
    public function __construct(
        DeploymentsFactory $deploymentsFactory,
        ServiceShellUser $serviceShellUser,
        $name = null
    ) {
        $this->deploymentsFactory = $deploymentsFactory;
        $this->serviceShellUser = $serviceShellUser;
        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("newrelic:create:deploy-marker");
        $this->setDescription("Check the deploy queue for entries and create an appropriate deploy marker.")
            ->addArgument(
                'message',
                InputArgument::REQUIRED,
                'Deploy Message?'
            )
            ->addArgument(
<<<<<<< HEAD
                'changelog',
=======
                'change_log',
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
                InputArgument::REQUIRED,
                'Change Log?'
            )
            ->addArgument(
                'user',
                InputArgument::OPTIONAL,
                'Deployment User'
            );
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->deploymentsFactory->create()->setDeployment(
            $input->getArgument('message'),
<<<<<<< HEAD
            $input->getArgument('changelog'),
=======
            $input->getArgument('change_log'),
>>>>>>> 35c4f041925843d91a58c1d4eec651f3013118d3
            $this->serviceShellUser->get($input->getArgument('user'))
        );
        $output->writeln('<info>NewRelic deployment information sent</info>');
    }
}
