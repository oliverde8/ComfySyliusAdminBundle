<?php
/**
 * @author    oliverde8<oliverde8@gmail.com>
 */
namespace oliverde8\ComfySyliusAdminBundle\Resolver;

use oliverde8\ComfyBundle\Resolver\AbstractScopeResolver;
use oliverde8\ComfyBundle\Resolver\ScopeResolverInterface;
use Sylius\Bundle\ChannelBundle\Doctrine\ORM\ChannelRepository;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class ChannelScopeResolver extends AbstractScopeResolver implements ScopeResolverInterface
{
    protected ChannelRepository $channelRepository;

    protected ChannelContextInterface $channelContext;

    protected LocaleContextInterface $localeContext;

    protected RequestStack $request;

    protected string $defaultScope;

    protected string $defaultScopeName;

    /**
     * ChannelScopeResolver constructor.
     * @param ChannelRepository $channelRepository
     * @param ChannelContextInterface $channelContext
     * @param LocaleContextInterface $localeContext
     * @param RequestStack $request
     * @param string $defaultScope
     * @param string $defaultScopeName
     */
    public function __construct(
        ChannelRepository $channelRepository,
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        RequestStack $request,
        string $defaultScope,
        string $defaultScopeName
    ) {
        $this->channelRepository = $channelRepository;
        $this->channelContext = $channelContext;
        $this->localeContext = $localeContext;
        $this->request = $request;
        $this->defaultScope = $defaultScope;
        $this->defaultScopeName = $defaultScopeName;
    }

    /**
     * @inheritDoc
     */
    public function getCurrentScope(): string
    {
        // For admin return default scope
        $request = $this->request->getCurrentRequest();
        if ($request instanceof Request && $request->get('_route') === 'sylius_admin_comfy_config') {
            return $this->defaultScope;
        }

        $scope = $this->defaultScope
            . '/' . $this->channelContext->getChannel()->getCode()
            . '/' . $this->localeContext->getLocaleCode();

        // If scope not found return default scope
        if (!array_key_exists($scope, $this->getScopes())) {
            return $this->defaultScope;
        }

        return $scope;
    }

    /**
     * @inheritDoc
     */
    protected function initScopes(): array
    {
        // Default scope
        $scopes = [$this->defaultScope => $this->defaultScopeName];

        // Add channel x locale scope
        /** @var Channel $channel */
        foreach ($this->channelRepository->findAll() as $channel) {
            $scopes[$this->defaultScope . '/' .  $channel->getCode()] = $channel->getName();
            foreach ($channel->getLocales() as $locale) {
                $scopes[$this->defaultScope . '/' .  $channel->getCode() . '/' . $locale->getCode()]
                    = $locale->getName();
            }
        }

        return $scopes;
    }
}
