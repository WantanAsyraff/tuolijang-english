export const useFocus = () => {
  const isFocus = ref(false);

  const handleFoucs = () => {
    isFocus.value = true;
  };

  const handleBlur = () => {
    isFocus.value = false;
  };

  return {
    isFocus,
    handleFoucs,
    handleBlur
  };
};
