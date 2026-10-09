<?php
/**
 * @author    oliverde8<oliverde8@gmail.com>
 */
namespace oliverde8\ComfySyliusAdminBundle\Controller;

use oliverde8\ComfyBundle\Form\Type\ConfigsForm;
use oliverde8\ComfyBundle\Manager\ConfigDisplayManager;
use oliverde8\ComfyBundle\Resolver\ScopeResolverInterface;
use oliverde8\ComfyBundle\Resolver\VisibleConfigsResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConfigController extends AbstractController
{
    public function __construct(
        protected VisibleConfigsResolver $visibleConfigsResolver,
        protected ScopeResolverInterface $scopeResolver,
        protected ConfigDisplayManager $configDisplayManager,
        protected TranslatorInterface $translator
    ) {
    }

    #[Route('/comfy/configs', name: 'sylius_admin_comfy_config')]
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
        if ($form->isSubmitted() && $form->isValid()) {
            // We need to recreate the form because config won't take their inheritance properly
            // into account untill all of them are saved.
            $form = $this->createForm(ConfigsForm::class, ['scope' => $scope, 'configs' => $configs]);
            $this->addFlash('success', $this->translator->trans('sylius.ui.comfy.success_message'));
            return $this->redirect($request->getRequestUri());
        }

        $configTree = $this->visibleConfigsResolver->getAllAllowedConfigs();

        return $this->render(
            "@oliverde8ComfySyliusAdmin/config/index.html.twig",
            [
                'form' => $form->createView(),
                'config_path' => $configPath,
                'config_keys' => $this->getConfigKeys($configs),
                'config_tree_data' => $this->getConfigTreeData($configTree, $scope, $configPath),
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
     */
    protected function getConfigPathFromRequest(Request $request): string
    {
        $configPath = $request->query->get('config', null);
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
        $scope = $this->scopeResolver->getScope($request->query->get("scope", null));

        if (!$this->scopeResolver->validateScope($scope)) {
            throw new NotFoundHttpException("Unknown scope.");
        }

        return $scope;
    }

    /**
     * Flatten the config tree into the node structure expected by InfiniteTree.
     *
     * @param array $configTree
     * @param string $scope
     * @param string $currentPath
     * @param string $parent
     *
     * @return array
     */
    protected function getConfigTreeData(
        array $configTree,
        string $scope,
        string $currentPath,
        string $parent = ''
    ): array {
        $nodes = [];
        $currentDottedPath = str_replace('/', '.', $currentPath);

        foreach ($configTree as $configKey => $children) {
            if (!is_array($children)) {
                continue;
            }

            $path = $parent . '.' . $configKey;
            $dottedPath = ltrim($path, '.');
            $childNodes = $this->getConfigTreeData($children, $scope, $currentPath, $path);

            $nodes[] = [
                'id' => $dottedPath,
                'name' => $this->translator->trans('comfy' . $path . '.name'),
                'url' => [] === $childNodes
                    ? $this->generateUrl('sylius_admin_comfy_config', ['scope' => $scope, 'config' => $path])
                    : null,
                'active' => $dottedPath === $currentDottedPath,
                'children' => $childNodes,
            ];
        }

        return $nodes;
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
