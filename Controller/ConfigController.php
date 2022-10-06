<?php
/**
 * @author    oliverde8<oliverde8@gmail.com>
 */
namespace oliverde8\ComfySyliusAdminBundle\Controller;

use oliverde8\ComfyBundle\Exception\UnknownScopeException;
use oliverde8\ComfyBundle\Form\Type\ConfigsForm;
use oliverde8\ComfyBundle\Manager\ConfigDisplayManager;
use oliverde8\ComfyBundle\Resolver\ScopeResolverInterface;
use oliverde8\ComfyBundle\Resolver\VisibleConfigsResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ConfigController extends AbstractController
{
    protected VisibleConfigsResolver $visibleConfigsResolver;
    protected ScopeResolverInterface $scopeResolver;
    protected ConfigDisplayManager $configDisplayManager;

    /**
     * ConfigController constructor.
     *
     * @param VisibleConfigsResolver $visibleConfigsResolver
     * @param ScopeResolverInterface $scopeResolver
     * @param ConfigDisplayManager $configDisplayManager
     */
    public function __construct(
        VisibleConfigsResolver $visibleConfigsResolver,
        ScopeResolverInterface $scopeResolver,
        ConfigDisplayManager $configDisplayManager
    )
    {
        $this->visibleConfigsResolver = $visibleConfigsResolver;
        $this->scopeResolver = $scopeResolver;
        $this->configDisplayManager = $configDisplayManager;
    }

    /**
     * @Route("/comfy/configs", name="sylius_admin_comfy_config")
     */
    public function index(Request $request): Response
    {
        $scope = $this->getConfigScopeFromRequest($request);
        $configPath = $this->getConfigPathFromRequest($request);
        $configs = $this->visibleConfigsResolver->getAllowedConfigs($configPath);

        if (empty($configs)) {
            throw new NotFoundHttpException("Unknown config path.");
        }

        $form = $this->createForm(ConfigsForm::class, ['scope' => $scope, 'configs' => $configs]);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // We need to recreate the form because config won't take their inheritance properly
                // into account untill all of them are saved.
                $form = $this->createForm(ConfigsForm::class, ['scope' => $scope, 'configs' => $configs]);
                return $this->redirect($request->getRequestUri());
            }
        }

        return $this->render(
            "@oliverde8ComfySyliusAdmin/Config/index.html.twig",
            [
                'form' => $form->createView(),
                'config_path' => $configPath,
                'config_keys' => $this->getConfigKeys($configs),
                'config_tree' => $this->visibleConfigsResolver->getAllAllowedConfigs(),
                'scope' => $scope,
                'scopes' => $this->configDisplayManager->getScopeTreeForHtml(),
            ]
        );
    }

    /**
     * Get the config path to use.
     *
     * @param Request $request
     * @return string
     *
     * @throws UnknownScopeException
     */
    protected function getConfigPathFromRequest(Request $request): string
    {
        $configPath = $request->get('config', null);
        $configPath = str_replace(".", "/", $configPath);
        $configPath = ltrim($configPath, '/');

        if (empty($configPath)) {
            $configPath = $this->configDisplayManager->getFirstConfigPath() ?: '';
            $configPath = ltrim($configPath, '/');
        }

        return $configPath;
    }

    /**
     * Get the scope we are editing the configs for.
     *
     * @param Request $request
     * @return string
     *
     * @throws NotFoundHttpException
     */
    protected function getConfigScopeFromRequest(Request $request): string
    {
        $scope = $this->scopeResolver->getScope($request->get("scope", null));

        if (!$this->scopeResolver->validateScope($scope)) {
            throw new NotFoundHttpException("Unknown scope.");
        }

        return $scope;
    }

    /**
     * @param array $configs
     * @return array
     */
    protected function getConfigKeys(array $configs): array
    {
        $configKeys = [];

        foreach ($configs as $config) {
            $configKeys[] = $this->configDisplayManager->getConfigHtmlName($config);
        }

        return $configKeys;
    }
}
