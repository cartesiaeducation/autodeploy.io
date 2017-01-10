<?php

namespace AppBundle\Command;

use AppBundle\Entity\Authentification;
use AppBundle\Entity\Environment;
use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

/**
 * Class ImportCommand.
 */
class ImportCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:import')
            ->setDescription('Import datas');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = $this->getContainer()->get('oneup_acl.manager');
        $em      = $this->getContainer()->get('doctrine.orm.entity_manager');

        if (false === $data = file_get_contents(__DIR__ . '/../../../export.json')) {
            throw new \RuntimeException('json file not found');
        }

        $users = [];

        $data = json_decode($data);
        foreach ($data as $item) {
            $output->writeln(sprintf('<info>Import project "%s"</info>', $item->name));

            $project = new Project();
            $project->setName($item->name);
            $project->setBranch($item->branch);
            $project->setDescription($item->description);
            $project->setType($item->type);
            $project->setRepository($item->repository);
            $project->setIsEnabled($item->is_enabled);
            $em->persist($project);
            $em->flush();

            if (empty($users[$item->user->username])) {
                $user = new User();
                $user->setUsername($item->user->username);
                $user->setEmail($item->user->email);
                $user->setSalt($item->user->salt);
                $user->setPassword($item->user->password);
                $user->setLocale($item->user->locale);
                $user->setEnabled(true);
                $user->setExpired(false);

                if (isset($item->user->github_id)) {
                    $user->setGithubId($item->user->github_id);
                    $user->setGithubAccessToken($item->user->bitbucket_access_token);
                }

                $project->setUser($user);
                $em->persist($user);

                $users[$user->getUsername()] = $user;
            } else {
                $user = $users[$item->user->username];
                $project->setUser($user);
            }

            $em->flush();

            $manager->addObjectPermission($project, MaskBuilder::MASK_OWNER, $user);
            $manager->addObjectPermission($project, MaskBuilder::MASK_VIEW, $user);
            $manager->addObjectPermission($project, MaskBuilder::MASK_EDIT, $user);
            $em->flush();

            foreach ($item->authentifications as $auth) {
                $authentification = new Authentification();
                $authentification->setName($auth->name);
                $authentification->setSsh($this->replace($auth->private));
                $authentification->setSshPublic($this->replace($auth->public));
                $authentification->setType($auth->type);
                $authentification->setIsValid($auth->isValid);
                $authentification->setIsChecked($auth->isChecked);
                $authentification->setProject($project);

                $project->addAuthentification($authentification);
                $em->persist($authentification);
            }
            $em->flush();

            foreach ($item->environements as $env) {
                $environment = new Environment();
                $environment->setName($env->name);
                $environment->setProject($project);

                $authentification = new Authentification();
                $authentification->setName($env->authentification->name);
                $authentification->setSsh($this->replace($env->authentification->private));
                $authentification->setSshPublic($this->replace($env->authentification->public));
                $authentification->setType($env->authentification->type);
                $authentification->setIsValid($env->authentification->isValid);
                $authentification->setIsChecked($env->authentification->isChecked);
                $authentification->setProject($project);
                $authentification->setEnvironment($environment);

                $environment->addAuthentification($authentification);
                $project->addAuthentification($authentification);
                $em->persist($authentification);

                $project->addEnvironment($environment);
                $em->persist($environment);
            }

            $em->flush();
        }
    }

    private function replace($text)
    {
        return $text;
    }
}
