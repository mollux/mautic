<?php

namespace MauticPlugin\MauticSocialBundle\Controller;

use Mautic\CoreBundle\Controller\FormController;
use Symfony\Component\Form\Form;

/**
 * Class TweetController.
 */
class TweetController extends FormController
{
    /**
     * @return mixed
     */
    protected function getModelName()
    {
        return 'social.tweet';
    }

    /**
     * @return mixed
     */
    protected function getJsLoadMethodPrefix()
    {
        return 'socialTweet';
    }

    /**
     * @return mixed
     */
    protected function getRouteBase()
    {
        return 'mautic_tweet';
    }

    /**
     * @param null $objectId
     *
     * @return mixed
     */
    protected function getSessionBase($objectId = null)
    {
        return 'mautic_tweet';
    }

    /**
     * @return mixed
     */
    protected function getTemplateBase()
    {
        return 'MauticSocialBundle:Tweet';
    }

    /**
     * @return mixed
     */
    protected function getTranslationBase()
    {
        return 'mautic.integration.Twitter';
    }

    /**
     * @return mixed
     */
    protected function getPermissionBase()
    {
        return 'mauticSocial:tweets';
    }

    /**
     * Define options to pass to the form when it's being created.
     *
     * @return array
     */
    protected function getEntityFormOptions()
    {
        return [
            'update_select'      => $this->getUpdateSelect(),
            'allow_extra_fields' => true,
        ];
    }

    /**
     * Get updateSelect value from request.
     */
    public function getUpdateSelect(): string|bool
    {
        return ('POST' == $this->request->getMethod())
            ? $this->request->request->get('twitter_tweet[updateSelect]', false)
            : $this->request->get('updateSelect', false);
    }

    /**
     * Set custom form themes, etc.
     *
     * @param string $action
     *
     * @return \Symfony\Component\Form\FormView
     */
    protected function getFormView(Form $form, $action)
    {
        return $this->setFormTheme($form, 'MauticSocialBundle:Tweet:form.html.php', ['MauticSocialBundle:FormTheme']);
    }

    /**
     * @param int $page
     */
    public function indexAction($page = 1): \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Response
    {
        return parent::indexStandard($page);
    }

    /**
     * Generates new form and processes post data.
     */
    public function newAction(): \Symfony\Component\HttpFoundation\JsonResponse|\Response
    {
        return parent::newStandard();
    }

    /**
     * Get the template file.
     *
     * @param $file
     *
     * @return string
     */
    protected function getTemplateName($file, string $engine = self::ENGINE_PHP)
    {
        if ('form.html.php' === $file && 1 == $this->request->get('modal')) {
            return parent::getTemplateName('form.modal.html.php');
        }

        return parent::getTemplateName($file);
    }

    /**
     * Generates edit form and processes post data.
     *
     * @param int  $objectId
     * @param bool $ignorePost
     */
    public function editAction($objectId, $ignorePost = false): \Symfony\Component\HttpFoundation\JsonResponse|\Response
    {
        return parent::editStandard($objectId, $ignorePost);
    }

    /**
     * Displays details.
     *
     * @param $objectId
     */
    public function viewAction($objectId): array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Response
    {
        return parent::indexStandard(1);
    }

    /**
     * Clone an entity.
     *
     * @param int $objectId
     */
    public function cloneAction($objectId): \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Response
    {
        return parent::cloneStandard($objectId);
    }

    /**
     * Deletes the entity.
     *
     * @param int $objectId
     */
    public function deleteAction($objectId): \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
    {
        return parent::deleteStandard($objectId);
    }

    /**
     * Deletes a group of entities.
     */
    public function batchDeleteAction(): \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
    {
        return parent::batchDeleteStandard();
    }
}
