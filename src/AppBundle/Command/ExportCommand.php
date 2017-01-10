<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ExportCommand.
 */
class ExportCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:export')
            ->setDescription('Export datas');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $serializer = $this->getContainer()->get('serializer');
        $em         = $this->getContainer()->get('doctrine.orm.entity_manager');
        $projects   = $em->getRepository('AppBundle:Project')->findAll();

        $data = [];
        foreach ($projects as $project) {
            $data[$project->getId()] = [
                'name'        => $project->getName(),
                'branch'      => $project->getBranch(),
                'description' => $project->getDescription(),
                'type'        => $project->getType(),
                'repository'  => $project->getRepository(),
                'is_enabled'  => $project->getIsEnabled(),
                'user'        => [
                    'username'               => $project->getUser()->getUsername(),
                    'email'                  => $project->getUser()->getEmail(),
                    'salt'                   => $project->getUser()->getSalt(),
                    'password'               => $project->getUser()->getPassword(),
                    'locale'                 => $project->getUser()->getLocale() ?: 'en',
                    'github_id'              => $project->getUser()->getGithubId(),
                    'bitbucket_access_token' => $project->getUser()->getGithubAccessToken(),
                ],
                'environements' => [

                ],
                'authentifications' => [

                ],
            ];

            foreach ($project->getEnvironments() as $env) {
                $data[$project->getId()]['environements'][] = [
                    'name'             => $env->getName(),
                    'authentification' => [
                        'name'      => $env->getAuthentification() ? $env->getAuthentification()->getName() : null,
                        'public'    => $env->getAuthentification() ? $env->getAuthentification()->getSshPublic() : null,
                        'private'   => $env->getAuthentification() ? $env->getAuthentification()->getSsh() : null,
                        'type'      => $env->getAuthentification() ? $env->getAuthentification()->getType() : null,
                        'isValid'   => $env->getAuthentification() ? $env->getAuthentification()->getIsValid() : false,
                        'isChecked' => $env->getAuthentification() ? $env->getAuthentification()->getIsChecked() : false,
                    ],
                ];
            }

            foreach ($project->getAuthentificationsRepository() as $auth) {
                $data[$project->getId()]['authentifications'][] = [
                    'name'      => $auth->getName(),
                    'public'    => $auth->getSshPublic(),
                    'private'   => $auth->getSsh(),
                    'type'      => $auth->getType(),
                    'isValid'   => $auth->getIsValid(),
                    'isChecked' => $auth->getIsChecked(),
                ];
            }
        }

        file_put_contents(__DIR__ . '/../../../export.json', $serializer->serialize($data, 'json'));
    }
}
