<?php

namespace Pagely\AtomicClient\Command\Apps;

use Pagely\AtomicClient\API\AuthApi;
use Pagely\AtomicClient\Command\Command;
use Pagely\AtomicClient\Command\OauthCommandTrait;
use Pagely\AtomicClient\API\Apps\AppsClient;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateAppCommand extends Command
{
    use OauthCommandTrait;

    /**
     * @var AppsClient
     */
    protected $api;

    public function __construct(AuthApi $authApi, AppsClient $apps, $name = 'apps:create')
    {
        $this->authClient = $authApi;
        $this->api = $apps;
        parent::__construct($name);
    }

    public function configure()
    {
        parent::configure();
        $this
            ->setDescription('Create new app')
            ->addArgument('accountId', InputArgument::REQUIRED, 'Account ID')
            ->addArgument('name', InputArgument::REQUIRED, 'App Name/Domain')
            ->addArgument('primaryDomain', InputArgument::REQUIRED, 'Primary Domain')
            ->addOption('multisite', 'm', InputOption::VALUE_REQUIRED, 'Enable multisite type (subdomain or subfolder)')
        ;
        $this->addOauthOptions();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $multi = $input->getOption('multisite');

        $token = $this->token->token;

        $r = $this->api->createApp(
            $token,
            $input->getArgument('accountId'),
            $input->getArgument('name'),
            $input->getArgument('primaryDomain'),
            !!$multi,
            $multi ?: null
        );

        $output->writeln(json_encode(json_decode($r->getBody()->getContents(), true), JSON_PRETTY_PRINT));
        return 0;
    }
}
