<?php
/**
 * @author    oliverde8<oliverde8@gmail.com>
 */
namespace oliverde8\ComfySyliusAdminBundle\Menu;

use Knp\Menu\FactoryInterface;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdminMenuListener
{
    public function __construct(
        protected FactoryInterface $factory,
        protected TranslatorInterface $translator
    ) {
    }

    /**
     * @param MenuBuilderEvent $event
     */
    public function addAdminMenuItems(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu()->getChild('configuration');

        if (!is_null($menu)) {
            $configMenu = $this->factory->createItem('comfy', ['route' => 'sylius_admin_comfy_config'])
                ->setLabel($this->translator->trans('sylius.ui.comfy.title'))
                ->setLabelAttribute('icon', 'tabler:settings');

            $menu->addChild($configMenu);
        }
    }
}
