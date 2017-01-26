<?php

namespace Mmd\Account\Controller;

use Application\Controller\Helper\RedirectWithMessageTrait;
use Application\Controller\Plugin\FlashRedirect;
use Epos\SocialAuth\Controller\AuthenticationController as SocialAuthController;
use Epos\SocialAuth\Service\ProviderService;
use Epos\UserCore\Controller\AuthenticationController;
use Epos\UserCore\Service\UserService;
use Interop\Container\ContainerInterface;
use Mmd\Account\Form\ChangePasswordForm;
use Mmd\Account\Form\ProfileBaseForm;
use Mmd\Account\InputFilter\ProfileBaseFilter;
use Mmd\Account\Service\Exception\SocialAttachException;
use Mmd\Account\Service\SocialAttachmentService;
use Zend\Http\Request as HttpRequest;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\ArrayUtils;
use Zend\View\Model\ViewModel;

/**
 * Class ProfileController
 *
 * @package Mmd\Account\Controller
 *
 * @method FlashRedirect flashRedirect()
 */
class ProfileController extends AbstractActionController
{

    use RedirectWithMessageTrait;

    /**
     * @var ContainerInterface
     */
    protected $forms;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var SocialAttachmentService
     */
    protected $attachService;

    /**
     * ProfileController constructor.
     *
     * @param ContainerInterface      $forms
     * @param UserService             $userService
     * @param SocialAttachmentService $attachService
     */
    public function __construct(
        ContainerInterface $forms,
        UserService $userService,
        SocialAttachmentService $attachService
    ) {
        $this->forms         = $forms;
        $this->userService   = $userService;
        $this->attachService = $attachService;
    }

    public function indexAction()
    {
        $viewModel = new ViewModel();
        $user      = $this->userService->getAuthenticatedUser();

        /** @var ProfileBaseForm $profileForm */
        $profileForm = $this->forms->get(ProfileBaseForm::class);
        $profileForm->bind($user);

        $changePasswordForm = $this->getChangePasswordForm();

        $viewModel->setVariable('profileForm', $profileForm);
        $viewModel->setVariable('changePasswordForm', $changePasswordForm);
        $viewModel->setVariable('socialProviders', $this->attachService->findAttachedForUser($user));

        return $viewModel;
    }

    public function updateBaseAction()
    {
        if (!$this->request->isPost()) {
            return $this->redirect()->toRoute('profile');
        }

        /** @var ProfileBaseForm $form */
        $form = $this->forms->get(ProfileBaseForm::class);
        $user = $this->userService->getAuthenticatedUser();

        $form->bind($user);
        $data = ArrayUtils::merge($this->params()->fromPost(), [ProfileBaseFilter::EL_ID => $user->getId()]);
        $form->setData($data);

        if (!$form->isValid()) {
            return $this->redirectToRoute('profile', $form->getMessages(), 'danger');
        }

        try {
            $this->userService->update($user);

            return $this->redirectToRoute('profile', 'Данные обновлены', 'success');
        } catch (\Exception $ex) {
            return $this->redirectToRoute(
                'profile', 'Не удалось обновить данные, повторите попытку чуть позднее', 'danger'
            );
        }
    }

    public function changePasswordAction()
    {
        if (!$this->request->isPost()) {
            return $this->redirect()->toRoute('profile');
        }

        $form = $this->getChangePasswordForm();
        $user = $this->userService->getAuthenticatedUser();

        $data = ArrayUtils::merge($this->params()->fromPost(), [ChangePasswordForm::EL_USER_ID => $user->getId()]);
        $form->setData($data);

        if (!$form->isValid()) {
            return $this->redirectToRoute('profile', $form->getMessages(), 'danger');
        }

        try {
            $this->userService->changePassword($user, $form->getPasswordValue());

            $this->forward()->dispatch(AuthenticationController::class, ['action' => 'logout']);

            return $this->redirectToRoute(
                'user.core.login',
                'Пароль изменен. Авторизуйтесь с новым паролем',
                'login-form-success'
            );
        } catch (\Exception $ex) {
            return $this->redirectToRoute(
                'profile', 'Не удалось изменить пароль. Попробуйте повторить попытку позднее', 'danger'
            );
        }
    }

    public function attachSocialAction()
    {
        $providerName = $this->params()->fromRoute('provider');

        if (empty($providerName)) {
            return $this->notFoundAction();
        }

        /** @var HttpRequest $request */
        $request = $this->request;

        if (!$token = $request->getMetadata('authentication_token', false)) {
            $this->flashRedirect()->toRoute('profile');
            $this->flashRedirect()->withForward(__CLASS__, ['action' => 'attachSocial', 'provider' => $providerName]);

            return $this->forward()->dispatch(
                SocialAuthController::class, ['action' => 'request', 'provider' => $providerName]
            );
        }

        $user = $this->userService->getAuthenticatedUser();

        try {
            $this->attachService->attach($user, $providerName, $token);
            $this->flashMessenger()
                 ->setNamespace('success')
                 ->addMessage('Социальный аккаунт успешно привязан');
        } catch (SocialAttachException $e) {
            switch ($e->getCode()) {
                case SocialAttachException::CODE_NOT_AVAILABLE:
                    $this->flashMessenger()
                         ->setNamespace('danger')
                         ->addMessage('Не удалось получить данные социального аккаунта. Повторите попытку позднее');
                    break;
                case SocialAttachException::CODE_NOT_VALID:
                    $this->flashMessenger()->setNamespace('danger');

                    foreach ($e->getValidationResult()->getFlattenMessages() as $message) {
                        $this->flashMessenger()->addMessage($message);
                    }
                    break;
                default:
                    $this->flashMessenger()
                         ->setNamespace('danger')
                         ->addMessage('Произошла ошибка. Повторите попытку позднее');
                    break;
            }
        } catch (\Exception $ex) {
            $this->flashMessenger()
                 ->setNamespace('danger')
                 ->addMessage('Произошла ошибка. Повторите попытку позднее');
        }

        return $this->redirect()->toRoute('profile');
    }

    public function detachSocialAction()
    {
        $providerName = $this->params()->fromRoute('provider');

        if (empty($providerName)) {
            return $this->notFoundAction();
        }

        try {
            $this->attachService->detach($this->userService->getAuthenticatedUser(), $providerName);

            return $this->redirectToRoute(
                'profile', sprintf('Аккаунт отвязан'), 'success'
            );
        } catch (\Exception $ex) {
            return $this->redirectToRoute(
                'profile', sprintf('Не удалось отвязать аккаунт. Повторите поптытку позднее.'), 'danger'
            );
        }
    }

    /**
     * @return ChangePasswordForm
     */
    protected function getChangePasswordForm()
    {
        /** @var ChangePasswordForm $form */
        $form = $this->forms->get(ChangePasswordForm::class);
        $user = $this->userService->getAuthenticatedUser();

        $currentPassword = $user->getPassword();
        if (empty($currentPassword)) {
            $form->remove(ChangePasswordForm::EL_CURRENT_PASSWORD);
            $form->getInputFilter()->remove(ChangePasswordForm::EL_CURRENT_PASSWORD);
        }

        return $form;
    }

}
