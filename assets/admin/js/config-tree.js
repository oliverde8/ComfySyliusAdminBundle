import InfiniteTree from 'infinite-tree';

const NODE_MARGIN = 20;

/**
 * Builds a tree row from the prototype markup, the same way Sylius does for the taxon tree
 * (see @sylius/admin-bundle/taxon-tree).
 */
const renderRow = (prototype, node) => {
  const { id, name, url, active } = node;
  const { depth, open } = node.state;
  const hasChildren = node.hasChildren();
  const rtl = document.querySelector('[dir="rtl"]');

  const item = prototype.cloneNode(true);
  const toggler = item.querySelector('[data-infinite-tree-toggler]');
  const title = item.querySelector('[data-infinite-tree-title]');

  item.setAttribute('data-id', id);
  item.setAttribute('data-expanded', hasChildren && open);
  item.setAttribute('data-depth', depth);
  item.style[rtl ? 'marginRight' : 'marginLeft'] = `${depth * NODE_MARGIN}px`;

  toggler.style.width = `${NODE_MARGIN}px`;
  if (hasChildren) {
    toggler.classList.add(open ? 'infinite-tree-open' : 'infinite-tree-closed');
  } else {
    toggler.classList.add('infinite-tree-leaf');
  }

  title.textContent = name;
  title.setAttribute('title', name);

  if (url) {
    title.setAttribute('href', url);
    title.style.cursor = 'pointer';
    title.classList.add('infinite-tree-link');
  } else {
    title.classList.add('infinite-tree-toggler');
  }

  if (active) {
    title.classList.add('fw-bold');
  }

  return item.outerHTML;
};

const createTree = (wrapper) => {
  const prototype = wrapper.querySelector('[data-comfy-config-tree-prototype]');
  const target = wrapper.querySelector('[data-comfy-config-tree-target]');

  if (!prototype || !prototype.firstElementChild || !target) {
    return;
  }

  const itemPrototype = prototype.firstElementChild;

  new InfiniteTree({
    el: target,
    data: JSON.parse(wrapper.dataset.comfyConfigTreeData || '[]'),
    autoOpen: true,
    selectable: false,
    rowRenderer: (node) => renderRow(itemPrototype, node),
  });
};

document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('[data-comfy-config-tree]').forEach(createTree);
});
