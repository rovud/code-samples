<?php

namespace Mmd\Account\Controller;

use Epos\UserCore\Service\UserService;
use Mmd\Character\Controller\MyCharactersController;
use Mmd\Guild\Controller\MyGuildsController;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Class DashboardController
 *
 * @package Mmd\Account\Controller
 */
class DashboardController extends AbstractActionController
{

    /**
     * @var UserService
     */
    protected $userService;

    public function indexAction()
    {
        $viewModel = new ViewModel();
        $viewModel->addChild(
            $this->forward()->dispatch(MyCharactersController::class, ['action' => 'recent-list']), 'characters'
        );

        $viewModel->addChild(
            $this->forward()->dispatch(MyGuildsController::class, ['action' => 'recently-added']), 'guilds'
        );

        return $viewModel;
    }
    
    

} 
