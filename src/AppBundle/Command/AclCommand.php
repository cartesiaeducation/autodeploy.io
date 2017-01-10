<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

class AclCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:acl:init')
            ->setDescription('Init ACL');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $aclManager = $this->getContainer()->get('oneup_acl.manager');
        $em         = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $projects   = $em->getRepository('AppBundle:Project')->findAll();

        foreach ($projects as $project) {
            $aclManager->revokeAllObjectPermissions($project);

            $aclManager->addObjectPermission($project, MaskBuilder::MASK_OWNER, $project->getUser());
            $aclManager->addObjectPermission($project, MaskBuilder::MASK_VIEW, $project->getUser());
            $aclManager->addObjectPermission($project, MaskBuilder::MASK_EDIT, $project->getUser());

            foreach ($project->getCollaborators() as $collaborator) {
                $aclManager->addObjectPermission($project, MaskBuilder::MASK_VIEW, $collaborator->getUser());
                $aclManager->addObjectPermission($project, MaskBuilder::MASK_EDIT, $collaborator->getUser());
            }
        }
    }
}
