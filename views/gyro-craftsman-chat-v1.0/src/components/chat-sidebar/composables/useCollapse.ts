export const useCollapse = () => {
  const isCollapse = ref(false);
  const dropdownVisibleIdxSet = ref(new Set<number>());

  const handleToggleCollapse = () => {
    isCollapse.value = !isCollapse.value;
    if (isCollapse.value) {
      dropdownVisibleIdxSet.value.clear();
    }
  };

  /**
   * 将所有的对话列表中的 dropdown 一次渲染出来会造成卡顿
   * 因此只有鼠标悬浮后才会渲染对应的 dropdown
   */
  const handleCollapseMouseOver = (e: MouseEvent) => {
    const target = e.target;
    if (!(target instanceof HTMLElement)) return;
    let index: number | undefined;
    if (target.dataset.chatIndex !== undefined) {
      index = Number(target.dataset.chatIndex);
    } else {
      const parent = target.closest(".chat-item");
      if (!(parent instanceof HTMLElement)) return;
      if (parent.dataset.chatIndex !== undefined) {
        index = Number(parent.dataset.chatIndex);
      }
    }
    if (index !== undefined) {
      if (!dropdownVisibleIdxSet.value.has(index)) {
        dropdownVisibleIdxSet.value.add(index);
      }
    }
  };

  return {
    isCollapse,
    dropdownVisibleIdxSet,
    handleToggleCollapse,
    handleCollapseMouseOver
  };
};
